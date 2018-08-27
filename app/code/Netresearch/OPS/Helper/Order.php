<?php

namespace Netresearch\OPS\Helper;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Netresearch\OPS\Model\Payment\PaymentAbstract;

/**
 * @package
 * @copyright 2013 Netresearch
 * @author    Michael Lühr <michael.luehr@netresearch.de>
 * @license   OSL 3.0
 */
class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DELIMITER = '#';

    /** @var \Netresearch\OPS\Model\Config $config */
    private $config;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    private $oPSHelper;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Netresearch\OPS\Helper\Payment;
     */
    private $oPSPaymentHelper;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilderFactory;
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var \Netresearch\OPS\Helper\Data | null
     */
    private $dataHelper = null;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Helper\Payment $paymentHelper,
        \Netresearch\OPS\Model\Config $config,
        \Magento\Quote\Model\QuoteFactory $quoteQuoteFactory,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        parent::__construct($context);
        $this->oPSHelper = $oPSHelper;
        $this->oPSPaymentHelper = $paymentHelper;
        $this->config = $config;
        $this->quoteFactory = $quoteQuoteFactory;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * @param \Netresearch\OPS\Helper\Data $dataHelper
     */
    public function setDataHelper(\Netresearch\OPS\Helper\Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return \Netresearch\OPS\Helper\Data
     */
    public function getDataHelper()
    {
        if (null === $this->dataHelper) {
            $this->dataHelper = $this->oPSHelper;
        }

        return $this->dataHelper;
    }

    /**
     * generates the OPS order id in dependency to the config
     *
     * @param OrderInterface|CartInterface $salesObject
     * @param bool                         $useOrderIdIfPossible if false forces the usage of quoteid (for Kwixo pm etc.)
     *
     * @return string
     */
    public function getOpsOrderId($salesObject, $useOrderIdIfPossible = true)
    {
        $orderReference = $this->config->getOrderReference($salesObject->getStoreId());
        $devPrefix = $this->config->getConfigData('devprefix');

        if ($useOrderIdIfPossible === false) {
            // force usage of quote id
            $orderReference = PaymentAbstract::REFERENCE_QUOTE_ID;
        }

        switch ($orderReference) {
            case PaymentAbstract::REFERENCE_QUOTE_ID:
                // quote ID as per legacy config setting
                $orderRef = $this->getSalesObjectQuoteId($salesObject);
                break;
            case PaymentAbstract::REFERENCE_ORDER_ID:
                // increment ID as per legacy config setting (with hash character)
                $orderRef = $this->getSalesObjectIncrementIdWithDelimiter($salesObject);
                break;
            default:
                // increment ID as current default behaviour (no legacy config setting available)
                $orderRef = $this->getSalesObjectIncrementId($salesObject);
        }

        return $devPrefix . $orderRef;
    }

    /**
     * getting the order from opsOrderId which can either the quote id or the order increment id
     * in both cases the dev prefix is stripped, if neccessary
     *
     * @param $opsOrderId
     *
     * @return \Magento\Framework\DataObject
     */
    public function getOrder($opsOrderId)
    {
        $opsOrderId = ltrim($opsOrderId, '#');
        $devPrefix = $this->config->getConfigData('devprefix');
        if (strpos($opsOrderId, $devPrefix) !== false) {
            $opsOrderId = substr($opsOrderId, strlen($devPrefix));
        }

        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter('increment_id', $opsOrderId);
        /** @var  \Magento\Sales\Model\ResourceModel\Order\Collection $orderCollection */
        $orderCollection = $this->orderRepository->getList($searchCriteriaBuilder->create());

        // if collection is empty try to load the order by quote id
        if ($orderCollection->getSize() < 1) {
            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
            $searchCriteriaBuilder->addFilter('quote_id', $opsOrderId);
            $orderCollection = $this->orderRepository->getList($searchCriteriaBuilder->create());
            $orderCollection->join(
                ['payment' => $orderCollection->getTable('sales_order_payment')],
                'main_table.entity_id=parent_id',
                'method'
            )
                ->addFieldToFilter('method', [['like' => 'ops_%']])
                // sort by increment_id of order to get only the latest (relevant for quote id search)
                ->addOrder('main_table.increment_id');
        }

        return $orderCollection->getFirstItem();
    }

    /**
     * load and return the quote via the quoteId
     *
     * @param string $quoteId
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote($quoteId)
    {
        return $this->quoteFactory->create()->load($quoteId);
    }

    /**
     * check if billing is same as shipping address
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return int
     */
    public function checkIfAddressesAreSame(\Magento\Sales\Model\Order $order)
    {
        $addMatch = 0;
        $billingAddressHash = null;
        $shippingAddressHash = null;
        if ($order->getBillingAddress() instanceof \Magento\Sales\Model\Order\Address) {
            $billingAddressHash = $this->generateAddressHash(
                $order->getBillingAddress()
            );
        }
        if ($order->getShippingAddress() instanceof \Magento\Sales\Model\Order\Address) {
            $shippingAddressHash = $this->generateAddressHash(
                $order->getShippingAddress()
            );
        }

        if ($billingAddressHash === $shippingAddressHash || $order->getIsVirtual()) {
            $addMatch = 1;
        }

        return $addMatch;
    }

    /**
     * generates hash from address data
     *
     * @param  $address
     *
     * @returns string hash of address
     */
    public function generateAddressHash($address)
    {
        $addressString = $address->getFirstname();
        $addressString .= $address->getMiddlename();
        $addressString .= $address->getLastname();
        $addressString .= $address->getCompany();
        $street = !empty($address->getStreetFull()) ? $address->getStreetFull() : $address->getStreet();
        if (is_array($street)) {
            $street = implode('', $street);
        }
        $addressString .= $street;
        $addressString .= $address->getPostcode();
        $addressString .= $address->getCity();
        $addressString .= $address->getCountryId();

        return hash($this->oPSPaymentHelper->getCryptMethod(), $addressString);
    }

    /**
     * Returns the QuoteId from the SalesObject
     *
     * @param CartInterface | OrderInterface $salesObject
     *
     * @return int
     */
    private function getSalesObjectQuoteId($salesObject)
    {
        $orderRef = '';
        if ($salesObject instanceof CartInterface) {
            $orderRef = $salesObject->getId();
        } elseif ($salesObject instanceof OrderInterface) {
            $orderRef = $salesObject->getQuoteId();
        }

        return $orderRef;
    }

    /**
     * Returns the OrderIncrementId with Delimiter from the SalesObject
     *
     * @param CartInterface | OrderInterface $salesObject
     *
     * @return int
     */
    private function getSalesObjectIncrementIdWithDelimiter($salesObject)
    {
        $orderRef = '';
        if ($salesObject instanceof CartInterface) {
            $salesObject->reserveOrderId();
            $orderRef = self::DELIMITER . $salesObject->getReservedOrderId();
        } elseif ($salesObject instanceof OrderInterface) {
            $orderRef = self::DELIMITER . $salesObject->getIncrementId();
        }

        return $orderRef;
    }

    /**
     * Returns the OrderIncrementId without Delimiter from the SalesObject
     *
     * @param CartInterface | OrderInterface $salesObject
     *
     * @return int
     */
    private function getSalesObjectIncrementId($salesObject)
    {
        $orderRef = '';
        if ($salesObject instanceof CartInterface) {
            $salesObject->reserveOrderId();
            $orderRef = $salesObject->getReservedOrderId();
        } elseif ($salesObject instanceof OrderInterface) {
            $orderRef = $salesObject->getIncrementId();
        }

        return $orderRef;
    }
}

<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Netresearch\OPS\Helper;

use Magento\Sales\Model\OrderFactory;

class Alias extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $backendAuthSession;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    private $oPSHelper;

    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    private $oPSPaymentHelper;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $quoteQuoteFactory;

    /**
     * @var \Netresearch\OPS\Helper\Order
     */
    private $orderHelper;

    /**
     * @var \Netresearch\OPS\Model\AliasFactory
     */
    private $oPSAliasFactory;

    /**
     * @var \Netresearch\OPS\Model\ResourceModel\Alias\CollectionFactory
     */
    private $aliasCollectionFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Magento\Quote\Model\QuoteFactory $quoteQuoteFactory,
        \Netresearch\OPS\Helper\Order $orderHelper,
        \Netresearch\OPS\Model\AliasFactory $oPSAliasFactory,
        \Netresearch\OPS\Model\ResourceModel\Alias\CollectionFactory $aliasCollectionFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder
    ) {
        parent::__construct($context);
        $this->backendAuthSession = $backendAuthSession;
        $this->oPSHelper = $oPSHelper;
        $this->oPSPaymentHelper = $oPSPaymentHelper;
        $this->quoteQuoteFactory = $quoteQuoteFactory;
        $this->orderHelper = $orderHelper;
        $this->oPSAliasFactory = $oPSAliasFactory;
        $this->aliasCollectionFactory = $aliasCollectionFactory;
        $this->jsonEncoder = $jsonEncoder;
    }

    public function getAdminSession()
    {
        return $this->backendAuthSession;
    }

    public function isAdminSession()
    {
        if ($this->getAdminSession()->getUser()) {
            return 0 < $this->getAdminSession()->getUser()->getUserId();
        }

        return false;
    }

    /**
     * PM value is not used for payments with Alias Manager
     *
     * @param \Magento\Quote\Model\Quote\Payment|null Payment
     *
     * @return null
     */
    public function getOpsCode($payment = null)
    {
        return $payment;
    }

    /**
     * BRAND value is not used for payments with Alias Manager
     *
     * @param \Magento\Quote\Model\Quote\Payment|null Payment
     *
     * @return null
     */
    public function getOpsBrand($payment = null)
    {
        return $payment;
    }

    /**
     * get alias or generate a new one
     *
     * alias has length 16 and consists of quote creation date, a separator,
     * and the quote id to make sure we have the full quote id we shorten
     * the creation date accordingly
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return string
     */
    public function getAlias($quote, $forceNew = false)
    {

        $alias = $quote->getPayment()->getAdditionalInformation('alias');
        if (0 == strlen($alias) || $forceNew) {
            /* turn createdAt into format MMDDHHii */
            $createdAt = time();
            $quoteId = $quote->getId();
            /* shorten createdAt, if we would exceed maximum length */
            $maxAliasLength = 16;
            $separator = '99';
            $maxCreatedAtLength
                = $maxAliasLength - strlen($quoteId) - strlen($separator);
            $alias = substr($createdAt, 0, $maxCreatedAtLength) . $separator
                . $quoteId;
        }

        if ($this->isAdminSession() && strpos($alias, 'BE') === false) {
            $alias = $alias . 'BE';
        }

        return $alias;
    }

    /**
     * saves the alias if customer is logged in (and want to create an alias)
     *
     * @param                            $params
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return \Netresearch\OPS\Model\Alias | null
     */
    public function saveAlias($params)
    {
        $quote = null;
        $aliasModel = null;
        $this->oPSHelper->log('aliasData ' . $this->jsonEncoder->encode($this->oPSHelper->clearMsg($params)));
        if (array_key_exists('Alias_OrderId', $params) && is_numeric($params['Alias_OrderId'])) {
            $quote = $this->quoteQuoteFactory->create()->load($params['Alias_OrderId']);
        }

        if ($quote instanceof \Magento\Quote\Model\Quote
            && $quote->getPayment()
            && $quote->getCheckoutMethod() != \Magento\Checkout\Model\Type\Onepage::METHOD_GUEST
            && (array_key_exists('Alias_StorePermanently', $params) && 'Y' == $params['Alias_StorePermanently'])
        ) {
            // alias does not exist -> create a new one if requested
            if (null !== $quote && $quote->getPayment()) {
                // create new alias
                $aliasModel = $this->saveNewAliasFromQuote($quote, $params);
                $quote->getPayment()->setAdditionalInformation(
                    'opsAliasId',
                    $aliasModel->getId()
                );
                $quote->getPayment()->save();
            }
        } elseif (array_key_exists('orderid', $params)) {
            /** @var  $order */
            $order = $this->orderHelper->getOrder($params['orderid']);
            $aliasModel = $this->saveNewAliasFromOrder($order, $params);
            $order->getPayment()->setAdditionalInformation('opsAliasId', $aliasModel->getId());
        }

        return $aliasModel;
    }

    /**
     *
     * @param \Magento\Quote\Model\Quote $quote
     */
    protected function deleteAlias(\Magento\Quote\Model\Quote $quote)
    {
        $customerId = $quote->getCustomer()->getId();
        $billingAddressHash = $this->orderHelper->generateAddressHash(
            $quote->getBillingAddress()
        );
        $shippingAddressHash = $this->orderHelper->generateAddressHash(
            $quote->getShippingAddress()
        );
        $aliasModel = $this->oPSAliasFactory->create();
        $aliasCollection = $aliasModel->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('billing_address_hash', $billingAddressHash)
            ->addFieldToFilter('shipping_address_hash', $shippingAddressHash)
            ->addFieldToFilter('state', \Netresearch\OPS\Model\Alias\State::PENDING)
            ->addFieldToFilter('store_id', [['eq' => $quote->getStoreId()], ['null' => true]])
            ->setOrder('created_at', 'DESC')
            ->setPageSize(1);
        $aliasCollection->load();
        foreach ($aliasCollection as $alias) {
            $alias->delete();
        }
    }

    protected function saveNewAliasFromQuote(\Magento\Quote\Model\Quote $quote, $params)
    {
        $customerId = $quote->getCustomer()->getId();

        $billingAddressHash = $this->orderHelper->generateAddressHash(
            $this->getQuoteBillingAddress($quote)
        );
        $shippingAddressHash = $this->orderHelper->generateAddressHash(
            $this->getQuoteShippingAddress($quote)
        );

        $aliasData = [];
        $aliasData['customer_id'] = $customerId;
        $aliasData['alias'] = $params['Alias_AliasId'];
        $aliasData['expiration_date'] = $params['Card_ExpiryDate'];
        $aliasData['billing_address_hash'] = $billingAddressHash;
        $aliasData['shipping_address_hash'] = $shippingAddressHash;
        $aliasData['brand'] = $params['Card_Brand'];
        $aliasData['payment_method'] = $quote->getPayment()->getMethod();
        $aliasData['pseudo_account_or_cc_no'] = $params['Card_CardNumber'];
        $aliasData['state'] = \Netresearch\OPS\Model\Alias\State::PENDING;
        $aliasData['store_id'] = $quote->getStoreId();

        if (array_key_exists('Card_CardHolderName', $params)) {
            $aliasData['card_holder'] = $params['Card_CardHolderName'];
        }

        $aliasModel = $this->persistAlias($aliasData);

        return $aliasModel;
    }

    public function saveNewAliasFromOrder(\Magento\Sales\Model\Order $order, $params)
    {
        $customerId = $order->getCustomerId();
        $billingAddressHash = $this->orderHelper->generateAddressHash(
            $this->getOrderBillingAddress($order)
        );
        $shippingAddressHash = $this->orderHelper->generateAddressHash(
            $this->getOrderShippingAddress($order)
        );


        $aliasData = [];
        $aliasData['customer_id'] = $customerId;
        $aliasData['alias'] = $params['alias'];
        $aliasData['expiration_date'] = $params['ed'];
        $aliasData['billing_address_hash'] = $billingAddressHash;
        $aliasData['shipping_address_hash'] = $shippingAddressHash;
        $aliasData['brand'] = $params['brand'];
        $aliasData['payment_method'] = $order->getPayment()->getMethod();
        $aliasData['pseudo_account_or_cc_no'] = $params['cardno'];
        $aliasData['state'] = \Netresearch\OPS\Model\Alias\State::ACTIVE;
        $aliasData['store_id'] = $order->getStoreId();
        $aliasData['card_holder'] = $params['cn'];

        $aliasModel = $this->persistAlias($aliasData);

        return $aliasModel;
    }

    public function persistAlias(array $aliasParams)
    {
        /** @var Netresearch_OPS_Model_Alias $aliasModel */
        $aliasModel = $this->oPSAliasFactory->create()->load($aliasParams['alias'], 'alias');

        $this->oPSHelper->log(
            'saving alias' . $this->jsonEncoder->encode($aliasModel->getData())
        );

        $aliasModel->addData($aliasParams);
        $aliasModel->save();

        return $aliasModel;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return \Magento\Quote\Model\Quote\Address
     */
    protected function getQuoteBillingAddress(\Magento\Quote\Model\Quote $quote)
    {
        return $quote->getBillingAddress();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return \Magento\Sales\Api\Data\OrderAddressInterface|null
     */
    protected function getOrderBillingAddress(\Magento\Sales\Model\Order $order)
    {
        return $order->getBillingAddress();
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return \Magento\Quote\Model\Quote\Address
     */
    protected function getQuoteShippingAddress(\Magento\Quote\Model\Quote $quote)
    {
        $address = $quote->getShippingAddress();
        if ($quote->getIsVirtual()) {
            $address = $this->getQuoteBillingAddress($quote);
        }

        return $address;
    }

    protected function getOrderShippingAddress(\Magento\Sales\Model\Order $order)
    {
        $address = $order->getShippingAddress();
        if ($order->getIsVirtual()) {
            $address = $this->getOrderBillingAddress($order);
        }

        return $address;
    }

    /**
     * retrieves the aliases for a given customer
     *
     * @param int $customerId
     * @param string $methodCode
     * @param \Magento\Quote\Model\Quote
     *
     * @return \Netresearch\OPS\Model\ResourceModel\Alias\Collection - collection of aliases for the given customer
     */
    public function getAliasesForCustomer($customerId, $methodCode = null, \Magento\Quote\Model\Quote $quote = null)
    {
        $billingAddressHash = null;
        $shippingAddressHash = null;
        if (null !== $quote) {
            $billingAddressHash = $this->orderHelper->generateAddressHash(
                $quote->getBillingAddress()
            );
            $shippingAddressHash = $this->orderHelper->generateAddressHash(
                $quote->getShippingAddress()
            );
        }

        return $this->oPSAliasFactory->create()
            ->getAliasesForCustomer(
                $customerId,
                $methodCode,
                $billingAddressHash,
                $shippingAddressHash
            );
    }

    /**
     * if alias is valid for address
     *
     * @param int                                $customerId
     * @param string                             $alias
     * @param \Magento\Quote\Model\Quote\Address $billingAddress
     * @param \Magento\Quote\Model\Quote\Address $shippingAddress
     * @param null                               $storeId
     *
     * @return bool
     */
    public function isAliasValidForAddresses(
        $customerId,
        $alias,
        $billingAddress,
        $shippingAddress,
        $storeId = null
    ) {

        $aliasCollection = $this->getAliasesForAddresses(
            $customerId,
            $billingAddress,
            $shippingAddress,
            $storeId
        )
            ->addFieldToFilter('alias', $alias)
            ->setPageSize(1);

        return (1 == $aliasCollection->count());
    }

    /**
     * get aliases that are allowed for customer with given addresses
     *
     * @param int                                             $customerId
     * @param \Magento\Customer\Model\Address\AbstractAddress $billingAddress
     * @param \Magento\Customer\Model\Address\AbstractAddress $shippingAddress
     *
     * @return \Netresearch\OPS\Model\ResourceModel\Alias\Collection
     */
    public function getAliasesForAddresses(
        $customerId,
        $billingAddress = null,
        $shippingAddress = null
    ) {
        $collection = $this->aliasCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId);

        if ($billingAddress) {
            $billingAddressHash = $this->orderHelper->generateAddressHash($billingAddress);
            $collection->addFieldToFilter('billing_address_hash', $billingAddressHash);
        }

        if ($shippingAddress) {
            $shippingAddressHash = $this->orderHelper->generateAddressHash($shippingAddress);
            $collection->addFieldToFilter('shipping_address_hash', $shippingAddressHash);
        }

        return $collection;
    }

    /**
     * formats the pseudo cc number in a brand specific format
     * supported brand (so far):
     *      - MasterCard
     *      - Visa
     *      - American Express
     *      - Diners Club
     *
     * @param $brand     - the cc brand we need to format the pseudo cc number
     * @param $aliasCcNo - the pseudo cc number itself
     *
     * @return string - the formatted pseudo cc number
     */
    public function formatAliasCardNo($brand, $aliasCcNo)
    {

        if (in_array(strtolower($brand), ['visa', 'mastercard'])) {
            $aliasCcNo = implode(' ', str_split($aliasCcNo, 4));
        }
        if (in_array(strtolower($brand), ['american express', 'diners club', 'maestrouk'])) {
            $aliasCcNo = str_replace('-', ' ', $aliasCcNo);
        }

        return strtoupper($aliasCcNo);
    }

    /**
     * saves the alias and if given the cvc to the payment information
     *
     * @param \Magento\Payment\Model\Info $payment           - the payment which should be updated
     * @param array                       $aliasData         - the data we will update
     * @param boolean                     $userIsRegistering - is registering method in checkout
     * @param boolean                     $paymentSave       - is it necessary to save the payment afterwards
     */
    public function setAliasToPayment(
        \Magento\Payment\Model\Info $payment,
        array $aliasData,
        $userIsRegistering = false,
        $paymentSave = false
    ) {
        if (array_key_exists('alias_aliasid', $aliasData) && 0 < strlen(trim($aliasData['alias_aliasid']))) {
            $payment->setAdditionalInformation('alias', trim($aliasData['alias_aliasid']));
            $payment->setAdditionalInformation('userIsRegistering', $userIsRegistering);
            if (array_key_exists('card_cvc', $aliasData)) {
                $payment->setAdditionalInformation('cvc', $aliasData['card_cvc']);
                $this->setCardHolderToAlias($payment->getQuote(), $aliasData);
            }

            if (array_key_exists('method', $aliasData)) {
                $alias = $this->oPSAliasFactory->create()->load($aliasData['alias_aliasid'], 'alias');
                if ($alias && $alias->getId()) {
                    $alias->setPaymentMethod($aliasData['method']);
                    $alias->save();
                }
            }

            $payment->setDataChanges(true);
            if ($paymentSave === true) {
                $payment->save();
            }
        } else {
            $this->oPSHelper->log('did not save alias due to empty alias');
            $this->oPSHelper->log(serialize($aliasData));
        }
    }

    protected function setCardHolderToAlias($quote, $aliasData)
    {
        $customerId = $quote->getCustomerId();
        $billingAddressHash = $this->orderHelper->generateAddressHash($quote->getBillingAddress());
        $shippingAddressHash = $this->orderHelper->generateAddressHash($quote->getShippingAddress());
        $oldAlias = $this->aliasCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('billing_address_hash', $billingAddressHash)
            ->addFieldToFilter('shipping_address_hash', $shippingAddressHash)
            ->addFieldToFilter('state', \Netresearch\OPS\Model\Alias\State::ACTIVE)
            ->addFieldToFilter('store_id', [['eq' => $quote->getStoreId()], ['null' => true]])
            ->getFirstItem();
        // and if so update this alias with alias data from alias gateway
        if (is_numeric($oldAlias->getId())
            && null === $oldAlias->getCardHolder()
            && array_key_exists('Card_CardHolderName', $aliasData)
        ) {
            $oldAlias->setCardHolder($aliasData['Card_CardHolderName']);
            $oldAlias->save();
        }
    }

    /**
     * set the last pending alias to active and remove other aliases for customer based on address
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Sales\Model\Order $order
     * @param bool                       $saveSalesObjects
     */
    public function setAliasActive(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Sales\Model\Order $order = null,
        $saveSalesObjects = false
    ) {
        if (null === $quote->getPayment()->getAdditionalInformation('userIsRegistering')
            || false == $quote->getPayment()->getAdditionalInformation('userIsRegistering')
        ) {
            $aliasesToDelete = $this
                ->getAliasesForAddresses(
                    $quote->getCustomer()->getId(),
                    $quote->getBillingAddress(),
                    $quote->getShippingAddress()
                )
                ->addFieldToFilter('state', \Netresearch\OPS\Model\Alias\State::ACTIVE);
            $lastPendingAlias = $this
                ->getAliasesForAddresses(
                    $quote->getCustomer()->getId(),
                    null,
                    null,
                    $quote->getStoreId()
                )
                ->addFieldToFilter('alias', $quote->getPayment()->getAdditionalInformation('alias'))
                ->addFieldToFilter('state', \Netresearch\OPS\Model\Alias\State::PENDING)
                ->setOrder('created_at', \Magento\Framework\Data\Collection::SORT_ORDER_DESC)
                ->getFirstItem();
            if (0 < $lastPendingAlias->getId()) {
                foreach ($aliasesToDelete as $alias) {
                    $alias->delete();
                }
                $billingAddressHash = $this->orderHelper->generateAddressHash($this->getQuoteBillingAddress($quote));
                $shippingAddressHash = $this->orderHelper->generateAddressHash($this->getQuoteShippingAddress($quote));
                $lastPendingAlias->setState(\Netresearch\OPS\Model\Alias\State::ACTIVE);
                $lastPendingAlias->setPaymentMethod($order->getPayment()->getMethod());
                $lastPendingAlias->setBillingAddressHash($billingAddressHash);
                $lastPendingAlias->setShippingAddressHash($shippingAddressHash);
                $lastPendingAlias->save();
            }
        } else {
            $this->setAliasToActiveAfterUserRegisters($order, $quote);
        }
        $this->cleanUpAdditionalInformation($order->getPayment(), false, $saveSalesObjects);
        $this->cleanUpAdditionalInformation($quote->getPayment(), false, $saveSalesObjects);
    }

    public function setAliasToActiveAfterUserRegisters(
        \Magento\Sales\Model\Order $order,
        \Magento\Quote\Model\Quote $quote
    ) {
        if (true == $quote->getPayment()->getAdditionalInformation('userIsRegistering')) {
            $customerId = $order->getCustomerId();
            $billingAddressHash = $this->orderHelper->generateAddressHash(
                $quote->getBillingAddress()
            );
            $shippingAddressHash = $this->orderHelper->generateAddressHash(
                $quote->getShippingAddress()
            );
            $aliasId = $quote->getPayment()->getAdditionalInformation(
                'opsAliasId'
            );
            if (is_numeric($aliasId) && 0 < $aliasId) {
                $alias = $this->aliasCollectionFactory->create()
                    ->addFieldToFilter('alias', $quote->getPayment()->getAdditionalInformation('alias'))
                    ->addFieldToFilter('billing_address_hash', $billingAddressHash)
                    ->addFieldToFilter('shipping_address_hash', $shippingAddressHash)
                    ->addFieldToFilter('store_id', ['eq' => $quote->getStoreId()])
                    ->getFirstItem();

                $alias->setState(\Netresearch\OPS\Model\Alias\State::ACTIVE);
                $alias->setPaymentMethod($order->getPayment()->getMethod());
                $alias->setCustomerId($customerId);
                $alias->save();
            }
        }
    }

    /**
     * cleans up the stored cvc and storedOPSId
     *
     * @param \Magento\Quote\Model\Quote\Payment || \Magento\Sales\Model\Order\Payment $payment
     * @param bool $cvcOnly
     * @param bool $savePayment
     *
     */
    public function cleanUpAdditionalInformation($payment, $cvcOnly = false, $savePayment = false)
    {
        if (is_array($payment->getAdditionalInformation())
            && array_key_exists('cvc', $payment->getAdditionalInformation())
        ) {
            $payment->unsAdditionalInformation('cvc');
        }

        if ($cvcOnly === false && is_array($payment->getAdditionalInformation())
            && array_key_exists('storedOPSId', $payment->getAdditionalInformation())
        ) {
            $payment->unsAdditionalInformation('storedOPSId');
        }

        /* OGNH-7: seems not to needed anymore since payment and quote is saved after this call,
         otherwise admin payments will fail */
        if ($savePayment) {
            $payment->save();
        }
    }
}

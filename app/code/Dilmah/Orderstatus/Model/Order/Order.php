<?php

namespace Dilmah\Orderstatus\Model\Order;

use Dilmah\Orderstatus\Model\Log\Logger;

/**
 * Class Order
 * @package IWD\OrderManager\Model\Order
 */
class Order extends \Magento\Sales\Model\Order
{
    /**
     * @var \IWD\OrderManager\Model\Quote\Quote
     */
    private $quote;

    /**
     * @var \IWD\OrderManager\Model\Invoice\Invoice
     */
    private $invoice;

    /**
     * @var \IWD\OrderManager\Model\Creditmemo\Creditmemo
     */
    private $creditmemo;

    /**
     * @var \IWD\OrderManager\Model\Shipment\Shipment
     */
    private $shipment;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory
     */
    private $taxCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory
     */
    private $orderHistoryCollectionFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    private $customerDataFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory
     * @param \IWD\OrderManager\Model\Quote\Quote $quote
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \IWD\OrderManager\Model\Invoice\Invoice $invoice
     * @param \IWD\OrderManager\Model\Shipment\Shipment $shipment
     * @param \IWD\OrderManager\Model\Creditmemo\Creditmemo $creditmemo
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $orderHistoryCollectionFactory
     * @param \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $taxCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory,
        \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory,
        //\IWD\OrderManager\Model\Quote\Quote $quote,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        //\IWD\OrderManager\Model\Invoice\Invoice $invoice,
        //\IWD\OrderManager\Model\Shipment\Shipment $shipment,
       // \IWD\OrderManager\Model\Creditmemo\Creditmemo $creditmemo,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $orderHistoryCollectionFactory,
        \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $taxCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        /*$this->quote = $quote;
        $this->invoice = $invoice;
        $this->creditmemo = $creditmemo;
        $this->shipment = $shipment;*/
        $this->scopeConfig = $scopeConfig;
        $this->taxCollectionFactory = $taxCollectionFactory;
        $this->orderHistoryCollectionFactory = $orderHistoryCollectionFactory;
        $this->customerRepository = $customerRepository;
        $this->customerDataFactory = $customerDataFactory;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $timezone,
            $storeManager,
            $orderConfig,
            $productRepository,
            $orderItemCollectionFactory,
            $productVisibility,
            $invoiceManagement,
            $currencyFactory,
            $eavConfig,
            $orderHistoryFactory,
            $addressCollectionFactory,
            $paymentCollectionFactory,
            $historyCollectionFactory,
            $invoiceCollectionFactory,
            $shipmentCollectionFactory,
            $memoCollectionFactory,
            $trackCollectionFactory,
            $salesOrderCollectionFactory,
            $priceCurrency,
            $productListFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return bool
     */
   /* public function isAllowDeleteOrder()
    {
        $isAllowedDelete = $this->scopeConfig->getValue('iwdordermanager/allow_delete/orders');

        if ($isAllowedDelete) {
            $allowedStatuses = $this->scopeConfig->getValue('iwdordermanager/allow_delete/order_statuses');
            $allowedStatuses = explode(',', $allowedStatuses);
            return in_array($this->getStatus(), $allowedStatuses);
        }

        return false;
    }*/

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        $this->deleteRelatedShipments();
        $this->deleteRelatedInvoices();
        $this->deleteRelatedCreditMemos();
        $this->deleteRelatedOrderInfo();

        return parent::beforeDelete();
    }

    /**
     * @return void
     */
    /*private function deleteRelatedOrderInfo()
    {
        try {
            $collection = $this->_addressCollectionFactory->create()->setOrderFilter($this);
            foreach ($collection as $object) {
                $object->delete();
            }

            $collection = $this->_orderItemCollectionFactory->create()->setOrderFilter($this);
            foreach ($collection as $object) {
                $object->delete();
            }

            $collection = $this->_paymentCollectionFactory->create()->setOrderFilter($this);
            foreach ($collection as $object) {
                $object->delete();
            }

            $collection = $this->orderHistoryCollectionFactory->create()->setOrderFilter($this);
            foreach ($collection as $object) {
                $object->delete();
            }

            $collection = $this->taxCollectionFactory->create()->loadByOrder($this);
            foreach ($collection as $object) {
                $object->delete();
            }
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }
    }

    /**
     * @return void
     */
    /*private function deleteRelatedInvoices()
    {
        try {
            $collection = $this->getInvoiceCollection();
            foreach ($collection as $item) {
                $object = $this->invoice->load($item->getId());
                $object->delete();
            }
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }
    }

    /**
     * @return void
     */
   /* private function deleteRelatedShipments()
    {
        try {
            $collection = $this->getShipmentsCollection();
            foreach ($collection as $item) {
                $object = $this->shipment->load($item->getId());
                $object->delete();
            }
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }
    }

    /**
     * @return void
     */
    /*private function deleteRelatedCreditMemos()
    {
        try {
            $collection = $this->getCreditmemosCollection();
            foreach ($collection as $item) {
                $object = $this->creditmemo->load($item->getId());
                $object->delete();
            }
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }
    }

    /**
     * @param string $status
     * @return void
     */
    public function updateOrderStatus($status)
    {
        $oldStatus = $this->getStatus();
        $newStatus = $status;

        $this->setData('status', $status)->save();

        if ($oldStatus != $newStatus) {
            Logger::getInstance()->addChange('Status', $oldStatus, $newStatus);
            Logger::getInstance()->saveLogsAsOrderComments($this);
        }
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer()
    {
        $customerId = $this->getCustomerId();

        try {
            return $this->getCustomerRepository()->getById($customerId);
        } catch (\Exception $e) {
            $customer = $this->customerDataFactory->create();
            $customer->setId(null);
            return $customer;
        }
    }

    /**
     * @return \IWD\OrderManager\Model\Quote\Quote
     */
    public function getQuote()
    {
        if (empty($this->quote->getId())) {
            $quoteId = $this->getQuoteId();
            $this->quote->load($quoteId);
        }
        return $this->quote;
    }

    /**
     * @return \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public function getCustomerRepository()
    {
        return $this->customerRepository;
    }
}

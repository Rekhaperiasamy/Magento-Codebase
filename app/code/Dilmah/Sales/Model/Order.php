<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Sales
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Sales\Model;

use Magento\Sales\Model\Order as SalesOrder;
use Magento\Sales\Model as SalesModel;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Order
 * @package Dilmah\Sales\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Order extends SalesOrder
{
    /**
     * @var \Dilmah\Checkout\Helper\Data
     */
    protected $helper;

    /**
     * Order constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param SalesOrder\Config $orderConfig
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param SalesModel\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param SalesOrder\Status\HistoryFactory $orderHistoryFactory
     * @param SalesModel\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory
     * @param SalesModel\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory
     * @param SalesModel\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory
     * @param SalesModel\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param SalesModel\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param SalesModel\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory
     * @param SalesModel\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory
     * @param SalesModel\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory
     * @param \Dilmah\Checkout\Helper\Data $helper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        SalesOrder\Config $orderConfig,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        SalesModel\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Eav\Model\Config $eavConfig,
        SalesOrder\Status\HistoryFactory $orderHistoryFactory,
        SalesModel\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory,
        SalesModel\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory,
        SalesModel\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory,
        SalesModel\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        SalesModel\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        SalesModel\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory,
        SalesModel\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory,
        SalesModel\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory,
        \Dilmah\Checkout\Helper\Data $helper
    ) {
        $this->helper = $helper;

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
            $productListFactory
        );
    }

    /**
     * Retrieve virtual product message for order email
     *
     * @return \Magento\Framework\Phrase|null
     */
    public function getVirtualProductMessage()
    {
        return $this->helper->getVirtualProductMessage($this);
    }

    /**
     * Has virtual products
     *
     * @return bool
     */
    public function hasVirtualItems()
    {
        return $this->helper->isVirtualProductAvailable($this);
    }
}

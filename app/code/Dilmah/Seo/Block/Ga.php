<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dilmah\Seo\Block;

use Magento\Framework\Stdlib\CookieManagerInterface;

class Ga extends \Magento\GoogleTagManager\Block\Ga
{
    /**
     * Cookie name for users who allowed cookie save
     */
    const IS_USER_ALLOWED_SAVE_COOKIE = 'user_allowed_save_cookie';

    /**
     * Checkout session.
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Product factory.
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $detailedProductRepo;

    /**
     * Sales order factory.
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * Google Analytics Helper.
     *
     * @var \Magento\GoogleAnalytics\Helper\Data
     */
    protected $helper;

    /**
     * Cookie Helper.
     *
     * @var \Magento\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * Jason Data Helper.
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * CookieManager
     *
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * Ga constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepo
     * @param \Magento\GoogleTagManager\Helper\Data $googleAnalyticsData
     * @param \Magento\Cookie\Helper\Cookie $cookieHelper
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param CookieManagerInterface $cookieManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection,
        \Magento\Catalog\Api\ProductRepositoryInterface  $productRepo,
        \Magento\GoogleTagManager\Helper\Data $googleAnalyticsData,
        \Magento\Cookie\Helper\Cookie $cookieHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        CookieManagerInterface $cookieManager,
        array $data = []
    ) {
        $this->detailedProductRepo = $productRepo;
        $this->orderFactory = $orderFactory;
        $this->checkoutSession = $checkoutSession;
        $this->cookieHelper = $cookieHelper;
        $this->jsonHelper = $jsonHelper;
        $this->cookieManager = $cookieManager;
        parent::__construct($context, $salesOrderCollection, $googleAnalyticsData, $cookieHelper, $jsonHelper, $data);
    }

    /**
     * Get order.
     *
     * @return Order|null
     */
    public function getOrder()
    {
        $orderId = $this->checkoutSession->getLastOrderId();
        if ($orderId) {
            return $this->orderFactory->create()->load($orderId);
        } else {
            return;
        }
    }
    /**
     * Is gtm available.
     *
     * @return bool
     */
    protected function _isAvailable()
    {
        return $this->_googleAnalyticsData->isGoogleAnalyticsAvailable();
    }

    /**
     * Render GA tracking scripts.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_isAvailable()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Get store currency code for page tracking javascript code.
     *
     * @return string
     */
    public function getStoreCurrencyCode()
    {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }

    /**
     * Variant Calculation.
     *
     * @param string $sku
     *
     * @return string
     */
    public function valiantCalculator($sku)
    {
        $detailedProduct = $this->detailedProductRepo->get($sku);
        if ($detailedProduct->getData(\Dilmah\Catalog\Helper\Data::IS_PACK_PRODUCT_ATTRIBUTE) == 1) {
            if (!$this->isEmpty($detailedProduct->getData(\Dilmah\Catalog\Helper\Data::PACK_SIZE_ATTRIBUTE))) {
                $packSize = $detailedProduct->getData(\Dilmah\Catalog\Helper\Data::PACK_SIZE_ATTRIBUTE);
            } else {
                return '';
            }
            if (!$this->isEmpty($detailedProduct->getData(\Dilmah\Catalog\Helper\Data::TEA_BAG_QTY_ATTRIBUTE))) {
                return $packSize * $detailedProduct->getData(\Dilmah\Catalog\Helper\Data::TEA_BAG_QTY_ATTRIBUTE);
            } elseif (!$this->isEmpty($detailedProduct->getData(\Dilmah\Catalog\Helper\Data::GRAMMAGE_ATTRIBUTE))) {
                return $packSize * $detailedProduct->getData(\Dilmah\Catalog\Helper\Data::GRAMMAGE_ATTRIBUTE);
            }
        } else {
            return '';
        }
    }

    /**
     * Purchased Child objects of the order item.
     *
     * @param array $purchasedItemsArray
     *
     * @return array
     */
    public function purchasedItemsExtractor($purchasedItemsArray)
    {
        $purchasedProduct = [];
        /** @var $purchasedItems \Magento\Catalog\Model\Product */
        foreach ($purchasedItemsArray as $purchasedItems) {
            $purchasedProduct['id'] = $purchasedItems->getSku();
            $purchasedProduct['brand'] = \Dilmah\Catalog\Helper\Data::BRAND_LABEL;
            $purchasedProduct['name'] = $purchasedItems->getName();
            $purchasedProduct['price'] = $purchasedItems->getBasePrice();
            $purchasedProduct['quantity'] = $purchasedItems->getQtyOrdered();
            $purchasedProduct['variant'] = $this->valiantCalculator($purchasedItems->getSku());
            $purchasedProducts[] = $purchasedProduct;
        }

        return $purchasedProducts;
    }

    /**
     * Render information about specified orders and their items.
     *
     * @return string
     */
    public function getOrdersData()
    {
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return '';
        }
        $currentOrder = $this->getOrder();
        $actionField['id'] = $currentOrder->getIncrementId();
        $revenue = $currentOrder->getBaseGrandTotal() -
            ($currentOrder->getBaseTaxAmount() + $currentOrder->getBaseShippingAmount());
        $actionField['revenue'] = (string) $revenue;
        $actionField['affiliation'] = \Dilmah\Catalog\Helper\Data::BRAND_LABEL;
        $actionField['tax'] = $currentOrder->getBaseTaxAmount();
        $actionField['shipping'] = $currentOrder->getBaseShippingAmount();
        $actionField['coupon'] = (string) $currentOrder->getCouponCode();

        $products = [];
        /** @var \Magento\Sales\Model\Order\Item $item*/
        foreach ($currentOrder->getAllVisibleItems() as $item) {
            $product['id'] = $item->getSku();
            $product['variant'] = $this->valiantCalculator($item->getSku());
            $product['name'] = $item->getName();
            $product['price'] = $item->getBasePrice();
            $product['quantity'] = $item->getQtyOrdered();
            if ($item->getHasChildren()) {
                $product['purchasedProducts'] = $this->purchasedItemsExtractor($item->getChildrenItems(), $item);
            }
            //$product['category'] = ''; //Not available to populate
            $products[] = $product;
        }
        $json['ecommerce']['purchase']['actionField'] = $actionField;
        $json['ecommerce']['purchase']['products'] = $products;
        $json['ecommerce']['currencyCode'] = $this->getStoreCurrencyCode();
        $json['event'] = 'purchase';
        $result[] = 'dataLayer.push('.$this->jsonHelper->jsonEncode($json).");\n";

        return implode("\n", $result);
    }

    /**
     * Is the user enable cookies.
     *
     * @return bool
     */
    public function isUserNotAllowSaveCookie()
    {
        return $this->cookieHelper->isUserNotAllowSaveCookie();
    }

    /**
     * Is user accepted the cookie policy
     *
     * @return null|string
     */
    public function isAcceptedCookiePolicyForCurrentWebsite()
    {
        return $this->cookieManager->getCookie(self::IS_USER_ALLOWED_SAVE_COOKIE) ? true : false ;
    }

    /**
     * Getting active store id of france website
     *
     * @return bool
     */
    public function isFranceStore()
    {
        $stores = null;
        $storeData = $this->_storeManager->getStores($withDefault = false);

        foreach($storeData as $store){
            if($store->getData("is_active")){
                if($store->getWebsite()->getCode() == "web_fr") {
                    $stores = $store->getId();
                }
            }
        }

        return ($this->_storeManager->getStore()->getId() == $stores) ? true : false ;
    }
}

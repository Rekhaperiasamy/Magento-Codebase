<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Seo\Block;

class Code extends \Magento\GoogleAdwords\Block\Code
{
    /**
     * product page type.
     */
    const PAGE_TYPE_PRODUCT = 'product_page';

    /**
     * category page type.
     */
    const PAGE_TYPE_CATEGORY = 'category_page';

    /**
     * cms page type.
     */
    const PAGE_TYPE_CMS = 'cms_page';

    /**
     * cms page type.
     */
    const PAGE_TYPE_HOME = 'home_page';

    /**
     * Scope of the config.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Page type.
     *
     * @var pageType
     */
    protected $_pageType;

    /**
     * Sales order factory.
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * Checkout session.
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Dilmah\Seo\Block\ListJson
     */
    protected $listJson;

    /**
     * Code constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\GoogleAdwords\Helper\Data $googleAdwordsData
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Dilmah\Seo\Block\ListJson $listJson
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\GoogleAdwords\Helper\Data $googleAdwordsData,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Dilmah\Seo\Block\ListJson $listJson,
        array $data = []
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_coreRegistry = $coreRegistry;
        $this->orderFactory = $orderFactory;
        $this->checkoutSession = $checkoutSession;
        $this->listJson = $listJson;
        parent::__construct($context, $googleAdwordsData, $data);
    }

    /**
     * Get order.
     *
     * @return order|null
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
     * Get grand total of order.
     *
     * @return string
     */
    public function getGrandTotal()
    {
        if ($this->getOrder()) {
            return $this->getOrder()->getGrandTotal();
        } else {
            return '';
        }
    }

    /**
     * Page Type.
     *
     * @return mixed|string
     */
    public function getPageType()
    {
        $pageType = $this->_coreRegistry->registry('page_type');

        return empty($pageType) ? 'other' : $pageType;
    }

    /**
     * Product Id.
     *
     * @return mixed|string
     */
    public function getProductId()
    {
        $productId = $this->_coreRegistry->registry('product_id');

        return empty($productId) ? '' : $productId;
    }

    /**
     * Product Id.
     *
     * @return mixed|string
     */
    public function getOrderedProductIds()
    {
        if ($this->getOrder()) {
            $items = $this->getOrder()->getAllVisibleItems();
            $products = [];
            foreach ($items as $item) {
                $products[] = $item['product_id'];
            }

            return $products;
        } else {
            return [];
        }
    }

    /**
     * Purchased Product List.
     *
     * @return mixed|string
     */
    public function getPurchsedProductList()
    {
        $purchasedProductCollection = $this->getOrderedProductIds();

        return $purchasedProductCollection;
    }

    /**
     * Total Remarketing Values
     * @return string
     */
    public function getTotalValue()
    {
        if ($this->getPageType() == 'product_page') {
            $curProduct = $this->listJson->getCurrentProduct();
            $productPrice = isset($curProduct) ?
                $curProduct->getPriceInfo()->getPrice('final_price')->getValue() : '';
            return $productPrice;
        } elseif ($this->checkoutSession->hasQuote()) {
            return $this->checkoutSession->getQuote()->getGrandTotal();
        }
        return $this->getGrandTotal();
    }
}

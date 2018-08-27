<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Seo
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2017 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Category
 * @package CameraHouse\Seo\Observer
 */
class Category implements ObserverInterface
{
    /**
     * France Store Code
     */
    const FR_STORE_CODE = 'fr';

    /**
     * Global shop locale
     */
    const GLOBAL_SHOP_LOCALE = 'en-US';

    /**
     * France shop locale
     */
    const FR_SHOP_LOCALE = 'fr-FR';

    /**
     * Global Store ID
     */
    const GLOBAL_STORE_ID = 1;

    /**
     * Variable categoryHelper
     * @var CategoryHelper
     */
    protected $categoryHelper;

    /**
     * Variable pageConfig
     * @var PageConfig
     */
    protected $pageConfig;

    /**
     * Variable urlBuilder
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Store Manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Category constructor.
     * @param CategoryHelper $category
     * @param PageConfig $config
     * @param UrlInterface $url
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CategoryHelper $category,
        PageConfig $config,
        UrlInterface $url,
        StoreManagerInterface $storeManager
    )
    {
        $this->categoryHelper = $category;
        $this->pageConfig = $config;
        $this->urlBuilder = $url;
        $this->_storeManager = $storeManager;
    }

    /**
     * Class execute function
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // DT-1576 start - //TODO: Get store code and Global Store ID using admin config
//        if($this->_storeManager->getStore()->getCode() == self::FR_STORE_CODE){
//            $this->pageConfig->addRemotePageAsset(
//                $this->getPageUrl([]),
//                'link_rel',
//                [
//                    'attributes' => [
//                        'rel' => 'alternate',
//                        'hreflang' => self::FR_SHOP_LOCALE,
//                        'href' => $this->_storeManager->getStore()->getBaseUrl()
//                    ]
//                ]
//            );
//
//            $this->pageConfig->addRemotePageAsset(
//                $this->getPageUrl([]),
//                'link_rel',
//                [
//                    'attributes' => [
//                        'rel' => 'alternate',
//                        'hreflang' => self::GLOBAL_SHOP_LOCALE,
//                        'href' => $this->_storeManager->getStore(self::GLOBAL_STORE_ID)->getBaseUrl()
//                    ]
//                ]
//            );
//        }
        // DT-1576 end

        if ('catalog_category_view' != $observer->getEvent()->getFullActionName()) {
            return $this;
        }

        /** @var \Magento\Catalog\Block\Product\ListProduct $productListBlock */
        $productListBlock = $observer->getEvent()->getLayout()->getBlock('category.products.list');

        /*to avoid brand pages*/
        if ($productListBlock == false) {
            return $this;
        }

        /** @var \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbarBlock */
        $toolbarBlock = $productListBlock->getToolbarBlock();
        /** @var \Magento\Theme\Block\Html\Pager $pagerBlock */
        $pagerBlock = $toolbarBlock->getChildBlock('product_list_toolbar_pager');
        $pagerBlock->setAvailableLimit($toolbarBlock->getAvailableLimit())
            ->setCollection($productListBlock->getLayer()->getProductCollection());

        /**
         * Add rel prev and rel next
         */
        if (1 < $pagerBlock->getCurrentPage()) {
            if ($pagerBlock->getCurrentPage() == 2) {
                $this->pageConfig->addRemotePageAsset(
                    $this->getPageUrl([]),
                    'link_rel',
                    ['attributes' => ['rel' => 'prev']]
                );
            } else {
                $this->pageConfig->addRemotePageAsset(
                    $this->getPageUrl([
                        $pagerBlock->getPageVarName() => $pagerBlock->getCollection()->getCurPage(-1)
                    ]),
                    'link_rel',
                    ['attributes' => ['rel' => 'prev']]
                );
            }

        }
        if ($pagerBlock->getCurrentPage() < $pagerBlock->getLastPageNum()) {
            $this->pageConfig->addRemotePageAsset(
                $this->getPageUrl([
                    $pagerBlock->getPageVarName() => $pagerBlock->getCollection()->getCurPage(+1)
                ]),
                'link_rel',
                ['attributes' => ['rel' => 'next']]
            );
        }
    }


    /**
     * Retrieve page URL by defined parameters
     *
     * @param array $params
     * @return string
     */
    protected function getPageUrl($params = [])
    {
        $urlParams = [];
        $urlParams['_current'] = false;
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;

        return $this->urlBuilder->getUrl('*/*/*', $urlParams);
    }
}
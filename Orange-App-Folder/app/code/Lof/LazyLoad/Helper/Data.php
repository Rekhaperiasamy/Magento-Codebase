<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_LazyLoad
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\LazyLoad\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context              $context       
     * @param \Magento\Framework\ObjectManagerInterface          $objectManager 
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager  
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig   
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
        ) {
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->_storeManager  = $storeManager;
        $this->_scopeConfig   = $context->getScopeConfig();
        $this->_request       = $context->getRequest();
    }

    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $result = $this->_scopeConfig->getValue(
            'loflazyload/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    public function isEnable() {
        $request = $this->_request;
        if ($request->getFullActionName() == 'cms_index_index' && !$this->getConfig('general/enable_homepage')) {
            return false;
        }
        if ($request->getFullActionName() == 'catalog_category_view' && !$this->getConfig('general/enable_categorypage')) {
            return false;
        }
        if ($request->getFullActionName() == 'checkout_cart_index' && !$this->getConfig('general/enable_cartpage')) {
            return false;
        }
        if ($request->getFullActionName() == 'catalogsearch_result_index' && !$this->getConfig('general/enable_searchpage')) {
            return false;
        }
        return true;
    }

    public function getMediaUrl() {
        $storeMediaUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
        ->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $storeMediaUrl;
    }

}
<?php
/**
* BSS Commerce Co.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://bsscommerce.com/Bss-Commerce-License.txt
*
* =================================================================
*                 MAGENTO EDITION USAGE NOTICE
* =================================================================
* This package designed for Magento COMMUNITY edition
* BSS Commerce does not guarantee correct work of this extension
* on any other Magento edition except Magento COMMUNITY edition.
* BSS Commerce does not provide extension support in case of
* incorrect edition usage.
* =================================================================
*
* @category   BSS
* @package    Bss_InfiniteScroll
* @author     Extension Team
* @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
* @license    http://bsscommerce.com/Bss-Commerce-License.txt
*/
namespace Bss\InfiniteScroll\Block;

use Magento\Framework\View\Element\Template;

use Magento\Framework\App\ObjectManager;

class InfiniteScroll extends Template
{
	protected $_config;
	protected $_gototop;
	protected $_btnload;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,       
        array $data = []
    ) {
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $objectManager = ObjectManager::getInstance();
		$scopeConfig = $objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
		$this->_config = $scopeConfig->getValue('infinitescroll/settings');
        $this->_gototop = $scopeConfig->getValue('infinitescroll/gototop');
        $this->_btnload = $scopeConfig->getValue('infinitescroll/btn_loadmore',$storeScope);
        parent::__construct($context, $data);
        $this->pageConfig->getTitle()->set(__('InfiniteScroll'));
    }

    public function getMediaUrl() 
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getConfig($config_path = '') 
    {
        return $this->_config[$config_path];
    }

    public function loadingIcon() {
		$loading = $this->getConfig('loading_icon');
		if($loading){
			return $this->getMediaUrl() .'infinitescroll/'. $loading;
		}
		return false;
	}

	public function getConfigGototop($config_path = '') {
		if($this->_gototop['enabled_gototop'])
			return $this->_gototop[$config_path];
		return false;
	}

	public function getConfigButton($config_path = '') {
		return $this->_btnload[$config_path];
	}
	public function getStoreName()
	{
		return $this->_storeManager->getStore()->getName();
	}
}
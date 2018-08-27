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
namespace Bss\InfiniteScroll\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_moduleManager;

	public function __construct(
		\Magento\Framework\Module\Manager $moduleManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
		) {
		$this->_moduleManager = $moduleManager;
		$this->_scopeConfig = $scopeConfig;
	}

    public function checkBssLazy() {
		return $this->_moduleManager->isEnabled('Bss_LazyImageLoader');
	}

	public function getExcludeCategory() {
		$category = $this->_scopeConfig->getValue('infinitescroll/settings/exclude_category', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if($category) {
			return explode(',', $category);
		}
		return false;
	}

	public function checkActive() 
    {
        $categoryExclude = $this->getExcludeCategory();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$category = $objectManager->get('Magento\Framework\Registry')->registry('current_category');
		$active = $this->_scopeConfig->getValue('infinitescroll/settings/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if($active && $category){
			$categoryIdParent = $category->getParentCategory()->getId();
			$categoryId = $category->getId();
			if(($categoryExclude && !in_array($categoryIdParent,$categoryExclude) && !in_array($categoryId,$categoryExclude)) || !$categoryExclude)
			return true;
		}
		return false;
    }

    public function enabledLazy() 
    {
    	return $this->_scopeConfig->getValue('infinitescroll/settings/active_lazy', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function getScrollText()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$category = $objectManager->get('Magento\Framework\Registry')->registry('current_category');
		$scrollText = __('Plus de ').$category->getName();
		return $scrollText;
	}
}
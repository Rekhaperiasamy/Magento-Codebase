<?php

namespace Orange\Catalog\Helper;

class CatalogUrl extends \Magento\Framework\App\Helper\AbstractHelper 
{

    protected $_scopeConfig;
	protected $_storeManager;

    public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager
    ) {        
	
		$this->_storeManager = $storeManager;
        $this->_scopeConfig = $context->getScopeConfig();
		parent::__construct($context);
    }
	
	/**
	 * Format the URL to add business keyword to SOHO customers
	 */
	public function getFormattedUrl($URL)
	{	
		if($this->getCustomerGroup() == 'SOHO')
		{			
			$parsedURL = parse_url($URL);
			$URLpath = $parsedURL['path'];
			$splitURL = explode("/",$URLpath);
			$storeCode = $this->getStoreCode();
			if($storeCode == 'nl') {
				$storeKey = 'zelfstandigen';//NL Store keyword
			} 
			else {
				$storeKey = 'independants';//FR Store Keyword
			}
			//$storeKey = 'business';
			$key = array_search($storeCode, $splitURL);
			$insertPos = $key+1;
			array_splice($splitURL, $insertPos , 0, array($storeKey));
			$formattedUrl = implode("/",$splitURL);
			
			return $formattedUrl;
		}
		return $URL;
	}
	
	/**
	 * Format the URL Path to add business keyword to SOHO customers
	 */
	 
	public function getFormattedURLPath($URL)
	{
		if($this->getCustomerGroup() == 'SOHO')
		{			
			$parsedURL = parse_url($URL);
			$URLpath = $parsedURL['path'];
			$splitURL = explode("/",$URLpath);
			$storeCode = $this->getStoreCode();
			if($storeCode == 'nl') {
				$storeKey = 'zelfstandigen';//NL Store keyword
			} 
			else {
				$storeKey = 'independants';//FR Store Keyword
			}
			
			$key = array_search($storeCode, $splitURL);
			$insertPos = $key+1;
			array_splice($splitURL, 0 , 0, array($storeKey));
			$formattedUrl = implode("/",$splitURL);
			
			return $formattedUrl;
		}
		return $URL;
	}
	
	private function getCustomerGroup()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerGroup = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();
		return $customerGroup;		
	}
	
	private function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }
	/**
	* Getting Color option TEXT 
	*/
	
	public function getColorOption($product)
    {
		$colorcode = $product->getColor();
		$attr = $product->getResource()->getAttribute('color');
		if ($attr->usesSource()) {
			$optionText = $attr->getSource()->getOptionText($colorcode);
		}
		return $optionText;
    }
}
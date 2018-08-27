<?php
namespace Orange\Priority\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper 
{
	const XML_PATH_ACCESSORY_POPULARITY_DURATION  = 'popularity/popularity_configuration/accessory_popularity';
	const XML_PATH_DEVICE_POPULARITY_DURATION  = 'popularity/popularity_configuration/handset_popularity';
	
	private $_scopeConfig;
	
    public function __construct(    
    	\Magento\Framework\App\Helper\Context $context
    ) {
    	$this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }
	
	/**
	 * Get Popularity Duration of Accessories
	 */
	public function getAccessoryPopularityDuration() 
	{
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		return $this->_scopeConfig->getValue(self::XML_PATH_ACCESSORY_POPULARITY_DURATION, $storeScope);
    }
	
	/**
	 * Get Popularity Duration of Devices
	 */
	public function getDevicePopularityDuration() 
	{
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		return $this->_scopeConfig->getValue(self::XML_PATH_DEVICE_POPULARITY_DURATION, $storeScope);
    }
	
}

<?php
namespace Orange\Customer\Model;

class Session extends \Magento\Customer\Model\Session 
{
	protected $_customergroups;
	protected $_objectmanager;
    /**
     * Get customer group id
     * If customer is not logged in system, 'not logged in' group id will be returned
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        /* if ($this->storage->getData('customer_group_id')) {
            return $this->storage->getData('customer_group_id');
        } */
        if ($this->getCustomerData()) {
            $customerGroupId = $this->getCustomerData()->getGroupId();
            $this->setCustomerGroupId($customerGroupId);
            return $customerGroupId;
        }
        /* return Group::NOT_LOGGED_IN_ID;
		$customerGroupId = $this->storage->getData('customer_group_id'); */		
		$customerGroupId = $this->getUserType();		
		$this->setCustomerGroupId($customerGroupId);			
		return $customerGroupId;		
    }
	private function getObjectManager()
	{
		if ($this->_objectmanager === null) {
			$this->_objectmanager = \Magento\Framework\App\ObjectManager::getInstance();			
		}
		return $this->_objectmanager; 
	}
	private function getAllCustomerGroups()
	{
		if ($this->_customergroups === null) {
			$objectManager = $this->getObjectManager();
			$this->_customergroups = $objectManager->get('\Magento\Customer\Model\Customer\Attribute\Source\Group')->getAllOptions();	
		}
		return $this->_customergroups; 
	}
	
	/** 
	 * SET customer group based on cookie value
	 */
	private function getUserType()
	{
		$objectManager = $this->getObjectManager();
		$urlInterface = $objectManager->get('Magento\Framework\UrlInterface');
		$storeUrl = $urlInterface->getCurrentUrl();
		$params = explode("/",$storeUrl);
		$urlInterface = $objectManager->get('Magento\Framework\App\RequestInterface');
		$segment = $urlInterface->getParam('segment');			
		$customerGroups = $this->getAllCustomerGroups();		
		$customerType = $this->getCustomerType();//Get Cookie							
		$customerGroupId = '';
		if(in_array('zelfstandigen',$params) || in_array('independants',$params)) {
			$key = array_search('SOHO', array_column($customerGroups, 'label'));
			$customerGroupId = $customerGroups[$key]['value'];				
			$this->storage->setData('customer_group_id', $customerGroupId);
			$this->storage->setData('customer_group_name', 'SOHO');
		}
		else {	
			if($customerType == 'SOHO') {
				if (strpos($storeUrl, '/cart/add/') !== false || strpos($storeUrl, '/cart/updatePost/') !== false || strpos($storeUrl, '/customer/section/load/') !== false || strpos($storeUrl, '/V1/guest-carts/') !== false || strpos($storeUrl, '/ogonecw/checkout/') !== false || strpos($storeUrl, '/banner/ajax/load/') !== false || strpos($storeUrl, '/checkout/index/quoteexpire/') !== false || strpos($storeUrl, '/checkout/index/vatnumber/') !== false || strpos($storeUrl, '/road/roadsixtyfive/roadsixtyfive/') !== false || strpos($storeUrl, '/road/scoring/scoring/') !== false || strpos($storeUrl, '/customer/section/load/') !== false || strpos($storeUrl, '/checkout/index/session/') !== false || strpos($storeUrl, '/numbervalidation/index/') !== false || strpos($storeUrl, '/checkout/index/simcard/') !== false || strpos($storeUrl, '/checkout/cart/delete/') !== false) {
					$key = array_search('SOHO', array_column($customerGroups, 'label'));
					$customerGroupId = $customerGroups[$key]['value'];				
					$this->storage->setData('customer_group_id', $customerGroupId);
					$this->storage->setData('customer_group_name', 'SOHO');
				}
				else {
					$customerGroupId = \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;			
					$this->storage->setData('customer_group_id', $customerGroupId);
					$this->storage->setData('customer_group_name', 'B2C');
				}
			}
			else {
				$customerGroupId = \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;			
				$this->storage->setData('customer_group_id', $customerGroupId);
				$this->storage->setData('customer_group_name', 'B2C');	
			}
		}					
		return $customerGroupId;		
	}
	
	public function getCustomerTypeName()
	{
		return $this->storage->getData('customer_group_name');
	}
	
	public function getCustomerTypeId()
	{
		return $this->storage->getData('customer_group_id');
	}
	
	public function setCustomerType($customertype)
	{
		$objectManager = $this->getObjectManager();
		$objectManager->get('Orange\Customer\Model\CustomerCookie')
			->set($customertype,86400);		
	}
	/**
	 * Retreive Cookie value
	 */
	public function getCustomerType()
	{
		$objectManager = $this->getObjectManager();
		$customerType = $objectManager->get('Orange\Customer\Model\CustomerCookie')
			->get();
		return $customerType;
	}
	
	public function resetCustomerType()
	{
		$objectManager = $this->getObjectManager();
		$customerType = $objectManager->get('Orange\Customer\Model\CustomerCookie')
			->delete();
	}
}



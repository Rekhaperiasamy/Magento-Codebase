<?php

namespace Orange\Bundle\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_scopeConfig;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
    }

    public function VirtualProductInfo($id) {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $om->create('Magento\Catalog\Model\Product')->load($id);

        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($id, true);
        $productIds = array();
        foreach ($requiredChildrenIds as $ids) {
            $productIds[] = $ids;
        }
        $productCollection = $om->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $productIds))
                ->addAttributeToFilter('type_id', 'virtual')
                ->load();

        $this->SessionInt()->setBundledProductIds($productIds);
        return $collection->getFirstItem();
    }
	
	public function SimpleProductInfo($id) {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $om->create('Magento\Catalog\Model\Product')->load($id);

        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($id, true);
        $productIds = array();
        foreach ($requiredChildrenIds as $ids) {
            $productIds[] = $ids;
        }
        $productCollection = $om->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $productIds))
                ->addAttributeToFilter('type_id', 'simple')
                ->load();

        $this->SessionInt()->setBundledProductIds($productIds);
        return $collection->getFirstItem();
    }

    public function productDetails($id) {

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $productCollection = $om->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $id))
                ->load();
		//$collection->addTierPriceData();		
        return $collection->getFirstItem();
    }

    public function simpleProductDetails($id) {

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $productCollection = $om->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $id))
                ->addAttributeToFilter('type_id', 'simple')
                ->load();

        return $collection->getFirstItem();
    }

    public function SessionInt() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $checkoutSession = $objectManager->get('Magento\Checkout\Model\Session');
        return $checkoutSession;
    }

    public function customerSessionInt() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        return $customerSession;
	}
	
	public function getCustomerGroup()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerGroupId = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeId();		
		return $customerGroupId;		
	}
	public function getCustomerTypeName()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerGroupName = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();		
		return $customerGroupName;		
	}
	
	public function getPriceLabel()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerGroup = $this->getCustomerTypeName();
		$priceLabel = $objectManager->create('Orange\Catalog\Model\PriceLabel')->getPriceLabel($customerGroup);
		return $priceLabel;		
	}
	
	/**
	 * Retrieve Bundle Price Tier Price using RAW Query
	 */ 
	public function getBundleTierPrice($productId)
	{		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerGroupId = $this->getCustomerGroup();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connectionDb = $resource->getConnection();
		$tableName =$resource->getTableName('catalog_product_index_price');		
		$sql = "Select final_price FROM " . $tableName . " WHERE customer_group_id = " . $customerGroupId . " AND entity_id=".$productId.";";
		$result = $connectionDb->fetchAll($sql);	
		return $result;
	}


}

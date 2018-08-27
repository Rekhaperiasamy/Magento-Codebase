<?php

/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Priority\Model;

use Magento\Framework\Exception\PriorityException;
use Magento\Framework\App\ObjectManager;

/**
 * Prioritytab priority model
 */
class Priority extends \Magento\Framework\Model\AbstractModel {

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    public function _construct() {
        $this->_init('Orange\Priority\Model\ResourceModel\Priority');
    }

    public function objectManager() {
        $objectManager = ObjectManager::getInstance();
        return $objectManager;
    }

    public function getProductPriorityValue($family, $store, $customerGroup) {
        $collection = $this->objectManager()->create('Orange\Priority\Model\Priority')->getCollection();
        $collection->addFilter('family_name', array('in' => $family))
                ->addFilter('store_id', array('in' => $store))
                ->addFilter('customer_group', array('in' => $customerGroup));
        return $collection;
    }
	// Get Popular product in Family
	public function getPopularProduct($family,$familyType,$storeId)
	{				
		if($familyType == 'accessories')
		{
			$popularityHours = $this->objectManager()->create('Orange\Priority\Helper\Data')->getAccessoryPopularityDuration();
			$familyId = 0;
		}
		else
		{
			$popularityHours = $this->objectManager()->create('Orange\Priority\Helper\Data')->getDevicePopularityDuration();
			$familyId = 1;
		}
		$today = time();					
		$last = $today - (60*60*$popularityHours);
		$from = date("Y-m-d", $last);

		$collection = $this->objectManager()->create('Orange\Priority\Model\Popularity')->getCollection();
        $collection->addFieldToFilter('family', array('in' => $family))
                ->addFieldToFilter('store_id', array('in' => $storeId))
				->addFieldToFilter('family_type', array('eq' => $familyId))
				->addFieldToFilter('logged_at', array('gteq' => $from));
		$collection ->getSelect()
                ->columns('COUNT(*) AS popularity')
                ->group('product_id')
				->order('popularity DESC');
		$popularItem = $collection->getFirstItem();
		if($popularItem->getProductId())
		{
			$popularItem = $this->objectManager()->create('Magento\Catalog\Model\Product')->load($popularItem->getProductId());
			return $popularItem;
		}
        return false;
	}

}

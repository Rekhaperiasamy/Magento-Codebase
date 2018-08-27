<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FME\Geoipultimatelock\Model;

use Magento\Framework\Model\AbstractModel;

class Rule extends \Magento\Rule\Model\AbstractModel
{

    /**
     * @var \FME\Geoipultimatelock\Model\Rule\Condition\CombineFactory
     */
    protected $_combineFactory;
    /**
     * @var \Magento\CatalogRule\Model\Rule\Action\CollectionFactory
     */
    protected $_actionCollectionFactory;
    /**#@+
     * Page's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \FME\Geoipultimatelock\Model\Rule\Condition\CombineFactory $combineFactory,
        \Magento\CatalogRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = array()
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
        
        $this->_combineFactory = $combineFactory;
        $this->_actionCollectionFactory = $actionCollectionFactory;
    }
    
    protected function _construct()
    {
        $this->_init('FME\Geoipultimatelock\Model\ResourceModel\Rule');
    }

    /**
     * Prepare rule's statuses.
     *
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return array(self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled'));
    }

    /**
     * Getter for rule conditions collection
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }
    
    /**
     * Getter for rule actions collection
     *
     * @return \Magento\CatalogRule\Model\Rule\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->_actionCollectionFactory->create();
    }
}

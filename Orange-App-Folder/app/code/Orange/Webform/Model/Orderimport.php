<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Webform\Model;

use Magento\Framework\Exception\StockException;
//use Psr\Log\LoggerInterface;

/**
 * Stocktab stock model
 */
class Orderimport extends \Magento\Framework\Model\AbstractModel
{
protected $productFactory;
//protected $logger;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
	   // LoggerInterface $logger,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,		
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);		
    }

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('Orange\Webform\Model\ResourceModel\Orderimport');
    }
	
   
}
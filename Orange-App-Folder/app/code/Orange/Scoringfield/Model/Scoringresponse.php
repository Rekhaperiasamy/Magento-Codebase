<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\Scoringfield\Model;

use Magento\Framework\Exception\PriorityException;
use Magento\Framework\App\ObjectManager;

/**
 * Prioritytab priority model
 */
class Scoringresponse extends \Magento\Framework\Model\AbstractModel {

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
        $this->_init('Orange\Scoringfield\Model\ResourceModel\Scoringresponse');
    }
}
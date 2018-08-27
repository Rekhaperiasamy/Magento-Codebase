<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Priority\Model;

use Magento\Framework\Exception\PriorityException;

/**
 * Prioritytab priority model
 */
class Popularity extends \Magento\Framework\Model\AbstractModel
{

    public function __construct(
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
        $this->_init('Orange\Priority\Model\ResourceModel\Popularity');
    }

   
}
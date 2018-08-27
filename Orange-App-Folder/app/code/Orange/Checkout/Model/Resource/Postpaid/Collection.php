<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Checkout\Model\Resource\Postpaid;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Orange\Checkout\Model\Postpaid', 'Orange\Checkout\Model\Resource\Postpaid');
    }
}

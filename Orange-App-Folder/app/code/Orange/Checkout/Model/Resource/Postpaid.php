<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Checkout\Model\Resource;

class Postpaid extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('orange_multiple_postpaid', 'entity_id');
    }
}

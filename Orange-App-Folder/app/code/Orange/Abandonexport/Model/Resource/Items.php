<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Abandonexport\Model\Resource;

class Items extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('orange_abandonexport_items', 'id');
    }
}

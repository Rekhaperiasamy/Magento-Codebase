<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\OutofstockReminder\Model\ResourceModel;

/**
 * OutofstockReminder resource
 */
class OutofstockReminder extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('outofstock_reminder', 'id');
    }

  
}

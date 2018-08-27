<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\Catalogversion\Model\ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
/**
 * Catalogversion resource
 */
class Catalogversion extends AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('catalogversion_catalogversion', 'id');
    }

  
}

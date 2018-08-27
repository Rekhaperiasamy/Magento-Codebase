<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\Catalogversion\Model\ResourceModel;

/**
 * Catalogversion resource
 */
class Catalogdraft extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('catalogversion_draft_schedule', 'id');
    }

  
}

<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\Reserve\Model\ResourceModel;

/**
 * Blacklist resource
 */
class Blacklist extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('reserve_blacklist', 'id');
    }

  
}

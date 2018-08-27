<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\Limitqty\Model\ResourceModel;

/**
 * Promo resource
 */
class Limitqty extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('limitqty_limitqty', 'id');
    }

  
}

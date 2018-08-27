<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\Errormessage\Model\ResourceModel;

/**
 * Promo resource
 */
class Errormessage extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('errormessage_errormessage', 'id');
    }

  
}

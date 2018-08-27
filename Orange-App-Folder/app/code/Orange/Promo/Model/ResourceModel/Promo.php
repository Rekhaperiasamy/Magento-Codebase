<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\Promo\Model\ResourceModel;

/**
 * Promo resource
 */
class Promo extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('promodescription_promotiondescription', 'id');
    }

  
}

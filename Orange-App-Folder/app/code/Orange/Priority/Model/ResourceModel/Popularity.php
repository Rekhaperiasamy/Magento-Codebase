<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\Priority\Model\ResourceModel;

/**
 * Priority resource
 */
class Popularity extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('catalog_product_family_popularity', 'id');
    }

  
}

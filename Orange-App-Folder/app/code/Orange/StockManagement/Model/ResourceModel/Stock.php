<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\StockManagement\Model\ResourceModel;

/**
 * Stock resource
 */
class Stock extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('stockmanagement_stock', 'id');
    }

  
}

<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\Webform\Model\ResourceModel;

/**
 * Stock resource
 */
class Activerform extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('webform_activerform', 'id');
    }

  
}

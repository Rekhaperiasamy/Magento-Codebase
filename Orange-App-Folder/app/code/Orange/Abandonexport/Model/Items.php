<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Abandonexport\Model;

class Items extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Orange\Abandonexport\Model\Resource\Items');
    }
}

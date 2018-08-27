<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Checkout\Model;

class Postpaid extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Orange\Checkout\Model\Resource\Postpaid');
    }
}

<?php
/**
 * Copyright © 2015 Bala. All rights reserved.
 */

namespace Orange\CouponReport\Model;

class Coupons extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Orange\CouponReport\Model\Resource\Coupons');
    }
}

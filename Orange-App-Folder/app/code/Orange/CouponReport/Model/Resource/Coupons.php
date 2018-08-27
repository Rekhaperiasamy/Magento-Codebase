<?php
/**
 * Copyright © 2015 Bala. All rights reserved.
 */

namespace Orange\CouponReport\Model\Resource;

class Coupons extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('orange_coupon_report', 'id');
    }
}

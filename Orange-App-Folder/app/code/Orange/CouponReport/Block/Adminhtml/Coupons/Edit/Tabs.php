<?php
/**
 * Copyright © 2015 Bala. All rights reserved.
 */
namespace Orange\CouponReport\Block\Adminhtml\Coupons\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('orange_couponreport_coupons_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Coupon Report'));
    }
}

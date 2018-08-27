<?php
/**
 * Copyright © 2015 Bala. All rights reserved.
 */
namespace Orange\CouponReport\Block\Adminhtml;

class Coupons extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'coupons';
        $this->_headerText = __('Coupons');
        $this->_addButtonLabel = __('Add New Coupons');
        parent::_construct();
    }
}

<?php
/**
 * Copyright © 2015 Bala. All rights reserved.
 */

namespace Orange\Couponreport\Controller\Adminhtml\Coupons;

class NewAction extends \Magento\Backend\App\Action
{

    public function execute()
    {
        $this->_forward('edit');
    }
}

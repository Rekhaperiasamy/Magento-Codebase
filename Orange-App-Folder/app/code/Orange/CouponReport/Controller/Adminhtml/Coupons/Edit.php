<?php
/**
 * Copyright © 2015 Bala. All rights reserved.
 */

namespace Orange\CouponReport\Controller\Adminhtml\Coupons;

class Edit extends \Magento\Backend\App\Action
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Orange\CouponReport\Model\Coupons');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('orange_couponreport/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_orange_couponreport_coupons', $model);
        $this->_initAction();
        $this->_view->getLayout()->getBlock('coupons_coupons_edit');
        $this->_view->renderLayout();
    }
}

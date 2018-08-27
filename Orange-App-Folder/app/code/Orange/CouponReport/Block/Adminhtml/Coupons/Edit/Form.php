<?php
/**
 * Copyright © 2015 Bala. All rights reserved.
 */
namespace Orange\CouponReport\Block\Adminhtml\Coupons\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('orange_couponreport_form');
        $this->setTitle(__('Coupon Information'));
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('orange_couponreport/coupons/save'),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}

<?php
/**
 * Copyright © 2015 Bala. All rights reserved.
 */

namespace Orange\CouponReport\Controller\Adminhtml\Coupons;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    
    protected $resultPageFactory = false;
    /**
     * Items list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected $resultPage;
    protected $coupons;
    
    public function __construct(\Orange\CouponReport\Model\Coupons $coupons, Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->coupons             = $coupons;
    }
    
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Orange_CouponReport::couponreport');
        $resultPage->getConfig()->getTitle()->prepend(__('Coupon Report'));
        $resultPage->addBreadcrumb(__('Orange'), __('Orange'));
        $resultPage->addBreadcrumb(__('Coupon Report'), __('Coupon Report'));
        return $resultPage;
    }
}

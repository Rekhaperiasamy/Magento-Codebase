<?php
/**
 * Copyright © 2015 Bala. All rights reserved.
 */

namespace Orange\Scoringfield\Controller\Adminhtml\Scoringresponse;

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
        $resultPage->setActiveMenu('Orange_Scoringfield::scoringresponse');
        $resultPage->getConfig()->getTitle()->prepend(__('WS Scoring Response Content Mapping'));
        $resultPage->addBreadcrumb(__('Orange'), __('Orange'));
        $resultPage->addBreadcrumb(__('Content Mapping'), __('Content Mapping'));
        return $resultPage;
    }
}

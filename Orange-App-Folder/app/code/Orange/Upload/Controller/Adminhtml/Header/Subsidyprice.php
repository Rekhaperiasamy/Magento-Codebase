<?php

namespace Orange\Upload\Controller\Adminhtml\Header;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Subsidyprice extends \Magento\Backend\App\Action {

    protected $resultPageFactory;
    protected $resultPage;

    public function __construct(
    Context $context, PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute() {
        $this->resultPage = $this->resultPageFactory->create();
        $this->resultPage->setActiveMenu('Orange_Priority::priority');
        $this->resultPage->getConfig()->getTitle()->set((__('Subsidy Product Import')));
        return $this->resultPage;
    }
    
    public function upload(){
        echo "test";
        exit;
    }

}

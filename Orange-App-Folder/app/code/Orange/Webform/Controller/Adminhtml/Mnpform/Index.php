<?php
namespace Orange\Webform\Controller\Adminhtml\Mnpform;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
class Index extends Action
{
    protected $resultPageFactory;
    public function __construct(\Magento\Backend\App\Action\Context $context,PageFactory $resultPageFactory)
    {
        
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
    }

    public function execute() {
        $this->resultPage = $this->resultPageFactory->create();
        $this->resultPage->setActiveMenu('orange_webform::orange_mnpform_index');
        $this->resultPage->getConfig()->getTitle()->set((__('MNPFORM')));
        return $this->resultPage;
    }
}

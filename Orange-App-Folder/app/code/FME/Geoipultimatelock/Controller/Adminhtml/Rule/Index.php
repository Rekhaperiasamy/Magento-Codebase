<?php

namespace FME\Geoipultimatelock\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Geoipultimatelock::rule');
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Geoipultimatelock::rule');
        $resultPage->addBreadcrumb(__('Geo IP Ultimate Lock'), __('Geo IP Ultimate Lock'));
        $resultPage->addBreadcrumb(__('Manage Geo IP Ultimate Lock'), __('Manage Rules'));
        $resultPage->getConfig()->getTitle()->prepend(__('Geo IP Ultimate Lock'));

        return $resultPage;
    }
}

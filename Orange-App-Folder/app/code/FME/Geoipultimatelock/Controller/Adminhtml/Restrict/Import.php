<?php

namespace FME\Geoipultimatelock\Controller\Adminhtml\Restrict;

class Import extends \Magento\Backend\App\Action
{

    protected $_geoipultimatelockHelper;
    protected $_resultPageFactory;
    protected $_resource;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \FME\Geoipultimatelock\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) 
{ 
     
        parent::__construct($context);
        $this->_geoipultimatelockHelper = $helper;
        $this->_resource = $resource;
        $this->_resultPageFactory = $pageFactory;
    }
    
    /**
     * @return void
     */
    public function execute()
    {

        $resource = $this->_resource;
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Geoipultimatelock::main_menu');
        $resultPage->getConfig()
                ->getTitle()
                ->prepend(__('Restrict IPs'));

        try {
            $this->messageManager->addSuccess(__(''));

            return $resultRedirect->setPath('*/*/index');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('*/*/index');
        }

        return $resultPage;
    }

    /**
     * News access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {

        return $this->_authorization->isAllowed('FME_Geoipultimatelock::import_start');
    }
}

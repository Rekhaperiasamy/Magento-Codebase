<?php
 
namespace FME\Geoipultimatelock\Controller\Adminhtml\Restrict;

 use \Magento\Framework\View\Result\PageFactory;
 
class Index extends \Magento\Backend\App\Action
{
    
    protected $_resultPageFactory;
    
    public function __construct(\Magento\Backend\App\Action\Context $context, PageFactory $pageFactory)
    {
        parent::__construct($context);
        $this->_resultPageFactory = $pageFactory;
    }
    /**
     * @return void
     */
    public function execute()
    {
       
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Geoipultimatelock::restrict_ip');
        $resultPage->getConfig()
                ->getTitle()
                ->prepend(__('Restrict IPs'));
 
        return $resultPage;
    }
   
   /**
    * News access rights checking
    *
    * @return bool
    */
    protected function _isAllowed()
    {
        
        return $this->_authorization->isAllowed('FME_Geoipultimatelock::restrict_ip');
    }
}

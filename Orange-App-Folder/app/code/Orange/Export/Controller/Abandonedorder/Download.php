<?php
namespace Orange\Export\Controller\Abandonedorder;

use Magento\Framework\App\Action\Context;
 
class Download extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
	protected $_abandonedOrderExport;
 
    public function __construct(
		Context $context, 
		\Magento\Framework\View\Result\PageFactory $resultPageFactory, 
		\Magento\Framework\View\Page\Config $pageConfig,
		\Orange\Export\Model\Abandonedorderexport $abandonedOrderExport
	)
    {
        $this->_resultPageFactory = $resultPageFactory;
		$this->_pageConfig = $pageConfig;  
		$this->_abandonedOrderExport = $abandonedOrderExport;
        parent::__construct($context);
    }
 
    public function execute()
    {	
		$from = $this->getRequest()->getParam('from');
		$to = $this->getRequest()->getParam('to');
		$limit = $this->getRequest()->getParam('limit');
		$page = $this->getRequest()->getParam('page');		
		$type = $this->getRequest()->getParam('type');				
			
		$downloadReport = $this->_abandonedOrderExport->generateReport($from,$to,$limit,$page,$type);
		
        $resultPage = $this->_resultPageFactory->create();		
        return $resultPage;
    }
}
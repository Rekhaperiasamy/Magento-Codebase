<?php
namespace Orange\Export\Controller\Abandonedorder;

use Magento\Framework\App\Action\Context;
 
class Generate extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
	protected $_abandonedOrderExport;
 
    public function __construct(
		Context $context, 
		\Magento\Framework\View\Result\PageFactory $resultPageFactory, 
		\Magento\Framework\View\Page\Config $pageConfig,
		\Orange\Export\Model\Abandonedorderexport $orderexport
	)
    {
        $this->_resultPageFactory = $resultPageFactory;
		$this->_pageConfig = $pageConfig;  
		$this->_abandonedOrderExport = $orderexport;
        parent::__construct($context);
    }
 
    public function execute()
    {
		$from = $this->getRequest()->getParam('from');
		$to = $this->getRequest()->getParam('to');
		$limit = $this->getRequest()->getParam('limit');
		$page = $this->getRequest()->getParam('page');		
		$type = $this->getRequest()->getParam('type');		
		$exportData = $this->_abandonedOrderExport->setExportData($from,$to,$limit,$page,$type);		
        $resultPage = $this->_resultPageFactory->create();		
        return $resultPage;
    }
}
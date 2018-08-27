<?php
namespace Orange\Export\Controller\Order;

use Magento\Framework\App\Action\Context;
 
class Generate extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
	protected $_orderExport;
 
    public function __construct(
		Context $context, 
		\Magento\Framework\View\Result\PageFactory $resultPageFactory, 
		\Magento\Framework\View\Page\Config $pageConfig,
		\Orange\Export\Model\Orderexport $orderexport
	)
    {
        $this->_resultPageFactory = $resultPageFactory;
		$this->_pageConfig = $pageConfig;  
		$this->_orderExport = $orderexport;
        parent::__construct($context);
    }
 
    public function execute()
    {	
		$from = $this->getRequest()->getParam('from');
		$to = $this->getRequest()->getParam('to');
		$limit = $this->getRequest()->getParam('limit');
		$page = $this->getRequest()->getParam('page');
		$status = $this->getRequest()->getParam('status');
		$type = $this->getRequest()->getParam('type');
		$exportData = $this->_orderExport->setExportData($from,$to,$status,$limit,$page,$type);
        $resultPage = $this->_resultPageFactory->create();		
        return $resultPage;
    }
}
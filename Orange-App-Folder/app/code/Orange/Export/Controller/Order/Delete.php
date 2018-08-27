<?php
namespace Orange\Export\Controller\Order;

use Magento\Framework\App\Action\Context;
 
class Delete extends \Magento\Framework\App\Action\Action
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
		$days = $this->getRequest()->getParam('days');
		$exportData = $this->_orderExport->flushOldData($days);	
        $resultPage = $this->_resultPageFactory->create();		
        return $resultPage;
    }
}
<?php
namespace Orange\Export\Controller\Abandonedorder;

use Magento\Framework\App\Action\Context;
 
class Delete extends \Magento\Framework\App\Action\Action
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
		$days = $this->getRequest()->getParam('days');
		$exportData = $this->_abandonedOrderExport->flushOldData($days);	
        $resultPage = $this->_resultPageFactory->create();		
        return $resultPage;
    }
}
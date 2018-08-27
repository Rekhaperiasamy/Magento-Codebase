<?php
namespace Orange\Export\Controller\Adminhtml\Abandonedorder;

class Generate extends \Magento\Framework\App\Action\Action
{
    /**
     * Page result factory
     * 
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Page factory
     * 
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $_resultPage;
	protected $_abandonedOrderExport;

    /**
     * constructor
     * 
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\App\Action\Context $context,
		\Orange\Export\Model\Abandonedorderexport $abandonedOrderExport
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
		$this->_abandonedOrderExport = $abandonedOrderExport;
        parent::__construct($context);
    }

    /**
     * execute the action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {		
		$from = $this->getRequest()->getParam('date_from');
		$to = $this->getRequest()->getParam('date_to');
		$limit = NULL;
		$page = NULL;		
		$type = 'admin-download';		
		$reportType = $this->getRequest()->getParam('report_type');
		if($reportType == 'month_report') {	
			$exportData = $this->_abandonedOrderExport->generateReport($from,$to,$limit,$page,$type,$reportType);
		}
		else {
			$currentDay = date("Y-m-d");
			//$currentDay = date("2017-11-29");		
			$exportData = $this->_abandonedOrderExport->setExportData($currentDay,$currentDay,$limit,$page,$type,$reportType);
		}
		
        $resultRedirect = $this->resultRedirectFactory->create();
		$resultRedirect->setPath('orangeexport/abandonedorder/index', ['_current' => true]);
		return $resultRedirect;
    }
}

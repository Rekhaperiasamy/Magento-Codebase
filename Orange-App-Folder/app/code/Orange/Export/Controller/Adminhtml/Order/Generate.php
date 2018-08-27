<?php
namespace Orange\Export\Controller\Adminhtml\Order;

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
	protected $_orderExport;

    /**
     * constructor
     * 
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\App\Action\Context $context,
		\Orange\Export\Model\Orderexport $orderexport
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
		$this->_orderExport = $orderexport;
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
		$status = $this->getRequest()->getParam('status');
		$type = 'admin-download';			
		$reportType = $this->getRequest()->getParam('report_type');
		if($reportType == 'month_report') {			
			$exportData = $this->_orderExport->generateReport($from,$to,$status,$limit,$page,$type,$reportType);
		}
		else {
			//pass current day as from and to
			$currentDay = date("Y-m-d");
			//$currentDay = date("2017-11-8");
			//$exportData = $this->_orderExport->setExportData($currentDay,$currentDay,$status,5,1,$type,$reportType);
			$exportData = $this->_orderExport->setExportData($currentDay,$currentDay,$status,$limit,$page,$type,$reportType);
		}
		
        $resultRedirect = $this->resultRedirectFactory->create();
		$resultRedirect->setPath('orangeexport/order/index', ['_current' => true]);
		return $resultRedirect;
    }
}

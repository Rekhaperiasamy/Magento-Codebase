<?php

namespace Orange\Export\Controller\Adminhtml\Order;


class Download extends \Magento\Backend\App\Action
{
    protected $_resultPageFactory;
    protected $_resultPage;
	protected $_exportHelper;
	
	public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\App\Action\Context $context,
		\Orange\Export\Helper\Data $exportHelper  
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
		$this->_exportHelper = $exportHelper;
        parent::__construct($context);
    }
	
    public function execute()
    {
        $file = $this->getRequest()->getParam('file');
		$reportPath = $this->_exportHelper->getOrderReportPath();
		$file = $reportPath.'/'.$file;
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Encoding: UTF-8');
			header('Content-Type: application/csv;charset=UTF-8');
			header('Content-Disposition: attachment; filename=' . basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			echo "\xEF\xBB\xBF"; // UTF-8 BOM
			readfile($file);
		}
		else {
			return;
		}
	}
}
?>
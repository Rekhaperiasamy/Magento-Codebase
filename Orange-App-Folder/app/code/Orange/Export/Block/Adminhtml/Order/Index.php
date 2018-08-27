<?php
/**
 * Copyright © 2015 Orange . All rights reserved.
 */
namespace Orange\Export\Block\Adminhtml\Order;
class Index extends \Magento\Framework\View\Element\Template
{
    protected $_storeManager; 
	protected $_exportHelper;	
    
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
	\Orange\Export\Helper\Data $exportHelper    
    ) {
        $this->_storeManager = $context->getStoreManager();
		$this->_exportHelper = $exportHelper;
        parent::__construct($context);
    }

    protected function _toHtml() {

        return parent::_toHtml();
    }
    
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
    
    public function getSaveUrl()
    {
        return $this->getUrl('orangeexport/order/generate',['type' => 'admin']);
    }
	
    public function getDownloadUrl($file)
    {
        return $this->getUrl('orangeexport/order/download/',['file' => $file]);
    }
    public function statusData() {
       $collect = "SELECT * FROM sales_order_status";
       $rest = $this->connectionEst()->fetchAll($collect);
       $status = array();
       foreach ($rest as $res){
           $status[$res['status']] = $res['label'];
       }       
       return $status;
    }
    public function connectionEst() {
        $resource = $this->objectManagerInt()->get('Magento\Framework\App\ResourceConnection');
        return $resource->getConnection();
    }
    
    public function objectManagerInt() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager;
    }
	
	public function getReportPath() {		
		$reportPath = $this->_exportHelper->getOrderReportPath();
		return $reportPath;
	}
}

<?php
namespace Orange\Export\Block\Adminhtml\AbandonedOrder;
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
        return $this->getUrl('orangeexport/abandonedorder/generate',['type' => 'admin']);
    }
	
    public function getDownloadUrl($file)
    {
        return $this->getUrl('orangeexport/abandonedorder/download/',['file' => $file]);
    }
	
	public function getReportPath() {		
		$reportPath = $this->_exportHelper->getAbandonedOrderReportPath();
		return $reportPath;
	}
}

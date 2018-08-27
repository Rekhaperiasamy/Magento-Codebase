<?php

namespace Orange\AdmincontentUpload\Block\Adminhtml;

class AdmincontentUpload extends \Magento\Framework\View\Element\Template {

    protected $_storeManager;  
    
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
	\Orange\Upload\Helper\Data $helper
    ) {
        $this->_storeManager = $context->getStoreManager();       
        parent::__construct($context);
      }
	/**
     * Server URL for File Upload
     **/
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/result');
    }
	/**
     * Download URL for NL
     **/
	 public function downloadnl()
    {
       return $this->getUrl('*/*/downloadnl');
    }
	/**
     * Download URL for FL
     **/
	 public function downloadfr()
    {
       return $this->getUrl('*/*/downloadfr');
    }
   

}

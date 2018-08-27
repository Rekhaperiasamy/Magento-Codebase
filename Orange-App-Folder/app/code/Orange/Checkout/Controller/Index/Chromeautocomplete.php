<?php

namespace Orange\Checkout\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Chromeautocomplete extends \Magento\Framework\App\Action\Action {

	protected $resultPageFactory;
	protected $resultJsonFactory;
	
	public function __construct(Context $context,\Magento\Framework\View\Result\PageFactory $resultPageFactory,\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory){
		$this->_resultPageFactory = $resultPageFactory;	
		$this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
    
	public function execute() {
		echo "update success"; 
    }
}

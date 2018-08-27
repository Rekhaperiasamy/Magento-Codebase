<?php

namespace Orange\Checkout\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Cancelorders extends \Magento\Framework\App\Action\Action {

	protected $resultPageFactory;
	protected $scopeConfig;
	
	public function __construct(Context $context,\Magento\Framework\View\Result\PageFactory $resultPageFactory,ScopeConfigInterface $scopeConfig
	){
		$this->_resultPageFactory = $resultPageFactory;
		$this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
    
	public function execute() {
	  //$orderId    =  $this->getRequest()->getParams(); 

      //$orderIdN   =  $orderId['orderid'];
      //$mulOrders  =  explode(",", $orderIdN);
 	  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	  $model = $objectManager->create('Orange\Checkout\Helper\Cancelorders')->execute();
	  exit;
    }
}

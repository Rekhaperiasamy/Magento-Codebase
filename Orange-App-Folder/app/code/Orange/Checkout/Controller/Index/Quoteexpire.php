<?php

namespace Orange\Checkout\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Quoteexpire extends \Magento\Framework\App\Action\Action {

	protected $resultPageFactory;
	protected $resultJsonFactory;
	
	public function __construct(Context $context,\Magento\Framework\View\Result\PageFactory $resultPageFactory,\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory){
		$this->_resultPageFactory = $resultPageFactory;	
		$this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
    
	public function execute() {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$quote = $objectManager->create('Magento\Checkout\Model\Cart')->getQuote();		
		return $this->resultJsonFactory->create()->setData($quote->getItemsCount());  
    }
}

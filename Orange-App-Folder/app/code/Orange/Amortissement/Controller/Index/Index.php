<?php

namespace Orange\Amortissement\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $_transportBuilder;
    protected $scopeConfig;
    protected $request;

    public function __construct(
    Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory,
	        \Magento\Framework\View\Page\Config $pageConfig,
            ScopeConfigInterface $scopeConfig) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $scopeConfig;
		$this->_pageConfig = $pageConfig; 
        parent::__construct($context);
        //$this->_transportBuilder = $transportBuilder;
    }

    public function execute() {
	   $amortissementId = $this->getRequest()->getParam('id');
	   $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	   if(isset($amortissementId) && intval($amortissementId)>0){
	      $product = $objectManager->create('Magento\Catalog\Model\Product')->load($amortissementId);
		  if ($product->getMetaTitle()) {
				$this->_pageConfig->getTitle()->set($product->getMetaTitle());
		  }
		  if ($product->getMetaKeyword()) {
				$this->_pageConfig->setKeywords($product->getMetaKeyword());
		  }
		  if ($product->getMetaDescription()) {
			    $this->_pageConfig->setDescription($product->getMetaDescription());
		  }
	   }
       return $this->_resultPageFactory->create();
    }

}

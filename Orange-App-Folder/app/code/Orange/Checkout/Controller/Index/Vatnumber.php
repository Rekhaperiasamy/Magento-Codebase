<?php

namespace Orange\Checkout\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Vatnumber extends \Magento\Framework\App\Action\Action {

	protected $resultPageFactory;
	protected $scopeConfig;
	
	public function __construct(Context $context,\Magento\Framework\View\Result\PageFactory $resultPageFactory,ScopeConfigInterface $scopeConfig
	){
		$this->_resultPageFactory = $resultPageFactory;
		$this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
    
	public function execute() {
	   $uName =  $this->scopeConfig->getValue('common/vatvalidation_configuration/vatnumber_username');
	   $pwd   =  $this->scopeConfig->getValue('common/vatvalidation_configuration/vatnumber_password');
	   
	   $vatNumber = trim($this->getRequest()->getParam('vat_number'));
	   $url = 'http://xml.b-information.be/scripts/arianeweb.dll/f/xml_mobistar?tvanum='.$vatNumber;
	   $context = stream_context_create(array(
		  'http' => array(
			'method'=>"GET",
			'header'  => "Authorization: Basic " . base64_encode($uName . ':' . $pwd),
		  )
		));
		$result = file_get_contents($url, false, $context);
	    $xml = simplexml_load_string($result);
		if (false === $xml) {
			die("Error: Cannot create object");
		} 
	    echo $xml->error->error_msg;
    }
}

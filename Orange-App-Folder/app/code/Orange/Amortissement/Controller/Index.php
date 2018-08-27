<?php

namespace Orange\Amortissement\Controller;

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
            ScopeConfigInterface $scopeConfig) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
        //$this->_transportBuilder = $transportBuilder;
    }

    public function execute() {
	   echo "hai";
	   exit;
       return $this->_resultPageFactory->create();
    }

}

<?php
namespace Orange\Webform\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ObjectManager;

class Test extends \Magento\Framework\App\Action\Action {
    protected $resultPageFactory;
	protected $scopeConfig;
	
	const SOHO_C_GRP = 'SOHO';
	 public function __construct(
	Context $context,
	 \Magento\Framework\View\Result\PageFactory $resultPageFactory,ScopeConfigInterface $scopeConfig,\Magento\Framework\App\ResourceConnection $resourceConnection
	){
		$this->_resultPageFactory = $resultPageFactory;
		$this->scopeConfig = $scopeConfig;
		$this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }
	public function execute(){
		echo 'Testing';
    }
	
}
<?php
namespace Orange\Webform\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\ScopeInterface;
class Success extends \Magento\Framework\App\Action\Action {
    protected $resultPageFactory;
	protected $scopeConfig;
	 public function __construct(
	Context $context,
	 \Magento\Framework\View\Result\PageFactory $resultPageFactory,ScopeConfigInterface $scopeConfig
	){
		$this->_resultPageFactory = $resultPageFactory;
		$this->scopeConfig = $scopeConfig;
        parent::__construct($context);
		//$this->_transportBuilder = $transportBuilder;
    }
	public function execute(){
		
		$resultPage = $this->_resultPageFactory->create();
				$resultPage->addHandle('webform_index_success');
				return $resultPage;
		
    }
	
	}
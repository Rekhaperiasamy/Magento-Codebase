<?php

namespace Orange\Export\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	const XML_PATH_FRONTEND_EMAIL_TEMPLATE_ID  = 'report_exception_email_frontend';
	const XML_PATH_BACKEND_EMAIL_TEMPLATE_ID  = 'report_exception_email_backend';
    const XML_PATH_STORE_EMAIL = 'trans_email/ident_sales/email';
	const XML_PATH_EXCEPTION_RECEIVER_EMAIL = 'common/report_exception_configuration/report_exception_notification_email';
	
	protected $_directoryList;	
	protected $_storeManager;	
	protected $inlineTranslation;
	protected $_transportBuilder;
	protected $temp_id;
	protected $_appState;
	
        
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\App\Filesystem\DirectoryList $directoryList,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		\Magento\Framework\App\State $appState
        
    ){
		$this->_directoryList = $directoryList;		
		$this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
		$this->_appState = $appState;
		parent::__construct($context);
    }
	
	/**
     * Return Order Report Download Path
     *          
     * @return mixed
     */
	public function getOrderReportPath() {
		$varPath = $this->_directoryList->getPath('var');
		$reportPath = $varPath."/export/ordersreport";
		return $reportPath;
	}
	
	/**
     * Return Abandoned Order Report Download Path
     *          
     * @return mixed
     */
	public function getAbandonedOrderReportPath() {
		$varPath = $this->_directoryList->getPath('var');
		$reportPath = $varPath."/export/abandonedreport";
		return $reportPath;
	}
	
	/**
     * Return SOHO price 
     *     
     * @param int $originalPrice
     * @return int $sohoPrice
     */
	public function getSohoPrice($originalPrice) {
		if($originalPrice!='') {
			$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
			$discountSohoPrice = $this->scopeConfig->getValue('soho/soho_configuration/soho_discount', $storeScope);		
			$sohoPrice = $originalPrice / (1+($discountSohoPrice/100));
			return $sohoPrice;
		}
		return;
	}
	
	/**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
 
    /**
     * Return store 
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }
	
	/**
     * Return store email id according to store
     *
     * @return mixed
     */
    public function getStoreEmailId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }
	
	/**
     * Return receiver email id according to store
     *
     * @return mixed
     */
    public function getReportExceptionReceiver($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }
 
    /**
     * [generateTemplate description]  with template file and tempaltes variables values                
     * @param  Mixed $emailTemplateVariables 
     * @param  Mixed $senderInfo             
     * @param  Mixed $receiverInfo           
     * @return void
     */
    public function generateTemplate($emailTemplateVariables,$senderEmail,$receiverInfo)
    {
		/* Sender Detail  */
		$senderInfo['name'] = 'Orange Support';
		$senderInfo['email'] = $senderEmail;
		
        $template =  $this->_transportBuilder->setTemplateIdentifier($this->temp_id)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, /* here you can define area and
                                                                                 store of template for which you prepare it */
                        'store' => $this->_storeManager->getStore()->getId()						
                    ]
                )
                ->setTemplateVars($emailTemplateVariables)				
                ->setFrom($senderInfo)
                ->addTo($receiverInfo,$receiverInfo);
        return $this;        
    }
 
    /**                 
     * @param  Mixed $emailTemplateVariables 
     * @param  Mixed $senderInfo             
     * @param  Mixed $receiverInfo           
     * @return void
     */    
    public function sendReportExceptionEmail($emailTemplateVariables)
    {				
		$senderInfo = $this->getStoreEmailId(self::XML_PATH_STORE_EMAIL);
		$receiverInfo = $this->getReportExceptionReceiver(self::XML_PATH_EXCEPTION_RECEIVER_EMAIL);
		if($this->_appState->getAreaCode() == 'frontend') {				
			$this->temp_id = self::XML_PATH_FRONTEND_EMAIL_TEMPLATE_ID;
		}
		else {				
			$this->temp_id = self::XML_PATH_BACKEND_EMAIL_TEMPLATE_ID;
		}
		
		$this->inlineTranslation->suspend(); 
		$emailReceivers = explode(';',$receiverInfo);		
		foreach($emailReceivers as $receiverInfo) {
			$this->generateTemplate($emailTemplateVariables,$senderInfo,$receiverInfo);    
			$transport = $this->_transportBuilder->getTransport();
			$transport->sendMessage(); 
		}     
		$this->inlineTranslation->resume();
    }
	 
}
<?php

namespace Orange\Emails\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\Sender;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Framework\Event\ManagerInterface;

class orderMail extends \Magento\Framework\App\Helper\AbstractHelper {

    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'entra/mail/entra_email_template';

    /* Here section and group refer to name of section and group where you create this field in configuration */

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    protected $_logger;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var string
     */
    protected $temp_id;

    /**
     * @param Magento\Framework\App\Helper\Context $context
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
    \Magento\Framework\App\Helper\Context $context,\Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Sales\Model\Order $_orderModel,\Magento\Framework\File\Csv $csvProcessor
    
    ) {
        $this->_scopeConfig = $context;
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_logger = $context->getLogger();
        $this->_storeManager = $storeManager;
        $this->csvProcessor = $csvProcessor;
        $this->scopeConfig = $context->getScopeConfig();
        $this->_orderModel = $_orderModel;
    }

    protected function getConfigValue($path, $storeId) {
        return $this->scopeConfig->getValue(
                        $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getStore() {
        return $this->_storeManager->getStore();
    }

    public function entraCredentials() {
        $Credentials = array();
        $Credentials['Entra_EmailSender'] = $this->scopeConfig->getValue('entra/entra_configuration/entra_sender', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Entra_EmailSenderName'] = $this->scopeConfig->getValue('entra/entra_configuration/entra_sender_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Entra_EmailReciever'] = $this->scopeConfig->getValue('entra/entra_configuration/entra_reciever', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Entra_Prmotion_Message'] = $this->scopeConfig->getValue('entra/entra_configuration/promotion_block_entra', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Entra_Email_Reciever_Cc'] = $this->scopeConfig->getValue('entra/entra_configuration/entra_reciever_cc', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $Credentials;
    }

    public function entraMailProcess($orderId) {		
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = array();
        $order = $objectManager->create('Magento\Sales\Model\Order')->load($orderId, 'increment_id');		
        $items = $order->getAllItems();
        $info = $this->entraCredentials();
        $promotions = $info['Entra_Prmotion_Message'];
        if ($order->getShippingAddress() != "") {
            $shippingAddress = $order->getShippingAddress();
        } else {
            $shippingAddress = $order->getBillingAddress();
        }         

        // Getting store id
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $store = $storeManager->getStore();
        $storeId = $store->getStoreId();    
        /* Receiver Detail  */
        $info = $this->entraCredentials();
        $senderName = $this->scopeConfig->getValue('trans_email/ident_sales/name');
        $senderMail = $this->scopeConfig->getValue('trans_email/ident_sales/email');
        $reciever = $shippingAddress->getEmail(); 
		$senderInfo = [
                'name'  => $senderName,
                'email' => $senderMail,
            ];
         $receiverInfo = [
                'email' => $reciever,
            ];
        
           
		//if($order->getStatus() == 'pending_payment'){
			$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());
			$emailTempVariables = array('order' => $order,'formattedShippingAddress' => 'User','billing' => $order->getBillingAddress(),'store' => $order->getStore());      
			$templateVars = $emailTempVariables;        
			$from = $senderInfo;
			$this->inlineTranslation->suspend();
			$to = $receiverInfo;
			$transport = //$this->_transportBuilder->setTemplateIdentifier('sales_email_order_template')
			$this->_transportBuilder->setTemplateIdentifier('sales_email_order_guest_template')		
					->setTemplateOptions($templateOptions)
					->setTemplateVars($emailTempVariables)
					->setFrom($from)
					->addTo($to)					
					->getTransport();
			$transport->sendMessage();
			$this->inlineTranslation->resume();
		//}		
    }

}

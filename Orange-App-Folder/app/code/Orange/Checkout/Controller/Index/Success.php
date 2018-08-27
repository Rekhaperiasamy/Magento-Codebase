<?php
namespace Orange\Checkout\Controller\Index;
use Orange\Selligent\Helper\Selligent;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order;
class Success extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
	protected $checkoutSession;
	protected $coreRegistry;
	protected $_catalogHelper;
	
	public function __construct(
       \Magento\Framework\App\Action\Context $context,
	    \Magento\Framework\App\ResourceConnection $resourceConnection,
	   \Magento\Checkout\Model\Session $checkoutSession,
	   \Magento\Framework\Registry $coreRegistry,	   
	   \Magento\Framework\View\Result\PageFactory $resultPageFactory,
	   \Orange\Catalog\Helper\CatalogUrl $catalogHelper
    ) {
        parent::__construct($context);
		$this->_checkoutSession = $checkoutSession;
		$this->_coreRegistry = $coreRegistry;
		$this->resourceConnection = $resourceConnection;
        $this->resultPageFactory = $resultPageFactory;
		$this->_catalogHelper = $catalogHelper;	
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute() {
		$incrementId = $this->_checkoutSession->getLastRealOrderId();
        $objectManager = ObjectManager::getInstance();
        if ($this->_checkoutSession->getOgoneTranscationid()) {
            $transcation_id = $this->_checkoutSession->getOgoneTranscationid();
        } else {
            $transcation_id = 0;
        }
		
		if($incrementId == '' && $this->_checkoutSession->getOgoneOrderid()){
			
			$incrementId = $this->_checkoutSession->getOgoneOrderid();
			$objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/order_ogone_responce.log', "order id ========>".$incrementId);
		}
		
        //$objectManager = ObjectManager::getInstance();
		
		/** FIX for ogone back btn don't remove this block temporarily commented */
		/* $lastOrderCookie = $objectManager->get('Orange\Customer\Model\CustomerCookie')->getLastOrderCookie();		
		$incrementId = ($incrementId!='' ? $incrementId : $lastOrderCookie);	
		$objectManager->get('Psr\Log\LoggerInterface')->addDebug('lastsuccesspaymentIncrementId:'.$incrementId); 
		if($incrementId || $incrementId!='') { */
		$logCondition = $incrementId."order checkout controller success page";
		$objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/order_ogone_responce.log', $logCondition);
		
		
		
		
		if($incrementId) {
			$billingId = $objectManager->get('Orange\Checkout\Model\Success')->getOrder($incrementId);	
			$tempSession = $objectManager->get('Magento\Checkout\Model\Session')->getNewcheckout();
		   	$orderD = $objectManager->create('Magento\Sales\Model\Order')->load($incrementId, 'increment_id');
            $state = $orderD->getState();
            $canceleld = Order::STATE_CANCELED;
            if ($state == $canceleld) {
                $RedirectUrl = $this->_catalogHelper->getFormattedURLPath('checkout/cart'); //Format SOHO URL					
			    return $this->_redirect($RedirectUrl, ['_secure' => true]);	
			}
			$model = $objectManager->create('Orange\Abandonexport\Model\Items');
			$abandonexport = $model->getCollection()->addFieldToFilter('quote_id',$orderD->getQuoteId());
			$tempSession = unserialize($abandonexport->getFirstItem()->getStepsecond());
			$gender = $tempSession['gender'];				
			if(isset($tempSession['scoringResponse']) && $tempSession['scoringResponse']){
				$scoringResponse = $tempSession['scoringResponse'];
			} else{
				$scoringResponse = '';
			}					
		
			$orderD->setScore($scoringResponse);				
			$orderD->save();		
			
			/* $objectManager = ObjectManager::getInstance();
		    $orderD = $objectManager->create('Magento\Sales\Model\Order')->load($incrementId, 'increment_id');
			$objectManager->get('Orange\Emails\Helper\Emails')->entraMailProcess($orderD); 
		    $objectManager->get('Orange\Selligent\Helper\Selligent')->doSelligentCall($orderD); */
			
			$this->_coreRegistry->register('OrderId', $incrementId);
			$this->_checkoutSession->clearHelperData();
            if (isset($tempSession['prepaidbir']) && $tempSession['prepaidbir'] == "1") {
                $objectManager->get('Orange\Selligent\Helper\Selligent')->doSelligentCall($orderD, $incrementId, $transcation_id);
			}			
			$objectManager->get('Orange\Emails\Helper\Emails')->entraMailProcess($incrementId,$scoringResponse); 
		   /*Commented for Paid Orders */	//$objectManager->get('Orange\Emails\Helper\orderMail')->entraMailProcess($incrementId, 'increment_id'); 
			$objectManager->get('Magento\Checkout\Model\Session')->unsNewcheckout();
			if($objectManager->get('Magento\Checkout\Model\Session')->getRemovedCartItem()) {
				$objectManager->get('Magento\Checkout\Model\Session')->unsRemovedCartItem();
			}
			$objectManager->get('Orange\Customer\Model\CustomerCookie')->orderCookiedelete();
			unset($incrementId);
            $this->_checkoutSession->setOgoneOrderid('');
            $this->_checkoutSession->setOgoneTranscationid('');
			$this->resultPage = $this->resultPageFactory->create();

			return $this->resultPage;
        } else {
			$objectManager->get('Orange\Customer\Model\CustomerCookie')->orderCookiedelete();
			$RedirectUrl = $this->_catalogHelper->getFormattedURLPath('checkout/cart');	//Format SOHO URL					
			return $this->_redirect($RedirectUrl, ['_secure' => true]);			
		}
    }
	
	 public function boxData($val, $qid) {
        $collect1 = "SELECT * FROM orange_abandonexport_items where quote_id='" . $qid . "'";
        $rest1 = $this->connectionEst()->fetchAll($collect1);
  
    }
	
	public function objectManagerInt() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager;
    }
	
	public function connectionEst() {
        $resource = $this->objectManagerInt()->get('Magento\Framework\App\ResourceConnection');
        return $resource->getConnection();
    }
}
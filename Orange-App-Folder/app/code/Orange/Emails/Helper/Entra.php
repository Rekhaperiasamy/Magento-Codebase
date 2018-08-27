<?php
namespace Orange\Emails\Helper;
 
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Mail\Template\TransportBuilder;

class Entra extends \Magento\Framework\App\Helper\AbstractHelper {
 
    protected $_logger;
	protected $_orderCollectionFactory;
	protected $_mailHelper;
	protected $_logHelper;
 
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
		\Orange\Emails\Helper\Emails $mailHelper,
		\Orange\Upload\Helper\Data $logHelper
    ) {
        $this->_logger = $logger;
		$this->_orderCollectionFactory = $orderCollectionFactory;
		$this->_mailHelper = $mailHelper;
		$this->_logHelper = $logHelper;
    }
    
    /**
     * Method executed when cron runs in server
     */
    public function execute() {        
		
		/**
		 * Get all orders for last 1 hour
		 * Get the scoring response
		 * Trigger entra mail with scoring response and order increment id as param
		 */
		$time = time();
		$dateEnd = date('Y-m-d H:i:s', $time);		
		$lastTime = $time - 3600; // 60*60
		$dateStart = date('Y-m-d H:i:s', $lastTime);
		
		$orders = $this->_orderCollectionFactory->create()->addAttributeToSelect('*')
		            ->addFieldToFilter('entra_status','0')
					->addFieldToFilter('created_at', array('from' => $dateStart, 'to' => $dateEnd));
		$ob = \Magento\Framework\App\ObjectManager::getInstance();
                $storeConfig = $ob->create('Magento\Framework\App\Config\ScopeConfigInterface');
                $log_mode = $storeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if(isset($log_mode) && $log_mode==1){
                    $this->_logHelper->logCreate('/var/log/entra_mail_cron.log','Entra Mail CRON Triggered');
                }
		foreach($orders as $order):
			//if($order->getEntraStatus() == 0 && ($order->getScore() == 'ACC' || $order->getScore() == 'REF')):
			if($order->getEntraStatus() == 0 && ($order->getStatus() == 'complete' || $order->getStatus() == 'processing')):
				/** Since there is no condition for triggering entra mail, mailing script will be called for last 1 hour orders with Entra status 0 **/
                                if(isset($log_mode) && $log_mode==1){
                                    $this->_logHelper->logCreate('/var/log/entra_mail_cron.log','Running Entra Mail Cron for the order :'.$order->getIncrementId().' with scoring response '.$order->getScore());				
                                }
				$this->_mailHelper->entraMailProcess($order->getIncrementId(),$order->getScore());//Trigger Entra mail process
				if(isset($log_mode) && $log_mode==1){
                                    $this->_logHelper->logCreate('/var/log/entra_mail_cron.log','Completed Entra Mail Cron for the order :'.$order->getIncrementId().' with scoring response '.$order->getScore());				
                                }
			endif;
		endforeach;
        return $this;
    }
}
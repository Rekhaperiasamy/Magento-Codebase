<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\Checkout\Helper;

use Magento\Store\Model\StoresConfig;
use Magento\Sales\Model\Order;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Mail\Template\TransportBuilder;
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;

class Cancelorders extends \Magento\Framework\App\Helper\AbstractHelper 
{
    /**
     * @var StoresConfig
     */
    protected $storesConfig;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param StoresConfig $storesConfig
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     */
	 protected $orderManagement; 
	 protected $_scopeConfig;
    public function __construct(
        StoresConfig $storesConfig,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
		\Magento\Sales\Api\OrderManagementInterface $orderManagement ,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Orange\Upload\Helper\Data $data		
    ) {
        $this->storesConfig = $storesConfig;
        $this->logger = $logger;
        $this->orderCollectionFactory = $collectionFactory;
		$this->orderManagement = $orderManagement;
		$this->_scopeConfig = $scopeConfig;	
		$this->_cancellog 			= $data;
		$this->_logFile 			= '/var/log/cancelorder.log';
    }

    /**
     * Clean expired quotes (cron process)
     *
     * @return void
     */
	public function execute()
	{

	// Change default magento cron order clean up code due to stock issue ( increment in stock issue ) //
		$lifetimes = $this->storesConfig->getStoresConfigByPath('sales/orders/delete_pending_after');
         //Configuration Yes or No to run the Cron
		 $cronConf = $this->_scopeConfig->getValue('cancel_order_test/test/yesno_source_model', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		 //Configuration Time For Pending Orders
		 $cronTime = $this->_scopeConfig->getValue('cancelcrontime/cancelcrontime_configuration/crontime', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	    if($cronConf == 1)
		{
		
			foreach ($lifetimes as $storeId => $lifetime) {
			
			$orders = $this->orderCollectionFactory->create()->addAttributeToSelect('*');
			$orders->addFieldToFilter('store_id', $storeId);
			$orders->addFieldToFilter('status', Order::STATE_PENDING_PAYMENT);
			$orders->getSelect()->where(
							new \Zend_Db_Expr('TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP, `updated_at`)) >= ' . $cronTime * 60)
			);
			
			 $this->_cancellog->logCreate($this->_logFile, 'Cancel orders Started'.count($orders));
			if(count($orders))
			{
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			foreach($orders as $order){        
				try {

				  // $order->cancel($order->getId());  
				   //$order->save($order->getId());
					$this->orderManagement->cancel($order->getEntityId()); 
					$this->_cancellog->logCreate($this->_logFile, 'Cancel orders'.$order->getEntityId()); 					
					$this->_couponHelper = $objectManager->create('Orange\Coupon\Helper\Data');
					/** Reactivate Coupon **/
					$this->_couponHelper->reactivateCouponByOrder($order->getIncrementId());
				} catch (\Exception $e) {
							   $this->logger->error('Error cancelling deprecated orders: ' .$order->getEntityId().'-'. $e->getTraceAsString());
								
								$this->logger->error('Error cancelling deprecated orders: ' .$order->getEntityId().'-'. $e->getMessage());
				}
			}
			
			}
		}              
        }
	}


}

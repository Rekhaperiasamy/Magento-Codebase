<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\Checkout\Model\Order\Email\Sender;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\Sender;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class OrderSender
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{

    /**
     * Sends order email to the customer.
     *
     * Email will be sent immediately in two cases:
     *
     * - if asynchronous email sending is disabled in global settings
     * - if $forceSyncMode parameter is set to TRUE
     *
     * Otherwise, email will be sent later during running of
     * corresponding cron job.
     *
     * @param Order $order
     * @param bool $forceSyncMode
     * @return bool
     */
    public function send(Order $order, $forceSyncMode = false)
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$order_email = true;
		$logFile 			= '/var/log/confmail.log';
		$objectManager->create('Orange\Upload\Helper\Data')->logCreate($logFile, 'Log file for Mail Order Started'.$order->getStatus().'=='.$order->getIncrementId());  
		/*Stops COnfirmation Mail for Pending and Payment Review Status*/
		if($order->getStatus() == 'payment_review' || $order->getStatus() == 'pending_payment'){
           $order_email = false;
		   $objectManager->create('Orange\Upload\Helper\Data')->logCreate($logFile, 'Log file for Mail Order Stoped'.$order->getStatus().'=='.$order->getIncrementId());
           return false;
		}
		
      if($order_email == true )
      {	
        $objectManager->create('Orange\Upload\Helper\Data')->logCreate($logFile, 'Log file for Mail Order Ended'.$order->getStatus().'=='.$order->getIncrementId());  	  
		$order->setSendEmail(true);

        if (!$this->globalConfig->getValue('sales_email/general/async_sending') || $forceSyncMode) {
            if ($this->checkAndSend($order)) {
                $order->setEmailSent(true);
                $this->orderResource->saveAttribute($order, ['send_email', 'email_sent']);
                return true;
            }
        }

        $this->orderResource->saveAttribute($order, 'send_email');

        return false;
	  }	
    }


   
}

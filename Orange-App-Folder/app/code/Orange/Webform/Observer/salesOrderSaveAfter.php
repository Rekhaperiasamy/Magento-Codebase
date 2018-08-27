<?php
namespace Orange\Webform\Observer;

use Magento\Framework\Event\ObserverInterface;

class salesOrderSaveAfter implements ObserverInterface
{
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		/* @var $order \Magento\Sales\Model\Order */
		$order = $observer->getEvent()->getOrder();
		$order->setBiTrackingFlag('0');
		$order->save(); 
	}
}
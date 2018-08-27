<?php
namespace Orange\Checkout\Observer;

use Magento\Framework\Event\ObserverInterface;

class QuoteSubmitBeforeSaveObserver implements ObserverInterface
{	
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$observer->getEvent()->getOrder()->setSubscriptionTotal($observer->getEvent()->getQuote()->getSubscriptionTotal());
		$observer->getEvent()->getOrder()->setSubsidyDiscount($observer->getEvent()->getQuote()->getSubsidyDiscount());
		$observer->getEvent()->getOrder()->setOriSubscriptionTotal($observer->getEvent()->getQuote()->getOriSubscriptionTotal());
		$observer->getEvent()->getOrder()->setCouponDescription($observer->getEvent()->getQuote()->getCouponDescription());
		$observer->getEvent()->getOrder()->setAccountNumber($observer->getEvent()->getQuote()->getAccountNumber());
		return $this;
	}
}
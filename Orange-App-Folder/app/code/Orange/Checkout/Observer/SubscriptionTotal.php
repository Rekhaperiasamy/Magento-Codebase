<?php
namespace Orange\Checkout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class SubscriptionTotal implements ObserverInterface
{
	public function __construct (
    \Magento\Checkout\Model\Session $_checkoutSession
    ) {
		$this->_checkoutSession = $_checkoutSession;
	}
	
	public function execute(\Magento\Framework\Event\Observer $observer) {
		$quote=$observer->getEvent()->getQuote();
		$cartData = $this->_checkoutSession->getQuote()->getAllItems();
		$subscriptionAmount = 0;
		$OrisubscriptionAmount = 0;	
		foreach ($cartData as $item) {	
            $qty = $item->getQty();
			if ($item->getParentItem()) {
				$qty = $item->getParentItem()->getQty();
			}
			$subscriptionAmount += $item->getSubscriptionAmount() * $qty;
			$OrisubscriptionAmount += $item->getOriSubscriptionAmount() * $qty;
		}
		$quote->setSubscriptionTotal($subscriptionAmount);
		$quote->setOriSubscriptionTotal($OrisubscriptionAmount);
		if(count($cartData) == 0) {
			$this->_checkoutSession->unsNewcheckout(); //Clear custom session when cart empty
		}
	}
}
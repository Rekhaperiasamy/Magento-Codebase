<?php
namespace Orange\Checkout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ObjectManager;

class SubscriptionAmount implements ObserverInterface
{
	public function execute(\Magento\Framework\Event\Observer $observer) {
		$item = $observer->getEvent()->getQuoteItem();
		$product = $item->getProduct();
		$customerType = $this->getCustomerTypeName();
		$item->setEagleType($product->getAttributeText('eagle_type'));
		if($customerType == 'SOHO'):
			$subscriptionAmount = $product->getSubscriptionAmount();			
			if($item->getProductType() == 'bundle'):
				if($product->getSohoPrice()) {
					$sohoPrice = $product->getSohoPrice();
					$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					$this->_objectManager->get('Psr\Log\LoggerInterface')->addDebug('SOHO PRICE:'.$product->getSohoPrice());
					$item->setCustomPrice($sohoPrice);
					$item->setOriginalCustomPrice($sohoPrice);
				}		
			endif;		
		else:
			$subscriptionAmount = $product->getSubscriptionAmount();
		endif;
		if($subscriptionAmount && $item->getProductType() == "virtual") {
			$item->setSubscriptionAmount($subscriptionAmount);
			$item->setOriSubscriptionAmount($subscriptionAmount);			
		}		
	}
	private function getCustomerTypeName()
	{
		$objectManager = ObjectManager::getInstance();
		$urlInterface = $objectManager->get('Magento\Framework\App\RequestInterface');
		$segment = $urlInterface->getParam('segment');
		$customerGroupName = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();			
		//if($segment) {
			if($segment == 'zelfstandigen' || $segment == 'independants'):
				$objectManager->create('Orange\Customer\Model\CustomerCookie')->set('SOHO');//SET Cookie(Need to remove after drupal integration)
			else:				
				// if($customerGroupName == 'SOHO'):
					// $objectManager->create('Orange\Customer\Model\CustomerCookie')->set('SOHO');//SET Cookie(Need to remove after drupal integration)
				// else:
					//$objectManager->create('Orange\Customer\Model\CustomerCookie')->set('res');//SET Cookie(Need to remove after drupal integration)
				//endif;
			endif;
		//}
		$customerGroupName = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();					
		return $customerGroupName;		
	}
}
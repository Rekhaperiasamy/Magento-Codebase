<?php
/**
 * Created by: IWD Agency "iwdagency.com"
 * Developer: Andrew Chornij "iwd.andrew@gmail.com"
 * Date: 12.01.2016
 */

namespace Orange\Checkout\Controller\Index;

class Index extends \Magento\Checkout\Controller\Index\Index
{
	public function execute()
    {
		$this->_objectManager->get('Psr\Log\LoggerInterface')->addDebug('Test Checkout');
        /** @var \Magento\Checkout\Helper\Data $checkoutHelper */
        $checkoutHelper = $this->_objectManager->get('Magento\Checkout\Helper\Data');
        if (!$checkoutHelper->canOnepageCheckout()) {
            $this->messageManager->addError(__('One-page checkout is turned off.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
       
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
			
			/** FIX for ogone back btn don't remove this block temporarily commented */
            /* $restoreQuote = $this->restoreQuote();
			if($restoreQuote) {
				$resultPage = $this->resultPageFactory->create();
				$resultPage->getConfig()->getTitle()->set(__('Checkout'));
				return $resultPage;
			}
			else {
				return $this->resultRedirectFactory->create()->setPath('checkout/cart');
			} */
		//	echo "welcom1e"; exit;
		    $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
			$storeCode = $storeManager->getStore()->getCode();
			$customerGroupName = $this->_objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();
			if ($storeCode == 'nl' && $customerGroupName =="SOHO") {
				return $this->resultRedirectFactory->create()->setPath('zelfstandigen/checkout/cart');
			} else if ($storeCode == 'fr' && $customerGroupName =="SOHO") {
				return $this->resultRedirectFactory->create()->setPath('independants/checkout/cart');
			} 
			return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        if (!$this->_customerSession->isLoggedIn() && !$checkoutHelper->isAllowedGuestCheckout($quote)) {
            $this->messageManager->addError(__('Guest checkout is disabled.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $this->_customerSession->regenerateId();
        $this->_objectManager->get('Magento\Checkout\Model\Session')->setCartWasUpdated(false);
        $this->getOnepage()->initCheckout();
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Checkout'));
        return $resultPage;
    }
	
	/**
	 * Restore last active quote
	 *
	 * @return bool True if quote restored successfully, false otherwise
	 */
	private function restoreQuote()
	{
		/** @var \Magento\Sales\Model\Order $order */ 
		$lastOrderCookie = $this->_objectManager->get('Orange\Customer\Model\CustomerCookie')->getLastOrderCookie();
		if($lastOrderCookie != '') {		
			$order = $this->_objectManager->get('Magento\Sales\Model\Order')->load($lastOrderCookie, 'increment_id');
			if ($order->getId() && $order->getStatus() == 'pending_payment') {
				try {
					$this->_checkoutSession =  $this->_objectManager->get('Magento\Checkout\Model\Session');
					$this->_checkoutSession->setLastRealOrderId($order->getIncrementId());
					$this->_objectManager->get('Psr\Log\LoggerInterface')->addDebug('LastOrderPlaced:'.$lastOrderCookie);
					$this->_objectManager->get('Psr\Log\LoggerInterface')->addDebug('LastRealOrderPlaceds:'.$order->getIncrementId());
					$lastOrder = $this->_checkoutSession->getLastRealOrder();
					$this->_checkoutSession->restoreQuote();
					$this->_objectManager->get('Psr\Log\LoggerInterface')->addDebug('LastRealOrder:'.$lastOrder->getIncrementId()); 
					return true;
				} catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
					$this->_objectManager->get('Psr\Log\LoggerInterface')->addDebug($e);
				}
			}
		}
		return false;
	}
}
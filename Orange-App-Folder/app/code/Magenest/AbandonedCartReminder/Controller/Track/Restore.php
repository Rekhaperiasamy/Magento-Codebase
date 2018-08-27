<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 23/10/2015
 * Time: 10:38
 */

namespace Magenest\AbandonedCartReminder\Controller\Track;

use Magenest\AbandonedCartReminder\Controller\Track as TrackController;

use Magento\Framework\Controller\ResultFactory;

class Restore extends TrackController
{

    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    protected $_urlBuilder;


    public function execute()
    {
        $resumeRequest = $this->getRequest()->getParam('utc');

        $cartId = $resumeRequest;
		
		
		$abandonedCartCollection = $this->_objectManager->create('Magenest\AbandonedCartReminder\Model\GuestFactory')->create()->getCollection()->addFieldToFilter('quote_id', $resumeRequest);
		$abandonedCart = $abandonedCartCollection->getFirstItem();
		
	
        $quote = $this->_objectManager->create('Magento\Quote\Model\Quote')->load($cartId);
		
		$sohoGroup = false;
		$storeCode = '';
		if ($abandonedCart->getCustomerGroup() =="SOHO") {
		    $sohoGroup = true;
			$storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
			$storeCode = $storeManager->getStore()->getCode(); 
		}

        if (!$this->checkoutSession) {
            $this->checkoutSession = $this->_objectManager->create('\Magento\Checkout\Model\Session');
        }

        // todo allow customer autologin
        //
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $this->checkoutSession->replaceQuote($quote);
		
		if ($sohoGroup) {
			if ($storeCode == 'nl') {
				$resultRedirect->setPath('zelfstandigen/checkout/cart/index');
			} else if ($storeCode == 'fr') {
				$resultRedirect->setPath('independants/checkout/cart/index');
			} else {
				$resultRedirect->setPath('checkout/cart/index');
			}
		} else {
			$resultRedirect->setPath('checkout/cart/index');
		}
        return $resultRedirect;

    }//end executes()
}//end class

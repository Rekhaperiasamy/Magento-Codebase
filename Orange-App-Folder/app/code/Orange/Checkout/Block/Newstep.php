<?php
namespace Orange\Checkout\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Bootstrap;

class Newstep extends Template
{	
 
	public function getFormAction()
    {
        return $this->getUrl('checkout/newstep/post', ['_secure' => true]);
    }
		public function getCheckoutTempSession()
    {
        $bootstrap = Bootstrap::create(BP, $_SERVER);
		$obj = $bootstrap->getObjectManager();

		// Set the state (not sure if this is neccessary)
		$state = $obj->get('Magento\Framework\App\State');
		$state->setAreaCode('frontend');
		
		// Getting the object managers dependencies
		$quote_CheckSession = $obj->get('Magento\Checkout\Model\Session')->getNewcheckout();
		return $quote_CheckSession;
    }
	
	public function getCartUrl()
    {
        return $this->getUrl('checkout/cart', ['_secure' => true]);
    }
	
	public function getQuoteItems()
    {
		$bootstrap = Bootstrap::create(BP, $_SERVER);
		$obj = $bootstrap->getObjectManager();

		// Set the state (not sure if this is neccessary)
		$state = $obj->get('Magento\Framework\App\State');
		$state->setAreaCode('frontend');
		
		// Getting the object managers dependencies
		$quote = $obj->get('Magento\Checkout\Model\Session')->getQuote();

		// Get quote items collection
		$quoteitems = $quote->getAllItems();

		return $quoteitems;
	}
}

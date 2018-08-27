<?php
namespace Orange\Checkout\Block;
use Magento\Framework\App\Bootstrap;

class Onepage extends \Magento\Checkout\Block\Onepage
{
	private $_paymentmethods;
	public function getFormAction()
    {
        return $this->getUrl('checkout/index/post', ['_secure' => true]);
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
		public function getQuoteItemsCount()
    {
		$bootstrap = Bootstrap::create(BP, $_SERVER);
		$obj = $bootstrap->getObjectManager();

		// Set the state (not sure if this is neccessary)
		$state = $obj->get('Magento\Framework\App\State');
		$state->setAreaCode('frontend');
		
		// Getting the object managers dependencies
		$quote = $obj->get('Magento\Checkout\Model\Session')->getQuote();

		// Get quote items collection
		$countItems = $quote->getItemsCount();

		return $countItems;
	}
	public function getPayMethods()
	{
		if($this->_paymentmethods === null)
		{
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$payment = $objectManager->get('Orange\Checkout\Model\Paymentmethod');
			$paymentMethods = $payment->toOptionArray();
			$this->_paymentmethods = $paymentMethods;
		}
		return $this->_paymentmethods;
	}
	public function getPaymentTypes()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$payment = $objectManager->get('Orange\Checkout\Model\Config\Source\Paymenttype');
		$paymentTypes = $payment->toOptionArray();		
		return $paymentTypes;
	}
	public function getPaymentMethodByType($paymentType)
	{
		$paymentMethods = $this->getPayMethods();
		$paymentType = (int)$paymentType;		
		$methods = array_filter($paymentMethods, function ($payment) use($paymentType){
			return ($payment['payment_type'] == $paymentType);
		});		
		return $methods;
	}

}
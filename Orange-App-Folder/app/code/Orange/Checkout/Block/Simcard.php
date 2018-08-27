<?php
namespace Orange\Checkout\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Bootstrap;

class Simcard extends Template
{	
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data); 
		$this->_isScopePrivate = true;
    }
	
	public function getFormAction()
    {
        return $this->getUrl('checkout/cart/post', ['_secure' => true]);
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

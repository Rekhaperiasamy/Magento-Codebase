<?php
namespace Orange\Checkout\Block;
use Magento\Framework\View\Element\Template;

class Tempo extends Template
{
    protected $_checkoutSession;
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        parent::__construct($context, $data); 
		$this->_isScopePrivate = true;
		$this->_checkoutSession 	 = $checkoutSession;

    }
	public function getFormAction()
    {
        return $this->getUrl('checkout/cart/posttempo', ['_secure' => true]);
    }
	   public function objectManager() {
        return \Magento\Framework\App\ObjectManager::getInstance();
    }
	public function getcartcount()
	{
/* 	$cartCount   = $this->objectManager()->create('\Magento\Checkout\Model\Session');
	$quote = $cartCount->getQuote(); */
	//print_r($quote->getData());
	   $quote = $this->_checkoutSession->getQuote();
		$totalItemsInCart1 = $quote->getAllItems();
		$totalItemsInCart = count($totalItemsInCart1);
        if($totalItemsInCart  == 1)
		 {
		 $forURl = $this->getUrl('checkout', ['_secure' => true]);
		 }
		 else
		 {
		  $forURl = $this->getUrl('checkout/cart', ['_secure' => true]);
		 }
	return $forURl;
	}
	 public function getOnepageSku()
	{
	
		$scopeconfig = $this->objectManager()->create('\Magento\Framework\App\Config\ScopeConfigInterface');
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		$onepageSKu = $scopeconfig->getValue('simcard_sku/simcardsku_configuration/simcardsku_url',$storeScope); 
		return $onepageSKu;
	}
}

<?php
namespace Orange\Catalog\Block\Product;

use Magento\Framework\App\ObjectManager;

class PriceView extends \Magento\Framework\View\Element\Template 
{
	protected $_storeManager;
	protected $_currency;
	protected $_priceLabel;
	protected $_orangeSession;
	protected $_customerGroup;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,       
		\Magento\Directory\Model\Currency $currency,		
		\Orange\Catalog\Model\PriceLabel $priceLabel,
		\Orange\Customer\Model\Session $orangeSession,
		array $data = []
    )
    {        
        $this->_storeManager = $context->getStoreManager();
		$this->_currency = $currency;
		$this->_priceLabel = $priceLabel; 
		$this->_orangeSession = $orangeSession;
		$this->_customerGroup = $this->getCustomerTypeName();
        parent::__construct($context, $data);
    }
	
	public function getCurrency()
	{
		$currencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();		
		$currencySymbol = $this->_currency->getCurrencySymbol(); 
		return $currencySymbol;
	}
	
	public function getPriceLabel($customerGroup=null)
	{
		if($customerGroup) {
			$priceLabel = $this->_priceLabel->getPriceLabel($customerGroup);
		} else {
			$priceLabel = $this->_priceLabel->getPriceLabel($this->_customerGroup);
		}
		return $priceLabel;		
	}
	
	public function getCustomerTypeName()
	{		
		$customerGroupName = $this->_orangeSession->getCustomerTypeName();		
		return $customerGroupName;		
	}
}
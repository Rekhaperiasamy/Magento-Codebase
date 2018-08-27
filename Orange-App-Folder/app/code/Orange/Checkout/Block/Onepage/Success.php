<?php
namespace Orange\Checkout\Block\Onepage;
use Magento\Framework\View\Element\Template;


class Success extends Template
{    
	protected $coreRegistry;
	protected $attributeSet;
	protected $_storeManager;
	protected $_currency;
	
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,			
        \Magento\Framework\Registry $coreRegistry,
		\Magento\Directory\Model\Currency $currency,
		\Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        array $data = []
    ) {
        parent::__construct($context, $data); 
		$this->_coreRegistry = $coreRegistry;
		$this->attributeSet = $attributeSet;
		$this->_isScopePrivate = true;
		$this->_storeManager = $context->getStoreManager();
		$this->_currency = $currency;
    }
	
	public function getOrderId()
    {
     return $this->_coreRegistry->registry('OrderId');
	  
    }
		public function getCustomOrderid()
    {
	    $incrementId = '000000019';
        return $incrementId;
    }

	public function getOrder()
    {
		$incrementId = $this->getOrderId();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$order = $objectManager->create('Magento\Sales\Model\Order')->load($incrementId, 'increment_id');
		return $order;
	}
	
	public function getCustomerGroup($customerGroupId)
	{		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerGroup = $objectManager->create('Magento\Customer\Api\GroupRepositoryInterface')->getById($customerGroupId)->getCode();
		return $customerGroup;
	}
	 public function getDynamicStatic($staticBlockID,$dynamicVariable,$variablename) {
       $dynamicContent = $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($staticBlockID)->toHtml();
       $replaceVariable = '{{var '.$variablename.'}}';
	   $dynamicPrVariable = $dynamicVariable.'&nbsp;'.$this->getCurrency();
	   return str_replace($replaceVariable,$dynamicPrVariable,$dynamicContent);

   }
   
	/**
	 * @param Price (INT)
	 * @param Has Price label (BOOLEAN)
	 * @param Show price label (BOOLEAN)
	 * @param Is subscription price (BOOLEAN)
	 * @param Color of price (TEXT)
	 * @return HTML
	 */
	public function getOrangePricingHtml($price,$hasPriceLabel=false,$showPriceLabel=false,$isSubscription=false,$priceColor,$isNegative = NULL,$isSohoDiscount=NULL)
	{
		$block = $this->getLayout()->createBlock('Orange\Catalog\Block\Product\PriceView');
		$block->setTemplate('Orange_Catalog::orange_price.phtml');
		$block->setProductPrice($price);
		$block->setIsPriceLabelVisible($hasPriceLabel);
		$block->setShowPriceLabel($showPriceLabel);
		$block->setIsSubscription($isSubscription);
		$block->setIsSohoDiscount($isSohoDiscount);
		$block->setPriceColor($priceColor);
		$block->setIsNegative($isNegative);
		return $block->toHtml();
	}

	public function getOrangeEmailPricingHtml($price,$hasPriceLabel=false,$showPriceLabel=false,$isSubscription=false,$priceColor,$order,$isNegative = NULL,$isSohoDiscount=NULL)
	{
		$block = $this->getLayout()->createBlock('Orange\Catalog\Block\Product\PriceView');
		$block->setTemplate('Orange_Catalog::orange_price_email.phtml');
		$block->setProductPrice($price);
		$block->setIsPriceLabelVisible($hasPriceLabel);
		$block->setShowPriceLabel($showPriceLabel);
		$block->setIsSubscription($isSubscription);
		$block->setIsSohoDiscount($isSohoDiscount);
		$block->setPriceColor($priceColor);
		$block->setOrder($order);
		$block->setIsNegative($isNegative);
		return $block->toHtml();
	}
    public function getOnlyPrepaidInOrder() {
        $orderData = $this->getOrder()->getAllItems();
        $data = 0;
        $count = count($orderData);
        foreach ($orderData as $item) {
            if ($item->getProductType() == 'virtual' && $item->getParentItemId() == "" && ($item->getProduct()->getAttributeSetId() == 10 || $item->getProduct()->getAttributeSetId() == 11 ) && $count == 1) {
                $data = 1;
                continue;
            }
        }
        return $data;
    }
	
	public function getAttributeSetName($attributeSetId) {		
		$attributeSetRepository = $this->attributeSet->get($attributeSetId);
		return $attributeSetRepository->getAttributeSetName();
	}
	
	public function getCurrency()
	{
		$currencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();		
		$currencySymbol = $this->_currency->getCurrencySymbol(); 
		return $currencySymbol;
	}
	
	public function getPriceLabel($storeName) 
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$priceLabel = $objectManager->create('Orange\Catalog\Model\PriceLabel')->getPriceLabel($storeName);
		return $priceLabel;
	}

}

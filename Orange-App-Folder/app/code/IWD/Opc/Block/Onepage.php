<?php

namespace IWD\Opc\Block;

class Onepage extends \Magento\Checkout\Block\Onepage {

    private $_variableModel;
     protected $scopeConfig;

    public function __construct(
    \Magento\Variable\Model\Variable $variable,
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            array $data = []) {
        $this->_variableModel = $variable;
         $this->scopeConfig = $scopeConfig;
    }

    public function getPropacks() {
        $propacks = $this->_variableModel->loadByCode('propacks');
        return $propacks->getPlainValue();
    }

    public function objectManager() {
        return \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function quoteAllItems() {
        return $this->objectManager()->create('Magento\Checkout\Model\Cart')->getQuote()->getAllVisibleItems();
    }

    public function quoteItemsProductSecondStepShowCase() {
        $quoteitems = $this->quoteAllItems();
        $prod = array();
        if ($this->checkOnlySimpleProducts() == 0) {
			if(count($quoteitems) == 1) {
				foreach ($quoteitems as $key => $item) {
					if ($item->getProductType() == 'simple') {
						$attrName = $this->getAttributeNameBasedOnID($item);
						$data = $this->scopeConfig->getValue('common/common_configuration/checkout_secondstep_showcase_simple');                    
						$secondAttr = explode(',', $data); //need to chnage config              
						if (is_array($secondAttr) && in_array($attrName, $secondAttr)) {
							$prod[$key]['name'] = $item->getName();                       
							$imageHelper = $this->objectManager()->get('Magento\Catalog\Model\Product')->load($item->getProduct()->getId());
							$prod[$key]['img'] = $imageHelper->getAttributeText('product_icon_image');
							$prod[$key]['price'] = $item->getPrice(); 
							$prod[$key]['qty'] = $item->getQty();
						}
					}
				}
				return $prod;
			}
        }
        return 0;
    }

    public function checkOnlySimpleProducts() {
        $quoteitems = $this->quoteAllItems();
        $products = 0;
        foreach ($quoteitems as $key => $item) {            
                $attrName = $this->getAttributeNameBasedOnID($item);
                $data = $this->scopeConfig->getValue('common/common_configuration/checkout_secondstep_showcase_simple');                    
                $secondAttr = explode(',',$data);            
                if (is_array($secondAttr) && !in_array($attrName, $secondAttr)) {
                    $products = 1;
                    continue;
                }           
        }
        return $products;
    }

    public function getCustomerTypeName() {
        $customerGroupName = $this->objectManager()->create('Orange\Customer\Model\Session')->getCustomerTypeName();
        return $customerGroupName;
    }

    public function getAttributeSetName($id) {
        $attributeSet = $this->objectManager()->create('\Magento\Eav\Api\AttributeSetRepositoryInterface');
        $attributeSetRepository = $attributeSet->get($id);
        return $attributeSetRepository->getAttributeSetName();
    }

    public function getAttributeNameBasedOnID($item) {
        return $this->getAttributeSetName($item->getProduct()->getAttributeSetId());
    }
	
	public function getCustomerZoneURL()
	{
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		$customerZone = $this->scopeConfig->getValue('customerzone/customerzone_configuration/customerzone_url',$storeScope); 
		return $customerZone;
	}
	public function getBpostAccountId()
	{
		$accountId = $this->scopeConfig->getValue('carriers/bpost/account'); 
		return $accountId;
	}
	public function getBpostPassphrase()
	{
		$passphrase = $this->scopeConfig->getValue('carriers/bpost/passphrase'); 
		return $passphrase;
	}
}

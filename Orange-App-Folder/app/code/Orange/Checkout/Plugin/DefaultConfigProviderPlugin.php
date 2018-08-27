<?php

namespace Orange\Checkout\Plugin;

class DefaultConfigProviderPlugin
{
	private $_ruleModel;
	private $_couponModel;
	private $_variableModel;
	private $urlBuilder;
	private $catalogHelper;
	private $storeManager;
	
	public function __construct(
		\Magento\SalesRule\Model\Rule $rule,
		\Magento\SalesRule\Model\Coupon $coupon,
		\Magento\Variable\Model\Variable $variable,
		\Magento\Framework\UrlInterface $urlBuilder,
		\Orange\Catalog\Helper\CatalogUrl $catalogHelper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		array $data = []){
		$this->_ruleModel = $rule;
		$this->_couponModel = $coupon;	
		$this->_variableModel = $variable;
		$this->urlBuilder = $urlBuilder;
		$this->catalogHelper = $catalogHelper;
		$this->storeManager = $storeManager;
	}
	public function aftergetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject,$result)
    {  		
		$propacks = $this->_variableModel->loadByCode('propacks');
		$result['propacks'] = $propacks->getPlainValue();
		$formattedURL = $this->catalogHelper->getFormattedUrl('/checkout/onepage/success/');//Formatted SOHO success page URL for redirection

		$baseURL = $this->storeManager->getStore()->getBaseUrl();
		if (strpos($baseURL, 'nl') !== false) {
			$baseURL = str_replace('/nl/', '/nl', $baseURL);
			$formattedURL = $baseURL.$formattedURL;
			$result['defaultSuccessPageUrl'] = $formattedURL;
		}
		else if (strpos($baseURL, 'fr') !== false) {
			$baseURL = str_replace('/fr/', '/fr', $baseURL);
			$formattedURL = $baseURL.$formattedURL;
			$result['defaultSuccessPageUrl'] = $formattedURL;
		}
		else {
			$result['defaultSuccessPageUrl'] = $this->urlBuilder->getUrl($formattedURL);
		}
        return $result;
    }
}
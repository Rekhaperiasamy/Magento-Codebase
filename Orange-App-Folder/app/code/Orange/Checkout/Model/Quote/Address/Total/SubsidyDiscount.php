<?php
namespace Orange\Checkout\Model\Quote\Address\Total;
 
class SubsidyDiscount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
	const XML_PATH_DEVICE_SUBSIDY_AMOUNT  = 'subsidy/subsidy_configuration/amount_subsidy';
	const XML_PATH_SOHO_DEVICE_SUBSIDY_AMOUNT  = 'subsidy/subsidy_configuration/amount_subsidy_soho';
	
    /**
    * @var \Magento\Framework\Pricing\PriceCurrencyInterface
    */
    protected $_priceCurrency;
	
	protected $_scopeConfig;
	protected $_logger;
	protected $_couponHelper;
 
    /**
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency [description]
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,		
		\Psr\Log\LoggerInterface $logger,
        \Orange\Coupon\Helper\Data $couponHelper
    ) {
        $this->_priceCurrency = $priceCurrency;
		$this->_scopeConfig = $scopeInterface;
		$this->_logger = $logger;
    	$this->_couponHelper = $couponHelper;
    }
 
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
	{
        parent::collect($quote, $shippingAssignment, $total);
		// Subsidy orders, with an upfront handset payment up to 50€, shall not be charged on the eShop		
		if ($shippingAssignment->getShipping()->getAddress()->getAddressType() == 'shipping') {
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			//$customerGroup = $objectManager->create('Magento\Customer\Api\GroupRepositoryInterface')->getById($quote->getCustomerGroupId())->getCode();
			$customerGroup = $this->getCustomerTypeName();
			$allItems = $quote->getAllItems();
			$SubsidyDiscount = 0;			
			$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
			if($customerGroup == 'SOHO'):
				$discountPrice = $this->_scopeConfig->getValue(self::XML_PATH_SOHO_DEVICE_SUBSIDY_AMOUNT, $storeScope);
			else:
				$discountPrice = $this->_scopeConfig->getValue(self::XML_PATH_DEVICE_SUBSIDY_AMOUNT, $storeScope);
			endif;
			foreach ($allItems as $item) {
			    $totalMonthPrice = $item->getPrice();
				if($item->getProductType() == "bundle") {					
					$product = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProduct()->getId());
					if($customerGroup == 'SOHO'){																	
						$objectManager->get('Psr\Log\LoggerInterface')->addDebug('SOHO PRICE DIS:'.$product->getSohoPrice());
						if($product->getSohoPrice()) {
							$sohoPrice = $product->getSohoPrice();						
							$objectManager->get('Psr\Log\LoggerInterface')->addDebug('SOHO PRICE DIS2:'.$product->getSohoPrice());
							$item->setCustomPrice($sohoPrice);
							$item->setOriginalCustomPrice($sohoPrice);
							$totalMonthPrice = $sohoPrice;
						}						
					}
					else {
						$sohoPrice = $product->getPrice();	
						$objectManager->get('Psr\Log\LoggerInterface')->addDebug('B2C PRICE DIS1:'.$sohoPrice);
						$item->setCustomPrice($sohoPrice);
						$item->setOriginalCustomPrice($sohoPrice);
						$totalMonthPrice = $sohoPrice;
					}					
				}
				if ($item->getProductType() == "bundle" && $totalMonthPrice <= $discountPrice){
					$SubsidyDiscount += $totalMonthPrice * $item->getQty();
				}
				/** Discount for subsidy prodct **/
				/** Explode and foreach ruleIds and apply discount with the getSubscriptionAmount **/				
				if($quote->getAppliedRuleIds()) {
					$ruleIds = explode(",",$quote->getAppliedRuleIds());
					$rule = $objectManager->create('Magento\SalesRule\Model\Rule')->load($quote->getAppliedRuleIds());
				}				
				if ($item->getParentItem()) {
					//Applying discount for subscription item
					$ruleIds = explode(",",$quote->getAppliedRuleIds());
					$totalDiscountAmount = 0;
					foreach($ruleIds as $ruleId):
						$rule = $objectManager->create('Magento\SalesRule\Model\Rule')->load($ruleId);	
						/* couponcode for custom coupon reporting */
						$couponCode = $rule->getCouponCode();
						if($couponCode == '')
							$couponCode = $quote->getCouponCode();
						/* end */
						if(!$rule->getActions()->validate($item->getParentItem())){
							//If discount is not applicable for parent product and check if applicable to child products
							if($item->getProductType() == 'virtual' && $item->getParentItem()->getDiscountAmount() > 0) {
								//If discount code applicable for virtual Apply discount only to subscription product inside bundled
								if($rule->getActions()->validate($item)) {
									switch ($rule->getSimpleAction()) {
										case \Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION:
											$rulePercent = max(0, 100 - $rule->getDiscountAmount());
											//$discountAmount = $item->getOriSubscriptionAmount() * $rulePercent / 100; //Get subscription based on customer type
											$discountAmount = $item->getOriSubscriptionAmount() * $rulePercent / 100; //Get subscription based on customer type										
											$totalDiscountAmount = $totalDiscountAmount + ($item->getOriSubscriptionAmount() - $discountAmount);
											$dAmount = $discountAmount;
											//$item->setSubscriptionAmount($discountAmount);
										break;
										case \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION:
											//$discountAmount = $item->getOriSubscriptionAmount() - $rule->getDiscountAmount();	//Get subscription based on customer type
											$discountAmount = $item->getOriSubscriptionAmount() - $rule->getDiscountAmount();	//Get subscription based on customer type
											$totalDiscountAmount = $totalDiscountAmount + ($item->getOriSubscriptionAmount() - $discountAmount);
											$dAmount = min($item->getOriSubscriptionAmount(),$rule->getDiscountAmount());
											//$item->setSubscriptionAmount($discountAmount);							 
										break;
									}
                                                                        /* Added new argument coupon code($couponCode) based on rule id */
									$this->_couponHelper->saveCouponData($item->getId(),$rule->getId(),$dAmount,'subscription', $couponCode);//Saving to orange quote coupon
								}
							}
							if($item->getProductType() == 'simple' && $item->getParentItem()->getDiscountAmount() > 0) {
								// If discount code applicable for simple Apply discount only to simple product inside bundled							
								if(!$rule->getActions()->validate($item)) {
									$parentDiscount = $item->getParentItem()->getDiscountAmount();
									/** zero discount for parent **/
									$item->getParentItem()->setDiscountAmount(0);
									$item->getParentItem()->setBaseDiscountAmount(0);
									/** Revert discount from quote **/
									$total->setSubtotalWithDiscount($total->getSubtotal());
									$total->setBaseSubtotalWithDiscount($total->getBaseSubtotal());
									$total->addTotalAmount('grand_total', $parentDiscount);
									$total->addBaseTotalAmount('base_grand_total', $parentDiscount);
									$total->setDiscountAmount('discount_amount', $parentDiscount);
									$total->setBaseDiscountAmount('base_discount_amount', $parentDiscount);							
									$quote->setSubtotalWithDiscount($total->getSubtotal());
									$quote->setBaseSubtotalWithDiscount($total->getBaseSubtotal());							
								}
							}						
						}
						else{
							// If Rule applicable for bundle product apply discount for virtual product
							if($item->getProductType() == 'virtual' && $item->getParentItem()->getDiscountAmount() > 0) {								
								switch ($rule->getSimpleAction()) {
									case \Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION:
										$rulePercent = max(0, 100 - $rule->getDiscountAmount());
										//$discountAmount = $item->getOriSubscriptionAmount() * $rulePercent / 100; //Get subscription based on customer type
										$discountAmount = $item->getOriSubscriptionAmount() * $rulePercent / 100; //Get subscription based on customer type
										//$item->setSubscriptionAmount($discountAmount);
										$totalDiscountAmount = $totalDiscountAmount + ($item->getOriSubscriptionAmount() - $discountAmount);
										$dAmount = $item->getOriSubscriptionAmount() - $discountAmount;
									break;
									case \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION:
										//$discountAmount = $item->getOriSubscriptionAmount() - $rule->getDiscountAmount();	//Get subscription based on customer type
										$discountAmount = $item->getOriSubscriptionAmount() - $rule->getDiscountAmount();	//Get subscription based on customer type
										//$item->setSubscriptionAmount($discountAmount);	
										$totalDiscountAmount = $totalDiscountAmount + ($item->getOriSubscriptionAmount() - $discountAmount);
										$dAmount = $rule->getDiscountAmount();
									break;
								}
                                                                /* Added new argument coupon code($couponCode) based on rule id */
                                $dAmount = max(0,$dAmount);
								$this->_couponHelper->saveCouponData($item->getId(),$rule->getId(),$dAmount,'subscription',$couponCode);//Saving to orange quote coupon													
							}
						}
						//Cancelling discount for subscription item
						if($item->getProductType() == 'virtual' && $quote->getCouponCode()=='' && $item->getParentItem()->getDiscountAmount() < 1){
							//Revert back to original subscription amount on cancelling coupon
							$subscriptionAmount = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProduct()->getId())->getSubscriptionAmount();
							//$item->setSubscriptionAmount($subscriptionAmount);
							$item->setOriSubscriptionAmount($subscriptionAmount);
							$totalDiscountAmount = 0;
							$quote->setSubscriptionTotal($quote->getOriSubscriptionTotal());						
						}
					endforeach;
					$totalDiscountAmount = max(0,$totalDiscountAmount);
					$consolidatedSubscriptionAmount = $item->getOriSubscriptionAmount() - $totalDiscountAmount;
					$item->setSubscriptionAmount($consolidatedSubscriptionAmount);
				} 
				if($item->getProductType() == 'virtual' && !$item->getParentItemId()){
					// If Coupon applied only applicable to virtual product
					if($item->getAppliedRuleIds()){
						$ruleIds = explode(",",$item->getAppliedRuleIds());
						$totalDiscountAmount = 0;
						$subscriptionAmount = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProduct()->getId())->getSubscriptionAmount();
						foreach($ruleIds as $ruleId):
							$rule = $objectManager->create('Magento\SalesRule\Model\Rule')->load($ruleId);
							/* couponcode for custom coupon reporting */
							$couponCode = $rule->getCouponCode();
							if($couponCode == '')
								$couponCode = $quote->getCouponCode();
							/* end */
							//Applying discount for subscription product													
							switch ($rule->getSimpleAction()) {
								case \Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION:
									$rulePercent = max(0, 100 - $rule->getDiscountAmount());
									$discountAmount = $subscriptionAmount * $rulePercent / 100; //Get subscription based on customer type
									$totalDiscountAmount = $totalDiscountAmount + ($subscriptionAmount - $discountAmount);									
									//$item->setSubscriptionAmount($discountAmount);
								break;
								case \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION:
									$discountAmount = $subscriptionAmount - $rule->getDiscountAmount();	//Get subscription based on customer type
									$totalDiscountAmount = $totalDiscountAmount + ($subscriptionAmount - $discountAmount);
									//$item->setSubscriptionAmount($discountAmount);	
									if($discountAmount < 0) {
										$discountAmount = $subscriptionAmount;
									}						 
								break;
								case \Amasty\Rules\Helper\Data::TYPE_CHEAPEST:
									$subscriptionItems = $quote->getAllItems();
									$subAmount = 0;									
									foreach ($subscriptionItems as $subscriptionItem) {
										if($subscriptionItem->getIsVirtual() == 1):
											$subAmount = $objectManager->get('Magento\Catalog\Model\Product')->load($subscriptionItem->getProduct()->getId())->getSubscriptionAmount();												
											$subscriptions[$subscriptionItem->getItemId()]=$subAmount;
										endif;
									}									
									$minSubscription = array_keys($subscriptions, min($subscriptions)); //Get cheapest subscription in caddy									
									$rulePercent = max(0, 100 - $rule->getDiscountAmount());
									foreach($minSubscription as $subscriptionId):
										if($item->getId() == $subscriptionId):
											/** Match with least subscription Amount **/											
											$discountAmount = $subscriptionAmount * $rulePercent / 100; //Get subscription based on customer type
											$totalDiscountAmount = $totalDiscountAmount + ($subscriptionAmount - $discountAmount);
										endif;
									endforeach;
								break;
								case \Amasty\Rules\Helper\Data::TYPE_EXPENCIVE:
									$subscriptionItems = $quote->getAllItems();
									$subAmount = 0;									
									foreach ($subscriptionItems as $subscriptionItem) {
										if($subscriptionItem->getIsVirtual() == 1):
											$subAmount = $objectManager->get('Magento\Catalog\Model\Product')->load($subscriptionItem->getProduct()->getId())->getSubscriptionAmount();												
											$subscriptions[$subscriptionItem->getItemId()]=$subAmount;
										endif;
									}									
									$maxSubscription = array_keys($subscriptions, max($subscriptions)); //Get expensive subscription in caddy									
									$rulePercent = max(0, 100 - $rule->getDiscountAmount());
									foreach($maxSubscription as $subscriptionId):
										if($item->getId() == $subscriptionId):
											/** Match with least subscription Amount **/											
											$discountAmount = $subscriptionAmount * $rulePercent / 100; //Get subscription based on customer type
											$totalDiscountAmount = $totalDiscountAmount + ($subscriptionAmount - $discountAmount);
										endif;
									endforeach;
								break;
							}
							if(isset($discountAmount)) {
								$subDiscountAmount = $item->getOriSubscriptionAmount()-$discountAmount;	
							} else {
								$subDiscountAmount = 0;
							}
                                                        /* Added new argument coupon code($couponCode) based on rule id */
							$this->_couponHelper->saveCouponData($item->getId(),$rule->getId(),$subDiscountAmount,'subscription',$couponCode);	//Saving to orange quote coupon						
							 //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
							 //$objectManager->get('Psr\Log\LoggerInterface')->addDebug('Subsidy Rule Applier');	
							 //$objectManager->get('Psr\Log\LoggerInterface')->addDebug('Rule ID:'.$ruleId);								
							 //$objectManager->get('Psr\Log\LoggerInterface')->addDebug($item->getId().'=>'.$item->getName().'=>'.$item->getOriSubscriptionAmount().'=='.$item->getSubscriptionAmount().'=='.$subDiscountAmount);
							 //$objectManager->get('Psr\Log\LoggerInterface')->addDebug('----------------------------------');
						endforeach;
						$consolidatedSubscriptionAmount = $subscriptionAmount - $totalDiscountAmount;
						$consolidatedSubscriptionAmount = max(0,$consolidatedSubscriptionAmount);
						$item->setSubscriptionAmount($consolidatedSubscriptionAmount);
					}
					else{
						//Cancelling discount for subscription product
						$subscriptionAmount = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProduct()->getId())->getSubscriptionAmount();							
						$item->setSubscriptionAmount($subscriptionAmount);
						$item->setOriSubscriptionAmount($subscriptionAmount);
						$quote->setSubscriptionTotal($quote->getOriSubscriptionTotal());						
					}
				}
			}
			$total->addTotalAmount('subsidydiscount', -$SubsidyDiscount);
			$total->addBaseTotalAmount('subsidydiscount', -$SubsidyDiscount);
			$quote->setSubsidyDiscount($SubsidyDiscount);
			/** Added this because Magento 2 not triggering sales_quote_total_collect_after observer from checkout after coupon applied */
			$subscriptionTotal = $this->_getSubscriptionTotal($quote);
			$quote->setSubscriptionTotal($subscriptionTotal);
			if($quote->getAppliedRuleIds()) {
				$quote->setCouponDescription($rule->getDescription());
			}
			else {
				$quote->setCouponDescription(null);
			}			
		}
		return $this;	
    }
	private function getCustomerTypeName()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerGroupName = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();		
		return $customerGroupName;		
	}
	private function _getSubscriptionTotal($quote)
	{
		$allItems = $quote->getAllItems();
		$subscriptionAmount = 0;
		foreach ($allItems as $item) {
			$subscriptionAmount += $item->getSubscriptionAmount();
		}
		return $subscriptionAmount;
	}
	
}
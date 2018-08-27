<?php
namespace Orange\Coupon\Model;

class RulesApplier extends \Magento\SalesRule\Model\RulesApplier
{
	private $_logger;
	protected $_couponHelper;
	 /**
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory $calculatorFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\SalesRule\Model\Utility $utility
     */
    public function __construct(
        \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory $calculatorFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\SalesRule\Model\Utility $utility,
        \Psr\Log\LoggerInterface $logger,
        \Orange\Coupon\Helper\Data $couponHelper
    ) {
    	$this->_logger = $logger;
    	$this->_couponHelper = $couponHelper;
        parent::__construct($calculatorFactory, $eventManager, $utility);
    }

	protected function applyRule($item, $rule, $address, $couponCode)
    {
		if($item->getProductType() == 'virtual' && !$item->getParentItemId()) {
			$totalDiscountAmount = 0;
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$quote = $objectManager->get('Magento\Checkout\Model\Session')->getQuote();
			$subscriptionAmount = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProduct()->getId())->getSubscriptionAmount();
			switch ($rule->getSimpleAction()) {
				case \Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION:
					$rulePercent = max(0, 100 - $rule->getDiscountAmount());
					$discountAmount = $subscriptionAmount * $rulePercent / 100; //Get subscription based on customer type
					$totalDiscountAmount = $totalDiscountAmount + ($subscriptionAmount - $discountAmount);									
					//$item->setSubscriptionAmount($discountAmount);
					$subDiscountAmount = $item->getOriSubscriptionAmount()-$discountAmount;
					$this->_couponHelper->saveCouponData($item->getId(),$rule->getId(),$subDiscountAmount,'subscription',$couponCode);	//Saving to orange quote coupond
				break;
				case \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION:
					$discountAmount = $subscriptionAmount - $rule->getDiscountAmount();	//Get subscription based on customer type
					$totalDiscountAmount = $totalDiscountAmount + ($subscriptionAmount - $discountAmount);
					//$item->setSubscriptionAmount($discountAmount);	
					if($discountAmount < 0) {
						$subDiscountAmount = $item->getOriSubscriptionAmount();
					}
					else {
						$subDiscountAmount = $item->getOriSubscriptionAmount()- $discountAmount;
					}
					$this->_couponHelper->saveCouponData($item->getId(),$rule->getId(),$subDiscountAmount,'subscription',$couponCode);	//Saving to orange quote coupond
				break;
			}
		} else {
			$discountData = $this->getDiscountData($item, $rule); //Get discount data for item
			$quoteId = $item->getQuoteId();				
			//$this->_logger->addDebug('Rule Applier');	
			//$this->_logger->addDebug('Rule ID:'.$rule->getId());	
			$discountAmount = $discountData->getAmount()-$item->getDiscountAmount(); //Get discount amount
			//$this->_logger->addDebug($item->getId().'=>'.$item->getName().'=>'.$item->getDiscountAmount().'=='.$discountAmount);
			//$this->_logger->addDebug('----------------------------------');
			$this->_couponHelper->saveCouponData($item->getId(),$rule->getId(),$discountAmount,NULL,$couponCode); //save to orange quote coupon
			$this->setDiscountData($discountData, $item);
		}
		$this->maintainAddressCouponCode($address, $rule, $couponCode);
		$this->addDiscountDescription($address, $rule);
        return $this;
    }
}
<?php

namespace Orange\Coupon\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_checkoutSession;
	protected $_logger;
	protected $_quoteCoupon;
	protected $_quoteCouponFactory;
    protected $_couponResourceModel;
	protected $_salesOrder;
	protected $_salesCoupon;
        
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,       
        \Orange\Coupon\Model\QuoteCouponFactory $quoteCouponFactory,
        \Orange\Coupon\Model\QuoteCoupon $quoteCoupon,
        \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory $couponResourceModel,
		\Magento\Sales\Model\Order $salesOrder,
		\Magento\SalesRule\Model\Coupon $salesCoupon
    ){
		$this->_checkoutSession = $checkoutSession;
		$this->_logger = $context->getLogger();
		$this->_quoteCoupon = $quoteCoupon;
		$this->_quoteCouponFactory = $quoteCouponFactory;
        $this->_couponResourceModel = $couponResourceModel;                
		$this->_salesOrder = $salesOrder;
		$this->_salesCoupon = $salesCoupon;
		parent::__construct($context);
    }
	
	/**
	 * Save the coupon data DB item level	 
	 */
	public function saveCouponData($itemId,$ruleId,$discount,$discountType = NULL,$couponCode=NULL)
	{
		$quoteId = $this->_checkoutSession->getQuote()->getId(); //Get current quote Id			
		$this->_quoteCoupon->clearItemData($quoteId,$itemId,$ruleId); // Clear Existing Item discount 
                $currentCouponCode = $this->_getCurrentCoupon($ruleId,$couponCode); //Get Current Coupon code based on ruleId
		$quoteCouponFactory = $this->_quoteCouponFactory->create();		
        $quoteCouponFactory->setQuoteId($quoteId);
        $quoteCouponFactory->setRuleId($ruleId);
        $quoteCouponFactory->setItemId($itemId);
        if($discountType) {
            $quoteCouponFactory->setSubscriptionDiscountAmount($discount);
        }
        else {
            $quoteCouponFactory->setDiscountAmount($discount);
        }
        $quoteCouponFactory->setCouponCode($currentCouponCode);
        $quoteCouponFactory->save();
		/* For Testing before saving DB*/
		// $this->_logger->addDebug('---------Save To DB-----------');		
		// $this->_logger->addDebug('Quote Id:'.$quoteId);
		// $this->_logger->addDebug('Item Id:'.$itemId);
		// $this->_logger->addDebug('Rule Id:'.$ruleId);
		// $this->_logger->addDebug('Discount:'.$discount);
		// $this->_logger->addDebug('------Finish Saving To DB------');
	}
	
	/**
	 * Get the Discount amount of particular rule/coupon	 
	 */
	 
	public function getRuleDiscountAmount($ruleId,$quoteId = null)
	{
		if($quoteId):
			$currentQuoteId = $quoteId;
		else:
			$currentQuoteId = $this->_checkoutSession->getQuote()->getId();
		endif;		
		$discountData = $this->_quoteCoupon->getRuleDiscountAmount($currentQuoteId,$ruleId);
		return $discountData;
	}
        
	/* ruleid based coupon code */
	private function _getCurrentCoupon($ruleId,$couponCode)
	{
		$applied_coupons = explode(',', $couponCode);
		
		$couponCollections = $this->_couponResourceModel->create()
		->addFieldToFilter('rule_id',$ruleId)
		->addFieldToFilter('code' , array('in' => $applied_coupons))
		->getFirstItem();
		return $couponCollections->getCode();
	}
	/* end */
	
	/**
	 * Reactivate coupon using order
	 * Added for incident 39120247 coupon reactivation 
	 */
	public function reactivateCouponByOrder($orderIncrementId)
	{
		try {
			$order = $this->_salesOrder->load($orderIncrementId, 'increment_id');
			$orderStatus = $order->getStatus();
			$appliedCouponCodes = explode(",",$order->getCouponCode());
			if ($order->getCouponCode()) {
				foreach($appliedCouponCodes as $couponCode) {
					$couponModel = $this->_salesCoupon->loadByCode($couponCode);
					if($couponModel->getCouponId()) {
						$usageLimit = intval($couponModel->getUsageLimit());                
						$couponModel->setUsageLimit($usageLimit + 1); //Increase the usage limit
						$couponModel->save();
						$this->_logger->info('Coupon Code : '.$couponCode.' reactivated for order '. $orderIncrementId.' with status '.$orderStatus);
					}
					else {
						$this->_logger->error('Coupon Code : '.$couponCode.' is invalid for order '. $orderIncrementId.' with status '.$orderStatus);              
					}
				}
				/** Apply the Coupon code on order cancel from ogone for reactivation 39120247 **/
				$currentQuote = $this->_checkoutSession->getQuote();
				$currentQuote->setCouponCode($order->getCouponCode());
				$currentQuote->save();
			}
		}
		catch (\Magento\Framework\Exception\LocalizedException $e) {
			$this->_logger->error($e->getMessage());
		} catch (\RuntimeException $e) {
			$this->_logger->error($e->getMessage());
		} catch (\Exception $e) {
			$this->_logger->error($e->getMessage());
		}
	}
	 
}
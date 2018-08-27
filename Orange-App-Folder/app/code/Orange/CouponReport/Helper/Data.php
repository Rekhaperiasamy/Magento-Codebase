<?php

namespace Orange\CouponReport\Helper;

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
        \Orange\CouponReport\Model\CouponsFactory $couponFactory,
		\Orange\Coupon\Model\QuoteCoupon $quoteCoupon
    ){
		$this->_logger = $context->getLogger();
		$this->_couponFactory = $couponFactory;
		$this->_quoteCoupon = $quoteCoupon;
		parent::__construct($context);
    }
	/**
	 * Save the coupon data DB item level	 
	 */
	public function saveCouponData($order_id,$coupon_code,$status,$quote_id,$discount_amount)
	{
		$appliedCouponCodes = explode(",",$coupon_code);
		if ($coupon_code) {
			foreach ($appliedCouponCodes as $couponCode) {
				$couponFactory = $this->_couponFactory->create();
				$couponFactory->setOrderId($order_id);
				$couponFactory->setOcrCouponCode($couponCode);
				$couponFactory->setOrderStatus($status);
				$couponFactory->setQuoteId($quote_id);
				if (count($appliedCouponCodes) < 2 && $discount_amount != 0)
					$couponFactory->setOcrDiscountAmount(abs($discount_amount)); 
				else {
					$quoteCouponCollection = $this->_quoteCoupon->getCollection()
					->addFieldToFilter('quote_id',$quote_id)
					->addFieldToFilter('CouponCode',$couponCode)
					->getFirstItem();
					$discount_amount = $quoteCouponCollection->getDiscountAmount() + $quoteCouponCollection->getSubscriptionDiscountAmount();
					$couponFactory->setOcrDiscountAmount($discount_amount);
					}
				$couponFactory->save();
			}
		}
	}
}
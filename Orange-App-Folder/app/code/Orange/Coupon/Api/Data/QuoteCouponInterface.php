<?php


namespace Orange\Coupon\Api\Data;

interface QuoteCouponInterface
{

    const SUBSCRIPTION_DISCOUNT_AMOUNT = 'subscription_discount_amount';
    const RULE_ID = 'rule_id';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const QUOTE_COUPON_ID = 'quote_coupon_id';
    const QUOTE_ID = 'quote_id';
    const COUPONCODE = 'CouponCode';


    /**
     * Get quote_coupon_id
     * @return string|null
     */
    
    public function getQuoteCouponId();

    /**
     * Set quote_coupon_id
     * @param string $quote_coupon_id
     * @return Orange\Coupon\Api\Data\QuoteCouponInterface
     */
    
    public function setQuoteCouponId($quoteCouponId, $quote_coupon_id);

    /**
     * Get quote_id
     * @return string|null
     */
    
    public function getQuoteId();

    /**
     * Set quote_id
     * @param string $quote_id
     * @return Orange\Coupon\Api\Data\QuoteCouponInterface
     */
    
    public function setQuoteId($quote_id);

    /**
     * Get rule_id
     * @return string|null
     */
    
    public function getRuleId();

    /**
     * Set rule_id
     * @param string $rule_id
     * @return Orange\Coupon\Api\Data\QuoteCouponInterface
     */
    
    public function setRuleId($rule_id);

    /**
     * Get discount_amount
     * @return string|null
     */
    
    public function getDiscountAmount();

    /**
     * Set discount_amount
     * @param string $discount_amount
     * @return Orange\Coupon\Api\Data\QuoteCouponInterface
     */
    
    public function setDiscountAmount($discount_amount);

    /**
     * Get subscription_discount_amount
     * @return string|null
     */
    
    public function getSubscriptionDiscountAmount();

    /**
     * Set subscription_discount_amount
     * @param string $subscription_discount_amount
     * @return Orange\Coupon\Api\Data\QuoteCouponInterface
     */
    
    public function setSubscriptionDiscountAmount($subscription_discount_amount);
    
    /**
     * Get Coupon code
     * @return string|null
     */
    
    public function getCouponCode();

    /**
     * Set Coupon code
     * @param string $CouponCode
     * @return Orange\Coupon\Api\Data\QuoteCouponInterface
     */
    
    public function setCouponCode($CouponCode);
}

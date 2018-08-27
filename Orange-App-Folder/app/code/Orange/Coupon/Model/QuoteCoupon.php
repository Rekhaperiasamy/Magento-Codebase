<?php


namespace Orange\Coupon\Model;

use Orange\Coupon\Api\Data\QuoteCouponInterface;

class QuoteCoupon extends \Magento\Framework\Model\AbstractModel implements QuoteCouponInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Orange\Coupon\Model\ResourceModel\QuoteCoupon');
    }

    /**
     * Get quote_coupon_id
     * @return string
     */
    public function getQuoteCouponId()
    {
        return $this->getData(self::QUOTE_COUPON_ID);
    }

    /**
     * Set quote_coupon_id
     * @param string $quoteCouponId
     * @return Orange\Coupon\Api\Data\QuoteCouponInterface
     */
    public function setQuoteCouponId($quoteCouponId, $quote_coupon_id)
    {
        return $this->setData(self::QUOTE_COUPON_ID, $quoteCouponId);
    }

    /**
     * Get quote_id
     * @return string
     */
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * Set quote_id
     * @param string $quote_id
     * @return Orange\Coupon\Api\Data\QuoteCouponInterface
     */
    public function setQuoteId($quote_id)
    {
        return $this->setData(self::QUOTE_ID, $quote_id);
    }

    /**
     * Get rule_id
     * @return string
     */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * Set rule_id
     * @param string $rule_id
     * @return Orange\Coupon\Api\Data\QuoteCouponInterface
     */
    public function setRuleId($rule_id)
    {
        return $this->setData(self::RULE_ID, $rule_id);
    }

    /**
     * Get discount_amount
     * @return string
     */
    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * Set discount_amount
     * @param string $discount_amount
     * @return Orange\Coupon\Api\Data\QuoteCouponInterface
     */
    public function setDiscountAmount($discount_amount)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $discount_amount);
    }

    /**
     * Get subscription_discount_amount
     * @return string
     */
    public function getSubscriptionDiscountAmount()
    {
        return $this->getData(self::SUBSCRIPTION_DISCOUNT_AMOUNT);
    }

    /**
     * Set subscription_discount_amount
     * @param string $subscription_discount_amount
     * @return Orange\Coupon\Api\Data\QuoteCouponInterface
     */
    public function setSubscriptionDiscountAmount($subscription_discount_amount)
    {
        return $this->setData(self::SUBSCRIPTION_DISCOUNT_AMOUNT, $subscription_discount_amount);
    }
    
    /**
     * Get applied Coupon code
     * @return string
     */
    public function getCouponCode()
    {
        return $this->getData(self::COUPONCODE);
    }
    /**
     * Set Coupon code
     * @param string $couponCode
     * @return Orange\Coupon\Api\Data\QuoteCouponInterface
     */
    public function setCouponCode($couponCode)
    {
        return $this->setData(self::COUPONCODE, $couponCode);
    }
	/**
     * Clear Item Discount
     * @param string $quoteId
	 * @param string $itemId
	 * @param string $ruleId     
     */
	 
    public function clearItemData($quoteId,$itemId,$ruleId)
    {
        $quoteCouponCollection = $this->getCollection()
            ->addFieldToFilter('quote_id',$quoteId)
            ->addFieldToFilter('item_id',$itemId)
            ->addFieldToFilter('rule_id',$ruleId);
        foreach ($quoteCouponCollection as $quoteCoupon) {
            $quoteCoupon->delete();
        }
    }
    
	/**
     * Get Discount Amount of Rule
     * @param string $quoteId	 
	 * @param string $ruleId   
	 * @return Collection Orange\Coupon\QuoteCoupon
     */
	 
    public function getRuleDiscountAmount($quoteId,$ruleId)
    {
        $quoteCouponCollection = $this->getCollection()
            ->addFieldToFilter('quote_id',$quoteId)            
            ->addFieldToFilter('rule_id',$ruleId);
        $quoteCouponCollection->getSelect()
            ->columns(['total_discount_amount' => new \Zend_Db_Expr('SUM(discount_amount)')])
            ->columns(['total_subscription_discount_amount' => new \Zend_Db_Expr('SUM(subscription_discount_amount)')])
            ->group('rule_id');
        return $quoteCouponCollection;

    }

    public function clearQuoteDiscountData($quoteId)
    {
        $quoteCouponCollection = $this->getCollection()
            ->addFieldToFilter('quote_id',$quoteId);
        foreach ($quoteCouponCollection as $quoteCoupon) {
            $quoteCoupon->delete();
        }
    }

}

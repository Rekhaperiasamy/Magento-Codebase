<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Coupons
 */


namespace Amasty\Coupons\Plugin;

class PluginObserver
{

    /**
     * @var \Magento\SalesRule\Model\Coupon
     */
    protected $_coupon;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon\Usage
     */
    protected $_couponUsage;

    /**
     * Save used coupon code ID
     *
     * @var
     */
    protected $usedCodes = [];

    /**
     * @param \Magento\SalesRule\Model\Coupon $coupon
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon\Usage $couponUsage
     */
    public function __construct(
        \Magento\SalesRule\Model\Coupon $coupon,
        \Magento\SalesRule\Model\ResourceModel\Coupon\Usage $couponUsage
    ) {
        $this->_coupon = $coupon;
        $this->_couponUsage = $couponUsage;
    }

    public function aroundExecute($subject, \Closure $proceed, $observer)
    {
        $result = $proceed($observer);
        $order = $observer->getEvent()->getOrder();
        $customerId = $order->getCustomerId();
        $coupons =  array_map('trim', explode(',', $order->getCouponCode()));
        if (is_array($coupons) && count($coupons) > 1) {
            $i = 0;
            foreach ($coupons as $coupon) {
                $i++;
                if ($i == 1 || $this->isUsed($coupon)) {
                    continue;
                }
                $this->_coupon->load($coupon, 'code');
                if ($this->_coupon->getId()) {
                    $this->_coupon->setTimesUsed($this->_coupon->getTimesUsed() + 1);
                    $this->_coupon->save();
                    if ($customerId) {
                        $this->_couponUsage->updateCustomerCouponTimesUsed($customerId, $this->_coupon->getId());
                    }
                }
            }
        }
        return $result;
    }

    protected function isUsed($code)
    {
        if (!isset($this->usedCodes[$code])) {
            $this->usedCodes[$code] = 1;
            return false;
        }
        return true;
    }
}

<?php
namespace Orange\SalesRule\Plugin\Magento\SalesRule\Observer;

class SalesOrderAfterPlaceObserver
{
    public function aroundExecute(
        \Magento\SalesRule\Observer\SalesOrderAfterPlaceObserver $subject,
        \Closure $proceed
    ) {
    	echo $observer->getEvent()->getOrder()->getId();
    	exit;
        //Your plugin code
        //$observer = $subject;
        // $order = $observer->getEvent()->getOrder();

        // if (!$order || $order->getDiscountAmount() == 0) {
        //     return $this;
        // }

        // // lookup rule ids
        // $ruleIds = explode(',', $order->getAppliedRuleIds());
        // $ruleIds = array_unique($ruleIds);

        // $ruleCustomer = null;
        // $customerId = $order->getCustomerId();

        // // use each rule (and apply to customer, if applicable)
        // foreach ($ruleIds as $ruleId) {
        //     if (!$ruleId) {
        //         continue;
        //     }
        //     /** @var \Magento\SalesRule\Model\Rule $rule */
        //     $rule = $this->_ruleFactory->create();
        //     $rule->load($ruleId);
        //     if ($rule->getId()) {
        //         $rule->loadCouponCode();
        //         $rule->setTimesUsed($rule->getTimesUsed() + 1);
        //         $rule->save();

        //         if ($customerId) {
        //             /** @var \Magento\SalesRule\Model\Rule\Customer $ruleCustomer */
        //             $ruleCustomer = $this->_ruleCustomerFactory->create();
        //             $ruleCustomer->loadByCustomerRule($customerId, $ruleId);

        //             if ($ruleCustomer->getId()) {
        //                 $ruleCustomer->setTimesUsed($ruleCustomer->getTimesUsed() + 1);
        //             } else {
        //                 $ruleCustomer->setCustomerId($customerId)->setRuleId($ruleId)->setTimesUsed(1);
        //             }
        //             $ruleCustomer->save();
        //         }
        //     }
        // }

        // $this->_coupon->load($order->getCouponCode(), 'code');
        // if ($this->_coupon->getId()) {
        //     $this->_coupon->setTimesUsed($this->_coupon->getTimesUsed() + 1);
        //     $this->_coupon->setOrderIds($order->getIncrementId());
        //     $this->_coupon->save();
        //     if ($customerId) {
        //         $this->_couponUsage->updateCustomerCouponTimesUsed($customerId, $this->_coupon->getId());
        //     }
        // }

        // return $this;
    }
}

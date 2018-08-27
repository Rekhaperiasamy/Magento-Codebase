<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\SalesRule\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderAfterPlaceObserver implements ObserverInterface
{
    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $_ruleCustomerFactory;

    /**
     * @var \Magento\SalesRule\Model\Coupon
     */
    protected $_coupon;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon\Usage
     */
    protected $_couponUsage;

    /**
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param \Magento\SalesRule\Model\Rule\CustomerFactory $ruleCustomerFactory
     * @param \Magento\SalesRule\Model\Coupon $coupon
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon\Usage $couponUsage
     */
    public function __construct(
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\SalesRule\Model\Rule\CustomerFactory $ruleCustomerFactory,
        \Magento\SalesRule\Model\Coupon $coupon,
        \Magento\SalesRule\Model\ResourceModel\Coupon\Usage $couponUsage
    ) {
        $this->_ruleFactory = $ruleFactory;
        $this->_ruleCustomerFactory = $ruleCustomerFactory;
        $this->_coupon = $coupon;
        $this->_couponUsage = $couponUsage;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$order) {
            return $this;
        } 
        if($order->getCouponCode()){
            $couponCodes = explode(",", $order->getCouponCode());
            $usedCouponCodes = count($couponCodes);
            $couponIncrement = 1;
            foreach ($couponCodes as $couponCode) {           
                $this->_coupon->load($couponCode, 'code');
                if ($this->_coupon->getId()) {
                    if($order->getIsVirtual()==1 || $usedCouponCodes != $couponIncrement){
                        $this->_coupon->setTimesUsed($this->_coupon->getTimesUsed() + 1);
                    }
                    if($this->_coupon->getOrderIds()){
                        $OrderIds = $this->_coupon->getOrderIds().'|'.$order->getIncrementId();
                    }
                    else{
                        $OrderIds = $order->getIncrementId();
                    }            
                    $this->_coupon->setOrderIds($OrderIds);
                    $this->_coupon->save();
                }
                $couponIncrement++;                 
            }
        }        

        return $this;
    }
}
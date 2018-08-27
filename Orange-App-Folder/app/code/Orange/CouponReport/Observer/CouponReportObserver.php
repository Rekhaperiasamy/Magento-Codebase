<?php

namespace Orange\CouponReport\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Coupon Report Grids  Observer.
 * Insert data into Coupon Report grid table
 */
class CouponReportObserver implements ObserverInterface
{
	protected $asyncInsert;

	public function __construct(
        
    ) {
        
    }

    /**
     * Handles insertion of the new entity into
     * corresponding grid after order placement.
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$order = $observer->getEvent()->getOrder();
		$order_id = $order->getIncrementId();
		$coupon_code = $order->getCouponCode();
		$status = $order->getStatus();
		$quote_id = $order->getQuoteID();
		$discount_amount = $order->getDiscountAmount();
		if (isset($coupon_code) && !empty($coupon_code)) {
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$this->_couponHelper = $objectManager->create('Orange\CouponReport\Helper\Data');
			$this->_couponHelper->saveCouponData($order_id,$coupon_code,$status,$quote_id,$discount_amount);
		}
	}
}
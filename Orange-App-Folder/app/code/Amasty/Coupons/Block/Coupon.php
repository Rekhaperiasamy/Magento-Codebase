<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Coupons
 */


namespace Amasty\Coupons\Block;

class Coupon extends \Magento\Checkout\Block\Cart\AbstractCart
{

    /**
     * @var \Amasty\Coupons\Helper\Data
     */
    protected $_couponHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Amasty\Coupons\Helper\Data $couponHelper,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->_isScopePrivate = true;
        $this->_couponHelper = $couponHelper;
    }

    /**
     * @return string
     */
    public function getCouponsCode()
    {
        return $this->_couponHelper->getRealAppliedCodes();
    }
	
	public function getCouponData($couponCode)
	{
		return $this->_couponHelper->getCouponData($couponCode);
	}
	
	public function getRuleData($ruleId)
	{
		return $this->_couponHelper->getRuleData($ruleId);
	}
	public function getStoreLabel($ruleId,$storeId)
	{
		return $this->_couponHelper->getStoreLabel($ruleId,$storeId);
	}
}

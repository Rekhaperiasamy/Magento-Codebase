<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Coupons
 */


namespace Amasty\Coupons\Plugin;

class RuleCollection
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
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\SalesRule\Model\Coupon $coupon
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon\Usage $couponUsage
     */
    public function __construct(
        \Magento\SalesRule\Model\Coupon $coupon,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\SalesRule\Model\ResourceModel\Coupon\Usage $couponUsage
    ) {
        $this->_coupon = $coupon;
        $this->_scopeConfig = $scopeConfig;
        $this->_couponUsage = $couponUsage;
    }
    
    public function aroundSetValidationFilter(
        $subject,
        \Closure $proceed,
        $websiteId,
        $customerGroupId,
        $couponCode = '',
        $now = null
    ) {

        $uniqueCodes = $this->_scopeConfig->getValue(
            'amcoupons/general/unique_codes',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $uniqueCodesArray = explode(',', $uniqueCodes);

        if (array_intersect($uniqueCodesArray, explode(',', $couponCode))) {
            $couponCode = array_intersect($uniqueCodesArray, explode(',', $couponCode));
            $couponCode = $couponCode[0];
        }

        $result = $proceed($websiteId, $customerGroupId, $couponCode, $now);

        if ($couponCode !== '' && $couponCode) {
            $select = $subject->getSelect();
            $coupons = explode(',', $couponCode);
            foreach ($coupons as $coupon) {
                $select->orWhere('rule_coupons.code = ? AND main_table.is_active = 1', $coupon);
            }
        }
        
        return $result;
    }
}

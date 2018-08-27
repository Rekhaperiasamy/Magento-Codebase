<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */
namespace Amasty\Rules\Helper;

class Discount extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_ruleDiscountAmount = [];

    protected $_ruleOriginalDiscountAmount = [];

    /**
     * @var \Magento\Framework\App\Config\scopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * Discount constructor.
     *
     * @param \Magento\Framework\App\Config\scopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface  $priceCurrency
     */
    public function __construct(
        \Magento\Framework\App\Config\scopeConfigInterface $scopeConfig,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_priceCurrency = $priceCurrency;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule                      $rule
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    public function setDiscount(
        \Magento\SalesRule\Model\Rule $rule,
        \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData
    ) {
        if (!isset($this->_ruleDiscountAmount[$rule->getId()])) {
            $this->_ruleDiscountAmount[$rule->getId()] = $discountData->getBaseAmount();
            $this->_ruleOriginalDiscountAmount[$rule->getId()] = $discountData->getBaseOriginalAmount();
        } else {
            $this->_ruleDiscountAmount[$rule->getId()] += $discountData->getBaseAmount();
            $this->_ruleOriginalDiscountAmount[$rule->getId()] += $discountData->getBaseOriginalAmount();
        }

        $maxDiscount = $rule->getAmrulesRule()->getMaxDiscount();

        if ($maxDiscount > 0 && $this->_ruleDiscountAmount[$rule->getId()] >= $maxDiscount) {
            $this->decrementDiscount($discountData, $rule);
        }

        return $discountData;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData
     * @param \Magento\SalesRule\Model\Rule                      $rule
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    protected function decrementDiscount(
        \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData,
        \Magento\SalesRule\Model\Rule $rule
    ) {
        if ($discountData->getAmount() === 0) {
            return $discountData;
        }
        $ruleMaxDiscount = $rule->getAmrulesRule()->getMaxDiscount();
        // $baseAmount = min(
            // $ruleMaxDiscount,
            // $ruleMaxDiscount - $this->_ruleDiscountAmount[$rule->getId()] + $discountData->getBaseAmount()
        // );
		
		/** BUG Fix for discount amount is getting doubled after placing order on using expensive and cheapest rule**/
		$baseAmount = min(
            $ruleMaxDiscount,
            $ruleMaxDiscount
        );

        // $baseOriginalAmount = min(
            // $ruleMaxDiscount,
            // $ruleMaxDiscount - $this->_ruleOriginalDiscountAmount[$rule->getId()] + $discountData->getBaseAmount()
        // );
		
		/** BUG Fix for discount amount is getting doubled after placing order on using expensive and cheapest rule**/
		$baseOriginalAmount = min(
            $ruleMaxDiscount,
            $ruleMaxDiscount
        );
		
        $discountData->setBaseAmount($baseAmount);
        $discountData->setAmount($this->_priceCurrency->round($baseAmount));
        $discountData->getBaseOriginalAmount($baseOriginalAmount);
        $discountData->setOriginalAmount($this->_priceCurrency->round($baseOriginalAmount));

        return $discountData;
    }
}

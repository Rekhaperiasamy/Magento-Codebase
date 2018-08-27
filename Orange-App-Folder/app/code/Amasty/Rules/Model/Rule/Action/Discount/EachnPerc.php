<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Rules\Model\Rule\Action\Discount;

class EachnPerc extends AbstractRule
{
    const RULE_VERSION = '1.0.0';

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data Data
     */
    public function calculate($rule, $item, $qty)
    {
        $this->beforeCalculate($rule, $item, $qty);
        $rulePercent = min(100, $rule->getDiscountAmount());
        $discountData = $this->_calculate($rule, $item, $rulePercent);
        $this->afterCalculate($discountData, $rule, $item);
        return $discountData;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $rulePercent
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data Data
     */
    protected function _calculate($rule, $item, $rulePercent)
    {
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();
        $allItems = $this->getSortedItems($item->getAddress(), $rule, 'desc');
        $_rulePct = $rulePercent / 100.0;
        $allItems = $this->skipEachN($allItems, $rule);
        $itemsId = $this->getItemsId($allItems);
        /** @var \Magento\Quote\Model\Quote\Item\AbstractItem $allItem */
        foreach ($allItems as $i => $allItem) {
            if (in_array($item->getAmrulesId(), $itemsId) && $allItem->getAmrulesId()===$item->getAmrulesId()) {
                $itemPrice = $this->validator->getItemPrice($item);
                $baseItemPrice = $this->validator->getItemBasePrice($item);
                $itemOriginalPrice = $this->validator->getItemOriginalPrice($item);
                $baseItemOriginalPrice = $this->validator->getItemBaseOriginalPrice($item);
                $itemQty = $this->getArrayValueCount($itemsId, $item->getAmrulesId());
                $discountData->setAmount($itemQty * $itemPrice * $_rulePct);
                $discountData->setBaseAmount($itemQty * $baseItemPrice * $_rulePct);
                $discountData->setOriginalAmount($itemQty * $itemOriginalPrice * $_rulePct);
                $discountData->setBaseOriginalAmount($itemQty * $baseItemOriginalPrice * $_rulePct);
                if (!$rule->getDiscountQty() || $rule->getDiscountQty() > $itemQty) {
                    $discountPercent = min(100, $item->getDiscountPercent() + $_rulePct*100);
                    $item->setDiscountPercent($discountPercent);
                }
            }
        }

        return $discountData;
    }
}

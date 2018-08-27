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

class BuyxgetyPerc extends Buyxgety
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
        // no conditions for Y elements
        if (!$rule->getAmrulesRule()->getPromoCats() && !$rule->getAmrulesRule()->getPromoSkus()) {
            return $discountData;
        }
        $rulePercent /=  100;
        $address = $item->getAddress();
        $arrX = $this->getTriggerElements($address, $rule);
        $realQty = $this->getTriggerElementQty($arrX);
        $maxQty = $this->ruleQuantity($realQty, $rule);
        // find all allowed Y (discounted) elements and calculate total discount
        $currQty = 0; // there can be less elemnts to discont than $maxQty
        $passedItems = [];
        $lastId = 0;
        $allItems = $this->getSortedItems($address, $rule, 'desc');
        $itemsId = $this->getItemsId($allItems);
        foreach ($allItems as $i=>$allItem) {
            if ($currQty >= $maxQty) {
                break;
            }
            // what should we do with bundles when we treat them as

            // we always skip child items and calculate discounts inside parents
            if (!$this->canProcessItem($allItem, $arrX, $passedItems)) {
                continue;
            }
            // separate items
            $passedItems[$allItem->getAmrulesId()] = $allItem->getAmrulesId();
            if (!$this->isDiscountedItem($rule, $allItem)) {
                continue;
            }
            $qty = $this->getItemQty($item);
            if (($qty==$currQty) && ($lastId == $item->getAmrulesId())) continue;
            $qty = min($maxQty - $currQty, $qty);
            $currQty += $qty;
            if (in_array($item->getAmrulesId(), $itemsId) && $allItem->getAmrulesId()===$item->getAmrulesId()) {
                $itemPrice = $this->rulesProductHelper->getItemPrice($item);
                $baseItemPrice = $this->rulesProductHelper->getItemBasePrice($item);
                $itemOriginalPrice = $this->rulesProductHelper->getItemOriginalPrice($item);
                $baseItemOriginalPrice = $this->rulesProductHelper->getItemBaseOriginalPrice($item);
                $itemQty = $qty;//$this->getArrayValueCount($itemsId, $item->getId());
                $discountData->setAmount($itemQty * $itemPrice * $rulePercent);
                $discountData->setBaseAmount($itemQty * $baseItemPrice * $rulePercent);
                $discountData->setOriginalAmount($itemQty * $itemOriginalPrice * $rulePercent);
                $discountData->setBaseOriginalAmount($itemQty * $baseItemOriginalPrice * $rulePercent);
                if (!$rule->getDiscountQty() || $rule->getDiscountQty() > $qty) {
                    $discountPercent = min(100, $item->getDiscountPercent() + $rulePercent*100);
                    $item->setDiscountPercent($discountPercent);
                }
            }
        }
        return $discountData;
    }
}

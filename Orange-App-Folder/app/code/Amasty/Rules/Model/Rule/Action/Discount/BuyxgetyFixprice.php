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

class BuyxgetyFixprice extends Buyxgety
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
        $discountData = $this->_calculate($rule, $item);
        $this->afterCalculate($discountData, $rule, $item);
        return $discountData;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $rulePercent
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data Data
     */
    protected function _calculate($rule, $item)
    {
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();
        // no conditions for Y elements
        if (!$rule->getAmrulesRule()->getPromoCats() && !$rule->getAmrulesRule()->getPromoSkus()) {
            return $discountData;
        }
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
            // we always skip child items and calculate discounts inside parents
            if (!$this->canProcessItem($allItem, $arrX, $passedItems)) {
                continue;
            }
            // what should we do with bundles when we treat them as
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
                $itemQty = $qty;//$this->getArrayValueCount($itemsId, $item->getId());
                $itemPrice = $this->rulesProductHelper->getItemPrice($item);
                $baseItemPrice = $this->rulesProductHelper->getItemBasePrice($item);
                $baseAmount = $baseItemPrice - $rule->getDiscountAmount();
                $quoteAmount = $this->priceCurrency->convert($rule->getDiscountAmount(), $item->getQuote()->getStore());
                $quoteAmount = $itemPrice - $quoteAmount;
                $itemBaseOriginalPrice = $this->rulesProductHelper->getItemBaseOriginalPrice($item);
                $baseOriginalAmount = $itemBaseOriginalPrice - $rule->getDiscountAmount();
                $itemOriginalPrice = $this->rulesProductHelper->getItemOriginalPrice($item);
                $originalAmount = $itemOriginalPrice - $quoteAmount;
                $discountData->setAmount($itemQty * $quoteAmount);
                $discountData->setBaseAmount($itemQty * $baseAmount);
                $discountData->setOriginalAmount($itemQty * $originalAmount);
                $discountData->setBaseOriginalAmount($itemQty * $baseOriginalAmount);
            }
        }
        return $discountData;
    }
}

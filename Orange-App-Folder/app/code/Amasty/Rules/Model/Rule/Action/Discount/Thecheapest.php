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

class Thecheapest extends AbstractRule
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
        $allItems = $this->getSortedItems($item->getAddress(), $rule, 'asc');
        $sliceQty = $this->ruleQuantity(count($allItems), $rule);
        $allItems = array_slice($allItems, 0, $sliceQty);
        $itemsId = $this->getItemsId($allItems);
        if (in_array($item->getAmrulesId(), $itemsId)) {
			$productType = $item->getProductType();
			//Check whether bundled and apply discount for child
			if($productType == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
				$this->_applyDiscountForSubscription($item,$rule,$rulePercent);				
			}			
            $itemPrice = $this->rulesProductHelper->getItemPrice($item);
            $baseItemPrice = $this->rulesProductHelper->getItemBasePrice($item);
            $itemOriginalPrice = $this->rulesProductHelper->getItemOriginalPrice($item);
            $baseItemOriginalPrice = $this->rulesProductHelper->getItemBaseOriginalPrice($item);
            $itemQty = $this->getArrayValueCount($itemsId, $item->getAmrulesId());
            $_rulePct = $rulePercent / 100;
            $discountData->setAmount($itemQty * $itemPrice * $_rulePct);
            $discountData->setBaseAmount($itemQty * $baseItemPrice * $_rulePct);
            $discountData->setOriginalAmount($itemQty * $itemOriginalPrice * $_rulePct);
            $discountData->setBaseOriginalAmount($itemQty * $baseItemOriginalPrice * $_rulePct);
            if (!$rule->getDiscountQty() || $rule->getDiscountQty() > $itemQty) {
                $discountPercent = min(100, $item->getDiscountPercent() + $rulePercent);
                $item->setDiscountPercent($discountPercent);
            }
        }

        return $discountData;
    }
	
	private function _applyDiscountForSubscription($parentItem,$rule,$rulePercent)
	{		
		$childItems = $parentItem->getChildren();
		foreach($childItems as $childItem):
			$childProductType = $childItem->getProductType();
			if($childProductType == \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL) {
				$this->_applyDiscount($childItem,$rule,$rulePercent);				
			}
		endforeach;		
	}
	
	private function _applyDiscount($item,$rule,$rulePercent)
	{		
		$percent = max(0, 100 - $rulePercent);
		$discountAmount = $item->getOriSubscriptionAmount() * $percent / 100;
		$item->setSubscriptionAmount($discountAmount);
	}
}

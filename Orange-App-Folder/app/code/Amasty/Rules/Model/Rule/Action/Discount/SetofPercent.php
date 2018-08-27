<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */

namespace Amasty\Rules\Model\Rule\Action\Discount;

class SetofPercent extends Setof
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
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    protected function _calculate($rule, $item)
    {
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();
        $address = $item->getAddress();
        list ( $qtySkus,$qtyCats,$sortedProducts ) = $this->prepareSetRule($rule, $address);
        $r = [];
        $discountedQtyByItem = [];
        $promoCats = $this->rulesDataHelper->getRuleCats($rule);
        $promoSku = $this->rulesDataHelper->getRuleSkus($rule);
        $discountedQty = 0;
        foreach ($sortedProducts as $itemId => $allItem) {
            $itemQty = $this->getItemQty($allItem);
            if ($rule->getAmrulesRule()->getPromoSkus() && in_array($allItem->getSku(), $promoSku)) {
                $discountedQty = min($itemQty, $qtySkus[$allItem->getSku()]);
                $discountedQtyByItem[$itemId] = $discountedQty;
                $qtySkus[$allItem->getSku()] -= $discountedQty;
            }

            if ( $rule->getAmrulesRule()->getPromoCats()
                && array_intersect($allItem->getProduct()->getCategoryIds(), $promoCats)
            ) {
                foreach (array_intersect($allItem->getProduct()->getCategoryIds(), $promoCats) as $category) {
                    if (isset($qtyCats[$category])) {
                        $discountedQty = min($itemQty, $qtyCats[$category]);
                        $discountedQtyByItem[$itemId] = $discountedQty;
                        $qtyCats[$category] -= $discountedQty;
                    }
                }
            }

            $percent = min(100, $rule->getDiscountAmount());

            $itemPrice = $this->rulesProductHelper->getItemPrice($allItem);
            $itemBasePrice = $this->rulesProductHelper->getItemBasePrice($allItem);
            $itemOriginalPrice = $this->rulesProductHelper->getItemOriginalPrice($allItem);
            $itemBaseOriginalPrice =  $this->rulesProductHelper->getItemBaseOriginalPrice($allItem);
            $r[$allItem->getAmrulesId()]['discount'] = $itemPrice * ($percent / 100) * $discountedQty;
            $r[$allItem->getAmrulesId()]['original_discount'] = $itemOriginalPrice * ($percent / 100) * $discountedQty;
            $r[$allItem->getAmrulesId()]['base_discount'] = $itemBasePrice * ($percent / 100) * $discountedQty;
            $r[$allItem->getAmrulesId()]['base_item_original_discount'] = $itemBaseOriginalPrice
                * ($percent / 100) * $discountedQty;
            $r[$allItem->getAmrulesId()]['percent'] = $percent;
        }

        if (isset($r[$item->getAmrulesId()])) {
            $discountData->setAmount($r[$item->getAmrulesId()]['discount']);
            $discountData->setBaseAmount($r[$item->getAmrulesId()]['base_discount']);
            $discountData->setOriginalAmount($r[$item->getAmrulesId()]['original_discount']);
            $discountData->setBaseOriginalAmount($r[$item->getAmrulesId()]['base_item_original_discount']);
        }

        return $discountData;
    }
}

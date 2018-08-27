<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */

namespace Amasty\Rules\Model\Rule\Action\Discount;

class SetofFixed extends Setof
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
        $summaryPrice = 0;
        $minQty = $this->getMinQty($rule,$qtySkus,$qtyCats);
        foreach ($sortedProducts as $itemId => $allItem) {
            $itemQty = $this->getItemQty($allItem);
            if ($rule->getAmrulesRule()->getPromoSkus() && in_array($allItem->getSku(), $promoSku)) {
                $discountedQty = min($itemQty, $qtySkus[$allItem->getSku()]);
                $discountedQtyByItem[$itemId] = $discountedQty;
                $qtySkus[$allItem->getSku()] -= $discountedQty;
                $summaryPrice += $discountedQty * ($this->rulesProductHelper->getItemBasePrice($item));
            }

            if ( $rule->getAmrulesRule()->getPromoCats() && array_intersect($allItem->getProduct()->getCategoryIds(), $promoCats) ) {
                foreach (array_intersect($allItem->getProduct()->getCategoryIds(), $promoCats) as $category) {
                    if (isset($qtyCats[$category])) {
                        $discountedQty = min($itemQty, $qtyCats[$category]);
                        $discountedQtyByItem[$itemId] = $discountedQty;
                        $qtyCats[$category] -= $discountedQty;
                        $summaryPrice += $discountedQty * ($this->rulesProductHelper->getItemBasePrice($item));
                    }
                }
            }
        }

        foreach ($sortedProducts as $itemId => $allItem) {
            if (!array_key_exists( $itemId,$discountedQtyByItem ) || !$allItem) {
                continue;
            }

            $percentDiscount = 1 - $rule->getDiscountAmount() * $minQty / $summaryPrice;
            $r[$allItem->getAmrulesId()]['discount'] = $this->rulesProductHelper->getItemPrice($allItem)
                * $percentDiscount * $discountedQtyByItem[$itemId];
            $r[$allItem->getAmrulesId()]['original_discount'] = $this->rulesProductHelper->getItemOriginalPrice($allItem)
                * $percentDiscount * $discountedQtyByItem[$itemId];
            $r[$allItem->getAmrulesId()]['base_discount'] = $this->rulesProductHelper->getItemBasePrice($allItem)
                * $percentDiscount * $discountedQtyByItem[$itemId];
            $r[$allItem->getAmrulesId()]['base_item_original_discount'] = $this->rulesProductHelper->getItemBaseOriginalPrice($allItem)
                * $percentDiscount * $discountedQtyByItem[$itemId];
            $r[$allItem->getAmrulesId()]['percent'] = $percentDiscount;
        }

        if (isset($r[$item->getAmrulesId()])) {
            $discountData->setAmount($r[$item->getAmrulesId()]['discount']);
            $discountData->setBaseAmount($r[$item->getAmrulesId()]['base_discount']);
            $discountData->setOriginalAmount($r[$item->getAmrulesId()]['original_discount']);
            $discountData->setBaseOriginalAmount($r[$item->getAmrulesId()]['base_item_original_discount']);
        }

        return $discountData;
    }

    protected function getMinQty($rule,$qtySkus,$qtyCats)
    {
        $minQty = 0;
        if ($qtySkus) {
            if ($rule->getAmrulesRule()->getPromoSkus()) {
                $minQty = min($qtySkus);
                foreach ($qtySkus as $key => $qtySku) {
                    $qtySkus[$key] = $minQty;
                }
            }
        }

        if ($qtyCats) {
            if ($rule->getAmrulesRule()->getPromoCats()) {
                $minQty = min($qtyCats);
                foreach ($qtyCats as $key => $qtyCat) {
                    $qtyCats[$key] = $minQty;
                }
            }
        }
        return $minQty;
    }
}

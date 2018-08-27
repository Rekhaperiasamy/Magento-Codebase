<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */

namespace Amasty\Rules\Model\Rule\Action\Discount;

abstract class Setof extends AbstractRule
{
    protected function prepareSetRule($rule, $address)
    {
        if (!$rule->getAmrulesRule()->getPromoSkus() && !$rule->getAmrulesRule()->getPromoCats()) {
            return [];
        }
        $sortedProducts = [];
        $qtySkus = [];
        $qtyCats = [];
        if ($rule->getAmrulesRule()->getPromoSkus()) {
            $skus = $this->rulesDataHelper->getRuleSkus($rule);
            foreach ($skus as $sku) {
                $qtySkus[$sku] = 0;
                $priceSkus [$sku] = [];
            }
        }
        if ($rule->getAmrulesRule()->getPromoCats()) {
            $cats = $this->rulesDataHelper->getRuleCats($rule);
            foreach ($cats as $cat) {
                $qtyCats[$cat] = 0;
                $priceCats[$cat] = [];
            }
        }
        $allItems = $this->getSortedItems($address, $rule, 'asc');
        foreach ($allItems as $item) {

            if (!$item->getAmrulesId()) {
                continue;
            }

            if ($rule->getAmrulesRule()->getPromoSkus() && in_array($item->getSku(), $skus)) {
                $qtySkus[$item->getSku()] += $this->getItemQty($item);
            }

            if ($rule->getAmrulesRule()->getPromoCats()
                && array_intersect($item->getProduct()->getCategoryIds(), $cats)
            ) {
                foreach (array_intersect($item->getProduct()->getCategoryIds(), $cats) as $category) {
                    $qtyCats[$category] += $this->getItemQty($item);
                }
            }

            $sortedProducts[$item->getAmrulesId()] = $item;
        }

        $qtySkus = $this->_setMinValue($qtySkus, $rule->getDiscountQty());
        $qtyCats = $this->_setMinValue($qtyCats, $rule->getDiscountQty());
        asort($sortedProducts);

        return [$qtySkus, $qtyCats, $sortedProducts];
    }

    protected function _setMinValue($array, $discountQty)
    {
        if (!$array) {
            return $array;
        }
        $min = min($array);
        if ($min == 0) {
            return [];
        }

        if ($discountQty == 0) $discountQty = $min;
        $min = min($min, (int)$discountQty);
        foreach($array as $key=>$value) {
            $array[$key] = $min;
        }

        return $array;
    }
}

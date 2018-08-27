<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */

namespace Amasty\Rules\Model\Rule\Action\Discount;

abstract class Buyxgety extends AbstractRule
{
    protected $_passedItems = [];

    public function getTriggerElements($address,$rule)
    {
        // find all X (trigger) elements
        $arrX = [];
        foreach ($this->getSortedItems($address, $rule, 'desc') as $item) {
            if ($item->getParentItemId()) {
                continue;
            }

            if (!$item->getAmrulesId()) {
                continue;
            }
            $promoCats = $this->rulesDataHelper->getRuleCats($rule);
            $promoSku = $this->rulesDataHelper->getRuleSkus($rule);
            //if ( Mage::helper('amrules')->isConfigurablePromoItem($item,$promoSku)  ) continue;
            if (!$rule->getActions()->validate($item)) {
                continue;
            }

            if ( in_array( $item->getSku(),$promoSku )  ) {
                continue;
            }
            if (!$promoSku) {
                $itemCats = $item->getCategoryIds();
                if (!$itemCats) $itemCats = $item->getProduct()->getCategoryIds();
                if ( !is_null( $itemCats ) && array_intersect( $promoCats, $itemCats ) ) {
                    continue;
                }
            }
            $arrX[$item->getAmrulesId()] = $item;
        }
        return $arrX;
    }

    public function getTriggerElementQty($arrX)
    {
        $realQty = 0;
        foreach ($arrX as $el) {
            $realQty += $this->getItemQty($el);
        }
        return $realQty;
    }

    public function isDiscountedItem($rule,$item)
    {
        $product = $item->getProduct();
        // for configurable product we need to use the child
        if ($item->getHasChildren() && $item->getProductType() == 'configurable') {
            foreach ($item->getChildren() as $child) {
                // one iteration only
                $product = $child->getProduct();
                // can work for credit cards, but does not work with PayPal, so it is commented out
                //$categoryIds = array_merge($product->getCategoryIds(), $item->getProduct()->getCategoryIds());
                //$product->setCategoryIds($categoryIds);
            }
        }

        $cats = $this->rulesDataHelper->getRuleCats($rule);
        $sku = $this->rulesDataHelper->getRuleSkus($rule);

        $currentSku = $product->getSku();
        $currentCats = $product->getCategoryIds();

        $parent = $item->getParentItem();

        //if ( Mage::helper('amrules')->isConfigurablePromoItem($item,$sku)  ) return true;

        if (isset($parent)) {
            $parentType = $parent->getProductType();
            if ($parentType == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                $currentSku = $item->getParentItem()->getProduct()->getSku();
                $currentCats = $item->getParentItem()->getProduct()->getCategoryIds();
            }
        }

        if (!in_array($currentSku, $sku) && !array_intersect($cats, $currentCats)) {
            return false;
        }
        return true;
    }

    public function canProcessItem($item, $arrX, $passed)
    {
        if (!$item->getAmrulesId()) {
            return false;
        }
        //do not apply discont on triggers
        if (isset($arrX[$item->getAmrulesId()])) {
            return false;
        }

        if (in_array($item->getAmrulesId(), $passed)) {
            return false;
        }

        return true;
    }

    protected function getNQty($rule, $realQty)
    {
        if ($rule->getDiscountStep() > $realQty) {
            return 0;
        } else {
            $qty = $rule->getDiscountQty();
            if (!$qty) $qty = 0;
            $rule->getDiscountStep() == 0 ? $step=1:$step=$rule->getDiscountStep();
            $count =  floor( $realQty / $step ) * $rule->getAmrulesRule()->getData('nqty');
            if ($rule->getDiscountQty()) {
                $nqty  = min($count , $rule->getDiscountQty() );
            } else {
                $nqty = $count;
            }
            return $nqty;
        }
    }
}

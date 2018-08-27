<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\DataObjectFactory;

/**
 * Class Utility
 */
class Utility
{

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $objectFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param DataObjectFactory $objectFactory
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        DataObjectFactory $objectFactory,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->objectFactory = $objectFactory;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Check if rule can be applied for specific address/quote/customer
     *
     * @param \Colissimo\Rule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function canProcessRule($rule, $address)
    {
        if ($rule->hasIsValidForAddress($address) && !$address->isObjectNew()) {
            return $rule->getIsValidForAddress($address);
        }

        $rule->afterLoad();

        if (!$rule->validate($address)) {
            $rule->setIsValidForAddress($address, false);
            return false;
        }

        $rule->setIsValidForAddress($address, true);

        return true;
    }

    /**
     * Return item price
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return float
     */
    public function getItemPrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        $calcPrice = $item->getCalculationPrice();
        return $price === null ? $calcPrice : $price;
    }

    /**
     * Return item base price
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return float
     */
    public function getItemBasePrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return $price !== null ? $item->getBaseDiscountCalculationPrice() : $item->getBaseCalculationPrice();
    }

    /**
     * Return discount item qty
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param \Colissimo\Rule\Model\Rule $rule
     * @return int
     */
    public function getItemQty($item, $rule)
    {
        return $item->getTotalQty();
    }

    /**
     * Merge two sets of ids
     *
     * @param array|string $a1
     * @param array|string $a2
     * @param bool $asString
     * @return array|string
     */
    public function mergeIds($a1, $a2, $asString = true)
    {
        if (!is_array($a1)) {
            $a1 = empty($a1) ? [] : explode(',', $a1);
        }
        if (!is_array($a2)) {
            $a2 = empty($a2) ? [] : explode(',', $a2);
        }
        $a = array_unique(array_merge($a1, $a2));
        if ($asString) {
            $a = implode(',', $a);
        }
        return $a;
    }
}

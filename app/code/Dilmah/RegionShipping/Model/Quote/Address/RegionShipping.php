<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah_RegionShipping
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\RegionShipping\Model\Quote\Address;

class RegionShipping implements \Dilmah\RegionShipping\Model\Quote\Address\RegionShippingInterface
{
    /**
     * @var \Magento\OfflineShipping\Model\SalesRule\Calculator
     */
    protected $calculator;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface          $storeManager
     * @param \Magento\OfflineShipping\Model\SalesRule\Calculator $calculator
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\OfflineShipping\Model\SalesRule\Calculator $calculator
    ) {
        $this->storeManager = $storeManager;
        $this->calculator = $calculator;
    }

    /**
     * {@inheritdoc}
     */
    public function isFreeShipping(\Magento\Quote\Model\Quote $quote, $items)
    {
        /** @var \Magento\Quote\Api\Data\CartItemInterface[] $items */
        if (!count($items)) {
            return false;
        }

        $addressFreeShipping = true;
        $store = $this->storeManager->getStore($quote->getStoreId());
        $this->calculator->init(
            $store->getWebsiteId(),
            $quote->getCustomerGroupId(),
            $quote->getCouponCode()
        );

        /** @var \Magento\Quote\Api\Data\CartItemInterface $item */
        foreach ($items as $item) {
            if ($item->getNoDiscount()) {
                $addressFreeShipping = false;
                $item->setFreeShipping(false);
                continue;
            }

            /* Child item discount we calculate for parent */
            if ($item->getParentItemId()) {
                continue;
            }

            $this->calculator->processFreeShipping($item);
            $itemFreeShipping = (bool) $item->getFreeShipping();
            $addressFreeShipping = $addressFreeShipping && $itemFreeShipping;

            /* Parent free shipping we apply to all children*/
            $this->applyToChildren($item, $itemFreeShipping);
        }

        return $addressFreeShipping;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param bool $isFreeShipping
     * @return void
     */
    protected function applyToChildren(\Magento\Quote\Model\Quote\Item\AbstractItem $item, $isFreeShipping)
    {
        if ($item->getHasChildren() && $item->isChildrenCalculated()) {
            foreach ($item->getChildren() as $child) {
                $this->calculator->processFreeShipping($child);
                if ($isFreeShipping) {
                    $child->setFreeShipping($isFreeShipping);
                }
            }
        }
    }
}

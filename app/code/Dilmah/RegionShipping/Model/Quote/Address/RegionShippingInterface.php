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

interface RegionShippingInterface
{
    /**
     * @param \Magento\Quote\Model\Quote                  $quote
     * @param \Magento\Quote\Api\Data\CartItemInterface[] $items
     *
     * @return bool
     */
    public function isFreeShipping(\Magento\Quote\Model\Quote $quote, $items);
}

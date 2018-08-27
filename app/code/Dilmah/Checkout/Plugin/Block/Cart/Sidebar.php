<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Checkout\Plugin\Block\Cart;

/**
 * Class Sidebar
 * @package Dilmah\Checkout\Plugin\Block\Cart
 */
class Sidebar
{
    /**
     * @var \Dilmah\Checkout\Block\Cart
     */
    protected $dilmahCart;

    /**
     * @var \Magento\Checkout\Block\Cart\Totals
     */
    protected $cartTotals;
    
    /**
     * Sidebar constructor.
     * @param \Dilmah\Checkout\Block\Cart $dilmahCart
     * @param \Magento\Checkout\Block\Cart\Totals $cartTotals
     */
    public function __construct(
        \Dilmah\Checkout\Block\Cart $dilmahCart,
        \Magento\Checkout\Block\Cart\Totals $cartTotals
    ) {
        $this->dilmahCart = $dilmahCart;
        $this->cartTotals = $cartTotals;
    }

    /**
     * after plugin for getConfig function
     *
     * @param \Magento\Checkout\Block\Cart\Sidebar $subject
     * @param array $result
     * @return mixed
     * @SuppressWarnings("unused")
     */
    public function afterGetConfig(\Magento\Checkout\Block\Cart\Sidebar $subject, $result)
    {
        $result['isFreeShippingThresholdActive'] = $this->dilmahCart->isActive();
        $result['threshold'] = $this->dilmahCart->getThreshold();
        $result['freeShippingThresholdMessage'] = $this->dilmahCart->getShippingMessage();

        return $result;
    }
}

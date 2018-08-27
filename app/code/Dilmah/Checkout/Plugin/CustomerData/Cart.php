<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Checkout\Plugin\CustomerData;

/**
 * Class Cart
 * @package Dilmah\Checkout\Plugin\CustomerData
 */
class Cart
{
    /**
     * @var \Dilmah\Checkout\Block\Cart
     */
    protected $dilmahCart;

    /**
     * Cart constructor.
     * @param \Dilmah\Checkout\Block\Cart $dilmahCart
     */
    public function __construct(\Dilmah\Checkout\Block\Cart $dilmahCart)
    {
        $this->dilmahCart = $dilmahCart;
    }

    /**
     * After plugin for getSectionData function
     *
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param array $result
     * @return mixed
     * @SuppressWarnings("unused")
     */
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $result)
    {
        $freeShippingAmount = false;
        if ($this->dilmahCart->isActive()) {
            $freeShippingAmount = $this->dilmahCart->getFreeShippingAlertMessage();
        }
        $result['freeShippingThresholdMessage'] = ($freeShippingAmount !== false)
            ? sprintf(
                __($this->dilmahCart->getShippingMessage()),
                $this->dilmahCart->getCurrencySymbol($this->dilmahCart->getCurrencyCode()) . '' . number_format(
                    $freeShippingAmount,
                    2
                )
            )
            : '';

        return $result;
    }
}

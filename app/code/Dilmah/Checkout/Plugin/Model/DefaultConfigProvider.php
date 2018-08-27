<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Checkout\Plugin\Model;

/**
 * Class DefaultConfigProvider
 * @package Dilmah\Checkout\Plugin\Model
 */
class DefaultConfigProvider
{
    /**
     * @var \Dilmah\Checkout\Block\Cart
     */
    protected $dilmahCart;

    /**
     * DefaultConfigProvider constructor.
     * @param \Dilmah\Checkout\Block\Cart $dilmahCart
     */
    public function __construct(
        \Dilmah\Checkout\Block\Cart $dilmahCart
    ) {
        $this->dilmahCart = $dilmahCart;
    }

    /**
     * after plugin for getConfig function
     *
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param array $result
     * @return mixed
     * @SuppressWarnings("unused")
     */
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, $result)
    {
        $result['isFreeShippingThresholdActive'] = $this->dilmahCart->isActive();
        $result['threshold'] = $this->dilmahCart->getThreshold();
        $result['freeShippingThresholdMessage'] = $this->dilmahCart->getShippingMessage();
        $result['currencySymbol'] = $this->dilmahCart->getCurrencySymbol($result['totalsData']['quote_currency_code']);

        return $result;
    }
}

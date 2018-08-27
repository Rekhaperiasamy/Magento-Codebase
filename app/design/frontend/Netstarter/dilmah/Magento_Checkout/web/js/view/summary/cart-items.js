/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

/*browser:true*/
/*global define*/
define(
    [
        'ko',
        'Magento_Checkout/js/model/totals',
        'uiComponent',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/quote'
    ],
    function (ko, totals, Component, stepNavigator, quote) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/summary/cart-items'
            },
            totals: totals.totals(),
            getItems: totals.getItems(),
            isFreeShippingThresholdActive: window.checkoutConfig.isFreeShippingThresholdActive,
            threshold: window.checkoutConfig.threshold,
            freeShippingThresholdMessage: window.checkoutConfig.freeShippingThresholdMessage,
            currencySymbol: window.checkoutConfig.currencySymbol,
            subtotal: window.checkoutConfig.totalsData.subtotal,
            getItemsQty: function() {
                return parseFloat(this.totals.items_qty);
            },
            isItemsBlockExpanded: function () {
                return quote.isVirtual() || stepNavigator.isProcessed('shipping');
            },
            gotoCart: function() {
                window.location.href = '/checkout/cart';
            },
            getFreeShippingThresholdMessage: function () {
                var str = '';
                if (this.isFreeShippingThresholdActive == 1) {
                    if (this.threshold > 0) {
                        var remaining = this.threshold - this.subtotal;
                        if (remaining > 0){
                            str = this.freeShippingThresholdMessage;
                            var val = this.currencySymbol + remaining.toFixed(2);
                            str = str.replace('%s', val);
                        }
                    }
                }
                return str;
            }
        });
    }
);

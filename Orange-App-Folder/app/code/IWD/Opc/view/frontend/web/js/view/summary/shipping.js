/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'IWD_Opc/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, Component, quote) {
        return Component.extend({
            defaults: {
                template: 'IWD_Opc/summary/shipping'
            },
            quoteIsVirtual: quote.isVirtual(),
            totals: quote.getTotals(),
            getShippingMethodTitle: function() {
                if (!this.isCalculated()) {
                    return '';
                }
                var shippingMethod = quote.shippingMethod();
                return shippingMethod ? shippingMethod.carrier_title + " - " + shippingMethod.method_title : '';
            },
            isCalculated: function() {
                return this.totals() && this.isFullMode() && null != quote.shippingMethod();
            },
            getValue: function() {
                if (!this.isCalculated()) {
                    return this.notCalculatedMessage;
                }
                var price =  this.totals().shipping_amount;
                return this.getFormattedPrice(price);
            }
        });
    }
);

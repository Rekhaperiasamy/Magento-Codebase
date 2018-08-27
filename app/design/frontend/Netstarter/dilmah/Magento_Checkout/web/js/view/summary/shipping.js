/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, Component, quote) {
        var storeId     = window.checkoutConfig.quoteData.store_id;
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/summary/shipping',
                storeId: storeId,
                freeShippingMessage: 'Free Shipping'
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
                    if(this.isGlobalStore()){
                        return this.freeShippingMessage;
                    }
                    return this.notCalculatedMessage;
                }
                var price =  this.totals().shipping_amount;
                return this.getFormattedPrice(price);
            },
            isGlobalStore: function() {
                return this.storeId == 1;
            }
        });
    }
);

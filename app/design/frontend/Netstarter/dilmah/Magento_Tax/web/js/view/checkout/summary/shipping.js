/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/shipping',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, Component, quote) {
        var displayMode = window.checkoutConfig.reviewShippingDisplayMode;
        var storeId     = window.checkoutConfig.quoteData.store_id;
        return Component.extend({
            defaults: {
                displayMode: displayMode,
                template: 'Magento_Tax/checkout/summary/shipping',
                storeId: storeId,
                freeShippingMessage: 'Free Shipping'
            },
            isBothPricesDisplayed: function() {
                return 'both' == this.displayMode
            },
            isIncludingDisplayed: function() {
                return 'including' == this.displayMode;
            },
            isExcludingDisplayed: function() {
                return 'excluding' == this.displayMode;
            },
            isCalculated: function() {
                return this.totals() && this.isFullMode() && null != quote.shippingMethod();
            },
            getIncludingValue: function() {
                if (!this.isCalculated()) {
                    if(this.isGlobalStore()){
                        return this.freeShippingMessage;
                    }
                    return this.notCalculatedMessage;
                }
                var price =  this.totals().shipping_incl_tax;
                return this.getFormattedPrice(price);
            },
            getExcludingValue: function() {
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
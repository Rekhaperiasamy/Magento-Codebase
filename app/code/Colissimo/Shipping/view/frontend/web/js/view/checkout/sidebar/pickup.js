/*global define*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'ko',
        'Colissimo_Shipping/js/view/checkout/address',
        'Colissimo_Shipping/js/view/shipping/pickup',
        'Magento_Checkout/js/model/quote'
    ],
    function(
        Component,
        ko,
        address,
        pickupView,
        quote
    ) {
        'use strict';
        return Component.extend({
            address: address.pickupAddress,
            totals: quote.getTotals(),
            defaults: {
                template: 'Colissimo_Shipping/checkout/sidebar/pickup'
            },

            initialize: function() {
                this._super();
            },

            getPickupAddress: function() {
                return address.pickupAddress();
            },

            updatePickupAddress: function() {
                pickupView.run();
            },

            getShippingMethodTitle: function () {
                var shippingMethod;

                if (!this.isCalculated()) {
                    return '';
                }
                if (!this.address()) {
                    return '';
                }

                shippingMethod = quote.shippingMethod();

                return shippingMethod ? shippingMethod['carrier_title'] + ' - ' + shippingMethod['method_title'] : '';
            },

            isCalculated: function () {
                return this.totals() && this.isFullMode() && quote.shippingMethod() !== null;
            }
        });
    }
);
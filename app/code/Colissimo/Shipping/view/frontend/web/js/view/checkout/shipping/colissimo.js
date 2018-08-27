/*global define*/
define([
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Colissimo_Shipping/js/view/shipping/pickup',
    'Colissimo_Shipping/js/view/checkout/address'
], function (Component, quote, pickupView, address) {
    'use strict';

    return Component.extend({
        shippingMethod: quote.shippingMethod,

        initialize: function () {
            this._super();

            this.shippingMethod.subscribe(function (shippingMethod) {
                var method = shippingMethod.carrier_code + '_' + shippingMethod.method_code;
                var isPickup = method === window.checkoutConfig.colissimoPickup;

                if (!isPickup) {
                    pickupView.pickupRemoveAddress();
                }

                if (isPickup && !address.pickupAddress() && window.checkoutConfig.colissimoOpen === '1') {
                    pickupView.run();
                }
            });
        }
    });
});
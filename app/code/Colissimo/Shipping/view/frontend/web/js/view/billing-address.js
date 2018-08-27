/*global define*/
define(
    [
        'Magento_Checkout/js/model/quote'
    ], function (quote) {
        'use strict';

        return function (target) {
            return target.extend({
                canUseShippingAddress: function() {
                    var canUseShippingAddress = this._super();

                    var method = quote.shippingMethod().carrier_code + '_' + quote.shippingMethod().method_code;

                    if (method === window.checkoutConfig.colissimoPickup) {
                        canUseShippingAddress = false;
                    }

                    return canUseShippingAddress;
                }
            });
        }
    }
);
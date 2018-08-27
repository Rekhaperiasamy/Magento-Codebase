/*global define*/
define(
    [
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/step-navigator',
        'Colissimo_Shipping/js/view/shipping/pickup',
        'Colissimo_Shipping/js/view/checkout/address'
    ],
    function (
        setShippingInformationAction,
        quote,
        stepNavigator,
        pickupView,
        address
    ) {
        'use strict';

        return function (target) {
            return target.extend({
                setShippingInformation: function () {
                    var method = quote.shippingMethod().carrier_code + '_' + quote.shippingMethod().method_code;
                    if (method === window.checkoutConfig.colissimoPickup && !address.pickupAddress()) {
                        if (this.validateShippingInformation()) {
                            setShippingInformationAction().done(
                                function() {
                                    pickupView.run();
                                }
                            );
                        }
                    } else {
                        this._super();
                    }
                }
            });
        }
    });
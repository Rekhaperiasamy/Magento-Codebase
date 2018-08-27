/*browser:true*/
/*global define*/

define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Netresearch_OPS/js/action/submit-retry',
        'Netresearch_OPS/js/action/redirect-on-success'
    ],
    function ($, Component, retryAction, redirectOnSuccessAction) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Netresearch_OPS/payment/ops-redirect'
            },

            redirectAfterPlaceOrder: false,

            initialize: function () {
                this._super();
            },

            getLogoClass: function (code) {
                var logoData = window.checkoutConfig.payment.ops.logoData;

                if (typeof logoData[code].class == 'undefined') {
                    return 'ops-payment-logo-hidden';
                }

                return logoData[code].class
            },

            getLogoSrc: function (code) {
                var logoData = window.checkoutConfig.payment.ops.logoData;

                if (typeof logoData[code].src == 'undefined') {
                    return '';
                }

                return logoData[code].src;
            },


            retryOrder: function () {
                var self = this;
                $.when(
                    retryAction(this.getData(), this.messageContainer)
                ).fail(
                    function () {
                        self.isPlaceOrderActionAllowed(true);
                    }
                ).done(
                    function () {
                        self.afterPlaceOrder();

                        if (self.redirectAfterPlaceOrder) {
                            redirectOnSuccessAction.execute();
                        }
                    }
                );
            },

            placeOrder: function (data, event) {
                if (this.validate()) {
                    if (0 <= window.location.href.indexOf(window.checkoutConfig.checkoutUrl)) {
                        // standard checkout - standard behaviour
                        return this._super();
                    } else {
                        // retry page
                        this.retryOrder();
                    }
                }
            },

            afterPlaceOrder: function () {
                redirectOnSuccessAction.execute();
            },

            getPaymentRedirectMessage: function () {
                return window.checkoutConfig.payment.ops.paymentRedirectMessage;

            }
        });
    }
);

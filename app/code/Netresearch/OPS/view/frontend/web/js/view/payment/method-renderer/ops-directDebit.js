/*browser:true*/
/*global define*/

define(
    [
        'ko',
        'jquery',
        'Netresearch_OPS/js/view/payment/directlink',
        'Magento_Checkout/js/model/quote'
    ],
    function (ko, $, Component, quote) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Netresearch_OPS/payment/ops-directDebit',
                aliasTemplate: 'Netresearch_OPS/payment/alias/directdebit',
            },

            initialize: function () {
                this._super();
                var self = this;
                if (quote.billingAddress() && this.getConfig().countries.filter(function (v) {
                        return v.value == quote.billingAddress().countryId
                    }).length > 0) {
                    self.selector('Direct Debits ' + quote.billingAddress().countryId);
                }
            },

            isInlinePaymentType: function (type) {
                return type != '';
            },

            getSelectorItems: function () {

                return _.map(this.getConfig().countries, function (c) {

                    var countryValue = c.value !== '' ? 'Direct Debits ' + c.value : c.value;

                    return {
                        'value': countryValue,
                        'label': c.label
                    };
                });
            },

            getAdditionalData: function () {
                return {
                    'brand': this.selector(),
                    'alias': this.aliasId()
                };
            },

            getConfig: function () {
                return window.checkoutConfig.payment.opsDirectDebit;
            }
        });
    }
);


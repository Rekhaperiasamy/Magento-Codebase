/*browser:true*/
/*global define*/

define(
    [
        'jquery',
        'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect',
        'mage/translate'
    ],
    function ($, Component, $t) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Netresearch_OPS/payment/ops-directEbanking',
                brand: ''
            },

            initObservable: function () {
                this._super().observe(['brand']);
                return this;
            },

            getData: function () {
                return {
                    "method": this.item.method,
                    "po_number": null,
                    "additional_data": {
                        'directEbanking_brand': this.brand()
                    }
                };
            },

            hasDirectEbankingBrands: function () {
                var brands = window.checkoutConfig.payment.opsDirectEbanking.brands;
                return _.size(brands) > 0;
            },

            isSingleDirectEbankingBrand: function () {
                var brands = window.checkoutConfig.payment.opsDirectEbanking.brands;
                return _.size(brands) == 1;
            },

            getSingleDirectEbankingBrand: function () {
                var brands = window.checkoutConfig.payment.opsDirectEbanking.brands;
                return _.first(_.toArray(brands));
            },

            getBrands: function () {
                var brands = window.checkoutConfig.payment.opsDirectEbanking.brands;
                return _.union(
                    [{'brand': '', 'value': $t('--Please Select--')}],
                    _.map(brands, function (value, key) {
                        return {
                            'brand': key,
                            'value': value
                        };
                    })
                );
            },

            validate: function () {
                if (this.brand() !== '') {
                    return true;
                } else {
                    this.messageContainer.addErrorMessage({message: $t('Please choose one of the options.')});
                    return false;
                }
            }
        });
    }
);

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
                template: 'Netresearch_OPS/payment/ops-interSolve',
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
                        'intersolve_brand': this.brand()
                    }
                };
            },

            hasInterSolveBrands: function () {
                var brands = window.checkoutConfig.payment.opsInterSolve.brands;
                return _.size(brands) > 0;
            },

            isSingleInterSolveBrand: function () {
                var brands = window.checkoutConfig.payment.opsInterSolve.brands;
                return _.size(brands) == 1;
            },

            getSingleInterSolveBrand: function () {
                var brands = window.checkoutConfig.payment.opsInterSolve.brands;
                return _.first(_.toArray(brands));
            },

            getBrands: function () {
                var brands = window.checkoutConfig.payment.opsInterSolve.brands;
                return _.map(brands, function (brand) {
                    return {
                        'brand': brand.brand,
                        'value': brand.value
                    };
                })
            },

            validate: function () {
                if (this.brand() !== '' || this.isSingleInterSolveBrand()) {
                    return true;
                } else {
                    this.messageContainer.addErrorMessage({message: $t('Please choose one of the options.')});
                    return false;
                }
            }
        });
    }
);

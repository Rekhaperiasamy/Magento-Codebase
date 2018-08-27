/*browser:true*/
/*global define*/

define(
    [
        'ko',
        'jquery',
        'Netresearch_OPS/js/view/payment/directlink',
        'mage/translate'
    ],
    function (ko, $, Component, $t) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Netresearch_OPS/payment/ops-cc',
                aliasCvc: null,
            },

            isInlinePaymentType: function (type) {
                var inlinePaymentCcTypes = this.getInlineBrands();
                return -1 < inlinePaymentCcTypes.indexOf(type);
            },

            initObservable: function () {
                this._super().observe(
                    [
                        'aliasCvc',
                    ]
                );
                return this;
            },

            getBrands: function () {
                return this.getConfig().ccBrands;
            },

            getInlineBrands: function () {
                return this.getConfig().inlinePaymentCcTypes;
            },

            getSelectorItems: function () {
                var ccBrands = this.getBrands();
                return _.union(
                    [{'brand': '', 'brandLabel': $t('--Please Select--')}],
                    _.map(ccBrands, function (value) {
                        return {
                            'brand': value,
                            'brandLabel': value
                        };
                    })
                );
            },

            getAdditionalData: function () {
                return {
                    'CC_BRAND': this.getSelectedBrand(),
                    'alias': this.aliasId(),
                    'cvc': this.aliasCvc()
                };
            },

            getConfig: function () {
                return window.checkoutConfig.payment.opsCc;
            }

        });
    }
);


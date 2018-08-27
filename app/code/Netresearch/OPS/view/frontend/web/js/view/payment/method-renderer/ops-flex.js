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
                template: 'Netresearch_OPS/payment/ops-flex',
                flexMethod: {title: null, pm: null, brand: null}
            },

            initObservable: function () {
                this._super().observe(['flexMethod']);
                return this;
            },

            isDefaultOptionActive: function () {
                return window.checkoutConfig.payment.opsFlex.isDefaultOptionActive;
            },

            getDefaultOptionTitle: function () {
                return window.checkoutConfig.payment.opsFlex.defaultOptionTitle;
            },

            getInfoKeyTitle: function () {
                return window.checkoutConfig.payment.opsFlex.infoKeyTitle;
            },

            getFlexMethods: function () {
                var methods = window.checkoutConfig.payment.opsFlex.methods;
                return _.map(methods, function (method) {
                    return {
                        'title': method.title,
                        'pm': method.pm,
                        'brand': method.brand
                    };
                });
            },

            setFlexMethod: function (title, pm, brand) {
                this.flexMethod({'title': title, 'pm': pm, 'brand': brand});
            },

            getData: function () {
                return {
                    "method": this.item.method,
                    "po_number": null,
                    "additional_data": {
                        'flex_title': this.flexMethod().title,
                        'flex_pm': this.flexMethod().pm,
                        'flex_brand': this.flexMethod().brand
                    }
                };
            },

            validate: function () {
                if (this.flexMethod().title !== null) {
                    return true;
                } else {
                    this.messageContainer.addErrorMessage({message: $t('Please choose one of the options.')});
                    return false;
                }
            }

        });
    }
);

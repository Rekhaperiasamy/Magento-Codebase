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
                template: 'Netresearch_OPS/payment/ops-ideal',
                issuerId: ''
            },

            initObservable: function () {
                this._super().observe(['issuerId']);
                return this;
            },

            getData: function () {
                return {
                    "method": this.item.method,
                    "po_number": null,
                    "additional_data": {
                        'iDeal_issuer_id': this.issuerId()
                    }
                };
            },

            getIssuers: function () {
                var issuers = window.checkoutConfig.payment.opsIdeal.issuers;
                return _.union(
                    [{'issuerKey': '', 'issuerValue': $t('--Please Select--')}],
                    _.map(issuers, function (value, key) {
                        return {
                            'issuerKey': key,
                            'issuerValue': value
                        };
                    })
                );
            },

            validate: function () {
                if (this.issuerId() !== '') {
                    return true;
                } else {
                    this.messageContainer.addErrorMessage({message: $t('Please choose one of the options.')});
                    return false;
                }
            }
        });
    }
);

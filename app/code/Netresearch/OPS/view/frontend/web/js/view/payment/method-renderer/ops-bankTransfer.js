/*browser:true*/
/*global define*/

define(
    [
        'jquery',
        'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
    ],
    function ($, Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Netresearch_OPS/payment/ops-bankTransfer',
                countryId: ''
            },

            initObservable: function () {
                this._super().observe(['countryId']);
                return this;
            },

            getData: function () {
                return {
                    "method": this.item.method,
                    "po_number": null,
                    "additional_data": {
                        'country_id': this.countryId()
                    }
                };
            },

            getCountries: function () {
                var countries = window.checkoutConfig.payment.opsBankTransfer.countries;
                return _.map(countries, function (item) {
                    return {
                        'countryId': item.value,
                        'countryName': item.label
                    }
                });
            },

            validate: function () {
                if (this.countryId() !== '') {
                    return true;
                } else {
                    this.messageContainer.addErrorMessage({message: $t('Please choose one of the options.')});
                    return false;
                }
            }
        });
    }
);

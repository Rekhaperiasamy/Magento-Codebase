/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'ko',
        'underscore',
        'IWD_Opc/js/model/checkout-data-resolver'
    ],
    function (ko, _, checkoutDataResolver) {
        "use strict";
        var shippingRates = ko.observableArray([]);
        var shippingCarriers = ko.observableArray([]);

        return {
            isLoading: ko.observable(false),
            /**
             * Set shipping rates
             *
             * @param ratesData
             */
            setShippingRates: function(ratesData) {

                this.prepareShippingCarriers(ratesData);
                shippingRates(ratesData);
                shippingRates.valueHasMutated();
                checkoutDataResolver.resolveShippingRates(ratesData);
            },

            /**
             * Get shipping rates
             *
             * @returns {*}
             */
            getShippingRates: function() {
                return shippingRates;
            },

            /**
             * Get shipping rates
             *
             * @returns {*}
             */
            getShippingCarriers: function() {
                return shippingCarriers;
            },

            prepareShippingCarriers:function(ratesData){
                shippingCarriers([]);
                var carriers = [];

                _.each(ratesData, function (rate) {
                    var carrierTitle = rate['carrier_title'];
                    var carrierCode = rate['carrier_code'];
                    if (carriers.indexOf(carrierCode) === -1) {
                        shippingCarriers.push({carrier_title:carrierTitle, carrier_code:carrierCode});
                        carriers.push(carrierCode);
                    }
                });

                shippingCarriers.valueHasMutated();
            }
        };
    }
);

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, totalsService) {
        'use strict';

        return Component.extend({

            isLoading: totalsService.isLoading,
            isFreeShippingThresholdActive: window.checkoutConfig.isFreeShippingThresholdActive,
            threshold: window.checkoutConfig.threshold,
            freeShippingThresholdMessage: window.checkoutConfig.freeShippingThresholdMessage,
            currencySymbol: window.checkoutConfig.currencySymbol,
            subtotal: window.checkoutConfig.totalsData.subtotal,
            getFreeShippingThresholdMessage: function () {
                var str = '';
                if (this.isFreeShippingThresholdActive == 1) {
                    if (this.threshold > 0) {
                        var remaining = this.threshold - this.subtotal;
                        if (remaining > 0){
                            str = this.freeShippingThresholdMessage;
                            var val = this.currencySymbol + remaining.toFixed(2);
                            str = str.replace('%s', val);
                        }
                    }
                }
                return str;
            }
        });
    }
);

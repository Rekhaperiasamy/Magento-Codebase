/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true*/
/*global define*/
/**
 * Customer balance view model
 */
define(
    [
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_CustomerBalance/js/action/use-balance'
    ],
    function (ko, component, quote, priceUtils, useBalanceAction) {
        var amountSubstracted = ko.observable(window.checkoutConfig.payment.customerBalance.amountSubstracted);

        var isActive = ko.pureComputed(function() {
            var totals = quote.getTotals();
            return !amountSubstracted() && totals().grand_total > 0;
        });

        return component.extend({
            defaults: {
                template: 'Magento_CustomerBalance/payment/customer-balance',
                isEnabled: true
            },
            isAvailable: window.checkoutConfig.payment.customerBalance.isAvailable,
            amountSubstracted: window.checkoutConfig.payment.customerBalance.amountSubstracted,
            usedAmount: window.checkoutConfig.payment.customerBalance.usedAmount,
            balance: window.checkoutConfig.payment.customerBalance.balance,
            initObservable: function () {
                this._super()
                    .observe('isEnabled');
                return this;
            },
            /**
             * Get active status
             *
             * @return {boolean}
             */
            isActive: function() {
                return isActive();
            },
            /**
             * Format customer balance
             *
             * @return {string}
             */
            formatBalance: function() {
                return priceUtils.formatPrice(this.balance, quote.getPriceFormat());
            },
            /**
             * Send request to use balance
             */
            sendRequest: function() {
                amountSubstracted(true);
                useBalanceAction();
            },
            /**
             * Toggle Customer Balance
             */
            toggleCustomerBalance: function() {

                if(document.getElementById('customerbalance-check').checked){
                    //use Store Credits
                    this.sendRequest();
                }else{
                    //remove store credits by redirect to /storecredit/cart/remove/
                    window.location = window.checkoutConfig.payment.customerBalance.balanceRemoveUrl;
                }

                return true;
            },
            /**
             * checkbox status
             */

            isChecked: function() {
               return amountSubstracted();
            }

        });
    }
);

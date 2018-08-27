/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true*/
/*global define*/
define(
    [
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'mage/translate',
        'Magento_Reward/js/action/set-use-reward-points'
    ],
    function (ko, Component, quote, $t, setUseRewardPointsAction) {
        'use strict';
        var rewardConfig = window.checkoutConfig.payment.reward;

        var isChecked = ko.pureComputed(function() {
            return rewardConfig.amountSubstracted;
        });

        var isVisible = ko.pureComputed(function() {
            return (rewardConfig.usedAmount + rewardConfig.balance)>0;
        });

        return Component.extend({
            defaults: {
                template: 'Magento_Reward/payment/reward'
            },

            label: rewardConfig.label,

            isAvailable: function() {
                var subtotal = parseFloat(quote.totals().grand_total),
                    rewardUsedAmount = parseFloat(quote.totals().extension_attributes.base_reward_currency_amount);
                return rewardConfig.isAvailable && ((subtotal > 0)) && rewardUsedAmount <= 0;
            },


            useRewardPoints: function() {
                setUseRewardPointsAction();
            },

            /** check whether we need to show rewards points */

            isVisible: function() {
                return isVisible();
            },

            /**
             * check if checkbox is Checked
             */
            isChecked: function() {
                return isChecked();
            },

            /**
             * check weather rewards points are active
             */
            isActive: function() {
              return isChecked();
            },

            /**
             * Toggle Reward points
             */
            toggleRewardPoints: function() {
                if(document.getElementById('rewardpoints-check').checked){
                    //use RewardPoints
                    this.useRewardPoints();
                }else{
                    //remove reward points by redirect to /reward/cart/remove/
                    window.location = window.location.protocol + '//' + window.location.hostname + '/reward/cart/remove/';
                }

                return true;

            }
        });
    }
);

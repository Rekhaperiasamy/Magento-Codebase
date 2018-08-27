/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'IWD_Opc/js/action/set-coupon-code',
        'IWD_Opc/js/action/cancel-coupon',
        'IWD_Opc/js/iwd/components'
    ],
    function ($, ko, Component, quote, setCouponCodeAction, cancelCouponAction) {
        'use strict';
        var totals = quote.getTotals();
        var couponCode = ko.observable(null);
        if (totals()) {
            couponCode(totals()['coupon_code']);
        }
        var isApplied = ko.observable(couponCode() != null);
        var isLoading = ko.observable(false);
        return Component.extend({
            defaults: {
                template: 'IWD_Opc/payment/discount'
            },
            couponCode: couponCode,
            /**
             * Applied flag
             */
            isApplied: isApplied,
            isLoading: isLoading,
            /**
             * Coupon code application procedure
             */

            apply: function () {
                if (this.validate()) {
                    isLoading(true);
                    setCouponCodeAction(couponCode(), isApplied, isLoading);
					$('.iwd-coupon-code-item').show();//Show Coupon line Item
                }
            },
            /**
             * Cancel using coupon
             */
            cancel: function () {
                if (this.validate()) {
                    isLoading(true);
                    couponCode('');
                    cancelCouponAction(isApplied, isLoading);
					$('.iwd-coupon-code-item').hide();//Hide Coupon line Item
                }
            },
            /**
             * Coupon form validation
             *
             * @returns {boolean}
             */
            validate: function () {
                var form = '#discount-form';
                return $(form).validation() && $(form).validation('isValid');
            }

        });
    }
);

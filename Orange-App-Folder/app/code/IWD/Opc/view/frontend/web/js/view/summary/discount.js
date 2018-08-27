/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
		'jquery'
    ],
    function (Component, quote, $) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'IWD_Opc/summary/discount'
            },
            totals: quote.getTotals(),
            isDisplayed: function() {
                return this.isFullMode() && this.getPureValue() != 0;
            },
            getCouponCode: function() {
                if (!this.totals()) {
                    return null;
                }
                return this.totals()['coupon_code'];
            },
            getPureValue: function() {
                var price = 0;
                if (this.totals() && this.totals().discount_amount) {
                    price = parseFloat(this.totals().discount_amount);
                }
                if(price == 0) {                    
                    var subscriptiontotal = window.checkoutConfig.quoteData.subscription_total;
                    var orisubscriptiontotal = window.checkoutConfig.quoteData.ori_subscription_total;
                    var subscriptionDiscount = orisubscriptiontotal - subscriptiontotal;
                    subscriptionDiscount = subscriptionDiscount * -1;
                    var formattedSubscriptionTotal = this.getFormattedPrice(subscriptiontotal);
                    var formattedSubscriptionDiscount = this.getFormattedPrice(subscriptionDiscount);
                    $('.cart-subscription-price').html(formattedSubscriptionTotal);
                    $('.discount-cart-coupon').html(formattedSubscriptionDiscount);
                    $('.discount-cart-coupon-code').html(window.checkoutConfig.quoteData.coupon_description);
                }
                return price;
            },
            getValue: function() {
                var formattedPrice = this.getFormattedPrice(this.getPureValue());					
				var grandTotal = this.totals()['grand_total'];
				$('.discount-cart-coupon').html(formattedPrice);//on applying coupon
				//$('.discount-cart-coupon-code').html(this.totals()['coupon_code']);//on applying coupon
                $('.discount-cart-coupon-code').html(window.checkoutConfig.quoteData.coupon_description);
				$('.orange-grand-total').html(this.getFormattedPrice(grandTotal));//on applying coupon				
                return formattedPrice;
            }
        });
    }
);

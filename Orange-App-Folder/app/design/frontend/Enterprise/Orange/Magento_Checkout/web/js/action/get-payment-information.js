/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Catalog/js/price-utils'
    ],
    function ($, quote, urlBuilder, storage, errorProcessor, customer, methodConverter, paymentService, priceUtils) {
        'use strict';

        return function (deferred, messageContainer) {
            var serviceUrl;

            deferred = deferred || $.Deferred();
            /**
             * Checkout for guest and registered customer.
             */
            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/payment-information', {
                    cartId: quote.getQuoteId()
                });
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/payment-information', {});
            }

            return storage.get(
                serviceUrl, false
            ).done(
                function (response) {
                    quote.setTotals(response.totals);
                    $('.discount-cart-coupon').html('');//on cancelling coupon
                    $('.discount-cart-coupon-code').html('');//on cancelling coupon
                    $('.cart-subscription-price').html(priceUtils.formatPrice(window.checkoutConfig.quoteData.ori_subscription_total), quote.getPriceFormat());//on cancelling coupon
                    $('.orange-grand-total').html(priceUtils.formatPrice(response.totals['grand_total']), quote.getPriceFormat()); //on cancelling coupon										
                    paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                    deferred.resolve();
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response, messageContainer);
                    deferred.reject();
                }
            );
        };
    }
);

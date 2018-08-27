/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer store credit(balance) application
 */
/*global define,alert*/
define(
    [
        'Magento_Checkout/js/action/set-shipping-information',
        'ko',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/error-processor',
        'IWD_Opc/js/model/payment/discount-messages',
        'mage/storage',
        'Magento_Checkout/js/action/get-totals',
        'mage/translate',
        'Magento_Checkout/js/model/payment/method-list'
    ],
    function (
        setShippingInformationAction,
        ko,
        $,
        quote,
        urlManager,
        paymentService,
        errorProcessor,
        messageContainer,
        storage,
        getTotalsAction,
        $t,
        paymentMethodList
    ) {
        'use strict';
        return function (couponCode, isApplied, isLoading) {
            var quoteId = quote.getQuoteId();
            var url = urlManager.getApplyCouponUrl(couponCode, quoteId);
            var message = $t('Your coupon was successfully applied');
            return storage.put(
                url,
                {},
                false
            ).done(
                function (response) {
                    if (response) {
                        var deferred = $.Deferred();
                        isLoading(false);
                        isApplied(true);
                        getTotalsAction([], deferred);
                        $.when(deferred).done(function() {
                            if(!quote.isVirtual() && quote.shippingMethod()){
                                setShippingInformationAction();
                            }else{
                                paymentService.setPaymentMethods(
                                    paymentMethodList()
                                );
                            }
                        });
                        messageContainer.addSuccessMessage({'message': message});
						window.location.reload();//Quote data not getting refreshed so reloading
                    }
                }
            ).fail(
                function (response) {
                    isLoading(false);
                    errorProcessor.process(response, messageContainer);
                }
            );
        };
    }
);

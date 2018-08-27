define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/place-order'
    ],
    function (quote, urlBuilder, customer, placeOrderService) {
        'use strict';

        return function (paymentData, messageContainer) {
            var serviceUrl, payload;

            payload = {
                cartId: quote.getQuoteId(),
                paymentMethod: paymentData
            };

            serviceUrl = urlBuilder.createUrl('/ops/payment/update', {});

            return placeOrderService(serviceUrl, payload, messageContainer);
        };
    }
);

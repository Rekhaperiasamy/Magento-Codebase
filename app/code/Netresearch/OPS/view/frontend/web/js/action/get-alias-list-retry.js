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
        'Magento_Customer/js/model/customer'
    ],
    function ($, quote, urlBuilder, storage, errorProcessor, customer) {
        'use strict';

        return function (payload, messageContainer) {
            var serviceUrl;

            var deferred = $.Deferred();
            /**
             * Checkout for guest and registered customer.
             */

            serviceUrl = urlBuilder.createUrl('/ops/payment/getAliasListForRetry', {});

            return storage.post(
                serviceUrl,
                JSON.stringify(payload)
            ).fail(
                function (response) {
                    errorProcessor.process(response, messageContainer);
                    deferred.reject();
                }
            );
        };
    }
);

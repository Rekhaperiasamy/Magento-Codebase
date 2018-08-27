/**
 * Created by paul.siedler on 01.03.2017.
 */
define(
    [
        'mage/url'
    ],
    function (url) {
        'use strict';

        return {
            redirectUrl: window.checkoutConfig.payment.ops.paymentRedirectUrl,

            /**
             * Provide redirect to page
             */
            execute: function () {
                window.location.replace(url.build(this.redirectUrl));
            }
        };
    }
);

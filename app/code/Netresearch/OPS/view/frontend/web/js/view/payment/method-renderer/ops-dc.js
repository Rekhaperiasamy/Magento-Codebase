/*browser:true*/
/*global define*/

define(
    [
        'jquery',
        'Netresearch_OPS/js/view/payment/method-renderer/ops-cc'
    ],
    function ($, Component) {
        'use strict';

        return Component.extend({
            getConfig: function () {
                return window.checkoutConfig.payment.opsDc;
            }
        });
    }
);


/*browser:true*/
/*global define*/

define(
    [
        'jquery',
        'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
    ],
    function ($, Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Netresearch_OPS/payment/ops-openInvoice'
            },

            hasInvoiceTerms: function (code) {
                return typeof window.checkoutConfig.payment.opsOpenInvoice[code] == 'object';
            },

            getInvoiceTermsTitle: function (code) {
                return window.checkoutConfig.payment.opsOpenInvoice[code].title;
            },

            getInvoiceTermsLink: function (code) {
                return window.checkoutConfig.payment.opsOpenInvoice[code].link;
            }

        });
    }
);

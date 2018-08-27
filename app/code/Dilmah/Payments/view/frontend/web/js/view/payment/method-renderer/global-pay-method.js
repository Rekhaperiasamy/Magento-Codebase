/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Payments
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
/*browser:true*/
/*global define*/
define(
    [
        'Dilmah_Payments/js/view/payment/method-renderer/global-pay-method-abstract'
    ],
    function (Component) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Dilmah_Payments/payment/global_pay'
            },

            /** Returns payment method instructions */
            getInstructions: function() {
                return window.checkoutConfig.payment.instructions[this.item.method];
            },

            /** Returns payment method terms and conditions */
            getTerms: function() {
                return window.checkoutConfig.payment.dilmah_payments.terms[this.item.method];
            },

            /** Returns payment method terms and conditions URL */
            getTermsUrl: function() {
                return window.checkoutConfig.payment.dilmah_payments.termsUrl[this.item.method];
            }
        });
    }
);

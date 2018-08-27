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
                template: 'Netresearch_OPS/payment/ops-kwixo'
            },

            getPmLogo: function () {
                if (this.item.method == 'ops_kwixoApresReception') {
                    return window.checkoutConfig.payment.opsKwixo.apresReception.pmLogo;
                } else if (this.item.method == 'ops_kwixoComptant') {
                    return window.checkoutConfig.payment.opsKwixo.comptant.pmLogo;
                } else if (this.item.method == 'ops_kwixoCredit') {
                    return window.checkoutConfig.payment.opsKwixo.credit.pmLogo;
                }

                return null;
            }
        });
    }
);

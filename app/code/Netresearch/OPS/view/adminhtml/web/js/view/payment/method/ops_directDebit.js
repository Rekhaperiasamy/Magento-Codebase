/**
 * @package   OPS
 * @copyright 2017 Netresearch GmbH & Co. KG <http://www.netresearch.de>
 * @author    Paul Siedler <paul.siedler@netresearch.de>
 * @license   OSL 3.0
 */
/*browser:true*/
/*global define*/

define(
    [
        'ko',
        'jquery',
        'Netresearch_OPS/js/view/payment/directlink'
    ],
    function (ko, $, Component) {
        'use strict';

        return Component.extend({
            initObservable: function () {
                var self = this;
                self.selector_id = 'OPS_DIRECTDEBIT_BRAND';
                this._super();
                return this;
            },
            getConfig: function () {
                return window.checkoutConfig.payment.opsDirectDebit;
            },
            getCode: function () {
                return 'ops_directDebit';
            },
            onActiveChange: function (isActive) {
                if (!isActive || !this.selector) {
                    return;
                }
                if (!this.aliasId()) {
                    this.resetIFrame();
                }
            },
            onSelectorChange: function (v) {
                if (v) {
                    this.resetIFrame();
                }
            }
        });
    }
);


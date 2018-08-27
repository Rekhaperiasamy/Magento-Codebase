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
            getConfig: function () {
                return window.checkoutConfig.payment.opsCc;
            },
            getCode: function () {
                return 'ops_cc';
            }
        });
    }
);


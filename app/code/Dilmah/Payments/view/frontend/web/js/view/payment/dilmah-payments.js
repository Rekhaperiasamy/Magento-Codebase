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
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'ntb',
                component: 'Dilmah_Payments/js/view/payment/method-renderer/ntb-method'
            }
        ),
        rendererList.push(
            {
                type: 'global_pay',
                component: 'Dilmah_Payments/js/view/payment/method-renderer/global-pay-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
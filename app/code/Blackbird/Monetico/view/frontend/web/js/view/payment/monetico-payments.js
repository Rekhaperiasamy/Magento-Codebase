/**
 * Blackbird Monetico Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category    Blackbird
 * @package     Blackbird_Monetico
 * @copyright   Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author      Blackbird Team
 * @license     https://store.bird.eu/license/
 * @support     help@bird.eu
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
                type: 'monetico_onetime',
                component: 'Blackbird_Monetico/js/view/payment/method-renderer/monetico-onetime'
            },
            {
                type: 'monetico_multitime',
                component: 'Blackbird_Monetico/js/view/payment/method-renderer/monetico-multitime'
            },
            {
                type: 'monetico_1euro',
                component: 'Blackbird_Monetico/js/view/payment/method-renderer/monetico-1euro'
            },
            {
                type: 'monetico_3xcb',
                component: 'Blackbird_Monetico/js/view/payment/method-renderer/monetico-3xcb'
            },
            {
                type: 'monetico_4xcb',
                component: 'Blackbird_Monetico/js/view/payment/method-renderer/monetico-4xcb'
            },
            {
                type: 'monetico_paypal',
                component: 'Blackbird_Monetico/js/view/payment/method-renderer/monetico-paypal'
            }
        );

        /** Add view logic here if needed */
        return Component.extend({});
    }
);

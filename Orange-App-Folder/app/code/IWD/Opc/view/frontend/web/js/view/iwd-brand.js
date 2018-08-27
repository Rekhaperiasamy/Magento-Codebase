define(
    [
        'ko',
        'jquery',
        'uiComponent'
    ],
    function (ko, $, Component) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'IWD_Opc/checkout/iwd-brand'
            }

        });
    }
);

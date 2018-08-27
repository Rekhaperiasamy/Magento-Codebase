/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'IWD_Opc/js/model/agreements/agreement-validator'
    ],
    function (Component, additionalValidators, agreementValidator) {
        'use strict';
        additionalValidators.registerValidator(agreementValidator);
        return Component.extend({});
    }
);

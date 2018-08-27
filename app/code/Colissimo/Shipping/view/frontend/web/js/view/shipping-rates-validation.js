/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../model/shipping-rates-validator',
        '../model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        colissimoShippingRatesValidator,
        colissimoShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('colissimo', colissimoShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('colissimo', colissimoShippingRatesValidationRules);
        return Component;
    }
);

/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_RegionShipping
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../../model/shipping-rates-validator/regionshipping',
        '../../model/shipping-rates-validation-rules/regionshipping'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        regionshippingShippingRatesValidator,
        regionshippingShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('regionshipping', regionshippingShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('regionshipping', regionshippingShippingRatesValidationRules);
        return Component;
    }
);

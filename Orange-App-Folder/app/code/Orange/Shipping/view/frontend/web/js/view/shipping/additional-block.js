define([
    'uiComponent',
	'ko',
	'uiRegistry',
	'jquery',
	'Magento_Checkout/js/model/quote' 
	
], function (Component,ko,registry,$,quote) {
    'use strict';
	var shippingMethod = ko.observable(quote.shippingMethod.carrier_method);
	quote.shippingMethod.subscribe(function (value) {
			if (!value) {
			  return;
			}
			var carrierCode = value.carrier_code;
			if (!carrierCode) {
			  return;
			}
			if (shippingMethod() === carrierCode) {
			  return;
			}
			
			shippingMethod(carrierCode);
		});
    return Component.extend({
        defaults: {
            template: 'Orange_Shipping/shipping/additional-block'
        }
    });
});
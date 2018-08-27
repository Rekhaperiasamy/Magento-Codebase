define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';
    var agreementsConfig = window.checkoutConfig.checkoutAgreements;

    return function (setPaymentAction) {
        return wrapper.wrap(setPaymentAction, function(originalAction, messageContainer, paymentData) {
            if (!agreementsConfig.isEnabled) {
                return originalAction(messageContainer, paymentData);
            }

            var agreementForm = $('#iwd-checkout-agreements-form'),
                agreementData = agreementForm.serializeArray(),
                agreementIds = [];

            agreementData.forEach(function(item) {
                agreementIds.push(item.value);
            });

            if(typeof(paymentData.extension_attributes) === 'undefined'
            || paymentData.extension_attributes === null
            ){
                paymentData.extension_attributes = {agreement_ids: agreementIds};
            }else{
                paymentData.extension_attributes.agreement_ids =  agreementIds;
            }
            return originalAction(messageContainer, paymentData);
        });
    };
});
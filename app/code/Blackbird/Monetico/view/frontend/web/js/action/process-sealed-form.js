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
        'jquery',
        'mage/url',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/error-processor'
    ],
    function (
        $,
        urlBuilder,
        fullScreenLoader,
        errorProcessor
    ) {
        'use strict';

        /**
         * Retrieve the payment sealed form and submit it
         */
        return function (methodCode, containerSelector) {
           var serviceUrl = urlBuilder.build('monetico/payment/sealedform/'),
		 
                params = {
                method_code: methodCode,
                isAjax: true,
                form_key: window.checkoutConfig.formKey
            };

            fullScreenLoader.startLoader();
console.log(serviceUrl);
            $.ajax(
                {url: serviceUrl, data: params, type: 'POST'}
            ).done(function (data) {
                var container = $(containerSelector);
                container.html(data);
                var sealedForm = container.find('#monetico_payment_checkout');
console.log(container);console.log("tesst");console.log(sealedForm);
                if (sealedForm.length) {
                    sealedForm.submit();
                }
            }).fail(function (response) {console.log(response);
                errorProcessor.process(response, $('#', containerId));
            });
        };
    }
);

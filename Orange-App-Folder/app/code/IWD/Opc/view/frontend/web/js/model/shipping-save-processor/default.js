define(
    [
        'jquery',
		'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/action/select-billing-address'
    ],
    function (
	     $,
        ko,
        quote,
        resourceUrlManager,
        storage,
        paymentService,
        methodConverter,
        errorProcessor,
        selectBillingAddressAction
    ) {
        'use strict';

        return {
            saveShippingInformation: function () {
                var payload;
				var session_step =  $("#session_step").val();
				if((session_step == "final") && ($("#step3_tab").hasClass("active"))) {
					$("#payment .loading-mask").show();
				}
                if(!quote.shippingMethod()){
                    return false;
                }
                // if (!quote.billingAddress()) {
                selectBillingAddressAction(quote.shippingAddress());
                // }

                payload = {
                    addressInformation: {
                        shipping_address: quote.shippingAddress(),
                        billing_address: quote.billingAddress(),
                        shipping_method_code: quote.shippingMethod().method_code,
                        shipping_carrier_code: quote.shippingMethod().carrier_code
                    }
                };
		

                return storage.post(
                    resourceUrlManager.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payload)
                ).done(
                    function (response) {
                        quote.setTotals(response.totals);
                        paymentService.setPaymentMethods(methodConverter(response.payment_methods));
						if((session_step == "final") && ($("#step3_tab").hasClass("active"))) {
						setTimeout(function(){
							$("#payment .loading-mask").hide();
							$("#payment input").removeAttr("disabled");
						}, 1000);
						}
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
						if((session_step == "final") && ($("#step3_tab").hasClass("active"))) {
						setTimeout(function(){
							$("#payment .loading-mask").hide();
							$("#payment input").removeAttr("disabled");
						}, 1000);
						}
                    }
                );
            }
        };
    }
);

define(
    [
        'jquery',
        "underscore",
        'uiComponent',
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/checkout-data-resolver'
    ],
    function (
        $,
        _,
        Component,
        ko,
        quote,
        stepNavigator,
        paymentService,
        methodConverter,
        getPaymentInformation,
        checkoutDataResolver
    ) {
        'use strict';
		setTimeout(function(){
		        var number_screen_laod = $("#number_screen").val();
	           var step_number = $("#step").val();
	   
				if($("#step3_tab").hasClass("active")) {
				
					$('.cartitemvalueonepage').html($('.onepagetopcart').html());
					$('#subscription-cart-total-iwd').html($('.subscription-cart-total-iwd').html());
					$('#subsidy-cart-total-iwd').html($('.subsidy-cart-total-iwd').html());
					$('.discount-cart-coupon').html($('.discount-cart-total-iwd').html());
					$('.cartcouponform').html($('.cart_coupon_from').html());
					$('.cartcouponform').show();
					//$('.discount-cart-coupon-code').html($('.discount-coupon-code').html());
					$('.discount-cart-coupon-code').html(window.checkoutConfig.quoteData.coupon_description);
					$('#iwd-grand-total-item').html($('.iwd-grand-total-item').html()); 
					if ($(".shipping-method-title-onepage")[0]){
						$('.shipping-method-rightside').html($('.shipping-method-title-onepage').html());
					} else {
					   //$(".total-shipping-section").hide();
					}
					$('#iwd-grand-total-item').addClass('iwd-grand-total-item');
					$("span.iwd-grand-total-item").addClass('orange');
					$('.cartitemvalueonepage').show();
					$(".iwd-opc-shipping-method").hide();
					$(".shippingaddressbottom").addClass("disable_div");
					$("#payment").show();
					$(".cartitemvalueonepage").show();
					$(".cartitemvalueonepageside").show();
					$(".iwd-discount-options").show();
					if ($('.virtualproductonepage').val()==1) {
							$("#payment .action-update").trigger('click');
							$(".creditcard-label").hide();
							$(".netbanking-label").hide();							
					}
					if ($('.virtualproductonepage').val()==1 && $('.virtualproductonepage_one').val()==1) {
						//$(".total-shipping-section").hide();
					}
				 }
				if($("#step2_tab").hasClass("active")) {
					$(".iwd-opc-shipping-method").show();
					//$(".shippingaddressbottom").show();
					$("#payment").hide();
				}
				   
		}, 2500); 
        /** Set payment methods to collection */
		
        paymentService.setPaymentMethods(methodConverter(window.checkoutConfig.paymentMethods));
		var isLoading = ko.observable(false);
        return Component.extend({
            defaults: {
                template: 'IWD_Opc/payment',
                activeMethod: ''
            },
            isVisible: true,
			isLoading: isLoading,
            quoteIsVirtual: quote.isVirtual(),
            isPaymentMethodsAvailable: ko.computed(function () {
				isLoading(true);
            	for (var i in paymentService.getAvailablePaymentMethods()) {
					if(i == 0) {						
						$('#checkout-payment-method-load').addClass('disable_div');
						$('.step-title').addClass('disable_div');
					}
					else {							
						$('#checkout-payment-method-load').removeClass('disable_div');
						$('.step-title').removeClass('disable_div');
					}
				}
				if(paymentService.getAvailablePaymentMethods().length == 1) {
					isLoading(false);
				}
                return paymentService.getAvailablePaymentMethods().length > 0;
            }),

            initialize: function () {
                this._super();
		this.subsidybank = $('.subsidybank').val();
                this.paymentBasedContent = $('.paymentBasedContent').val();
				this.sessionfinalvalue = $('#session_step').val();
                checkoutDataResolver.resolvePaymentMethod();
                return this;
            },
            getFormKey: function() {
                return window.checkoutConfig.formKey;
            },
            buttonName: function() {
	      var name = $('#payment_button_value').val();
              return name;
            }
        });
    }
);

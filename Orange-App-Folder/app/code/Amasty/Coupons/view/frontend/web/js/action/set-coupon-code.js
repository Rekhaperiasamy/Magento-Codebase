/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

/**
 * Customer store credit(balance) application
 */
/*global define,alert*/
define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Ui/js/model/messageList',
        'mage/storage',
        'Magento_Checkout/js/action/get-totals',
		'Magento_Checkout/js/action/get-payment-information',
        'mage/translate'
    ],
    function (ko, $, quote, urlManager, paymentService, errorProcessor, messageList, storage, getTotalsAction, getPaymentInformationAction, $t) {
        'use strict';
		if($('#discount-code-fake').val()!='') {
			return function (coupon, isApplied, isLoading, isDelete ) {
				var quoteId = quote.getQuoteId();
				var url = urlManager.getApplyCouponUrl(coupon, quoteId);
				var message = $.mage.__("Le code promo %1 a ete applique.").replace('%1', $('#discount-code-fake').val());
				var messageError = $.mage.__("Le code promo %1 n'est pas valide.").replace('%1', $('#discount-code-fake').val());
				var messageDelete = $t('Coupon code was removed');
				//$('.loading-mask').show();
				$('body').loader('show');
				$("#coupon-error-message").hide();
				return storage.put(
					url,
					{},
					false
				).done(
					function (response) {
						if (response) {
							var deferred = $.Deferred();
							isLoading(false);						
							isApplied(true);
							getTotalsAction([], deferred);
							$.when(deferred).done(function() {
								paymentService.setPaymentMethods(
									paymentService.getAvailablePaymentMethods()
								); 
								getPaymentInformationAction(deferred);
								$("#coupon-error-message").hide();
								/** Dynamic coupon item **/
								$('#iwd-grand-total-item').html($('.coupon-grandtotal').html());
								var session_url = $("#coupon_subscription_url").val();
								$.ajax({
								url: session_url,
								type: 'POST',
								dataType: 'json',
								showLoader: true
								}).done(function(data){
									$('#subscription-cart-total-iwd .text-right').html(data['subscription_total']);
									$('.cartcouponform').html(data['coupon_line']);
									var tealium_status = $('input[name="tealium_status"]').val();
									if (tealium_status > 0) {
									  var coupon_code = $('#tealium_coupon_code').val();
									  var coupon_discount = $('#tealium_coupon_discount').val();
									  var tealium_data = $('input[name="tealium_values"]').val();
									  var current_operator = $('#tealium_current_operator').val();
									  var totalpostpaidValue = $("#totalvirtualproduct").val();
									  var res = new Array();
									  if (totalpostpaidValue.indexOf(',') != -1) {
									    var res = totalpostpaidValue.split(",");
									  } else {
									    res = [totalpostpaidValue];
									  }
									  var tealium_number_type = {};
									  for (var i = 0; i < res.length; i++) {
									    var number_type = $("input[name='design_sim_number-" + res[i] + "']:checked").val();
									    tealium_number_type[i] = (number_type == 1) ? 'existing' : 'new';
									  }
									  var num_type = [];
									  for (var n in tealium_number_type) {
									    num_type.push(tealium_number_type[n]);
									  }
									  var d = $.parseJSON(tealium_data);
									  d.page_name = "checkout payment details";
									  d.checkout_step = "payment_details";
									  d.order_discount = coupon_discount;
									  d.order_discount_id = coupon_code;
									  d.number_type = num_type;
									  d.current_operator = current_operator.toLowerCase();
									  utag.view(d);
								  }
								});
								/** EOF Dynamic coupon item **/
								var codeList =  response.split(',');
								$('#discount-code').val(response).change();
								var newCode = $('#discount-code-fake').val();
								if (isDelete==true){
									messageList.addSuccessMessage({'message': messageDelete});
									$('.orange-promocode').val('');
								}else if ( $.inArray( newCode , codeList  )==-1 ) {
									messageList.addErrorMessage({'message': messageError});
								}else{
									messageList.addSuccessMessage({'message': message});
									$('.orange-promocode').val('');
								}
								$('#discount-code-fake').val('');								
								$('body').loader('hide');
							});

						}
					}
				).fail(
					function (response) {
						isLoading(false);						
						// messageList.addErrorMessage({'message': messageError});
						// $("#coupon-error-message").html(messageError);
						// $("#coupon-error-message").show();						
						var messageNotValid = $.mage.__("Le code promo %1 n'est pas valide.").replace('%1',$('#discount-code-fake').val());
						messageList.addErrorMessage({'message': messageNotValid});
						$('body').loader('hide');
						//errorProcessor.process(response);

					}
				);
			};
		}
    }
);

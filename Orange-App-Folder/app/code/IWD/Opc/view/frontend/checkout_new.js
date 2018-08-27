require(['jquery','Magento_Checkout/js/model/quote','Magento_Checkout/js/model/shipping-save-processor', "mage/mage",'mage/translate'], function($,quote,shippingSaveProcessor) {
  'use strict';
  var existing = '';
/*DOB ON Change Validation*/	
  $('#c_dob').change(function() {
	 var prepaid_check           = $("#prepaid_check").val();
	 var number_screen           = $("#number_screen").val();
            if(number_screen == "" && prepaid_check==1)
               {
	                var c_dob                = $.validator.validateElement($("#c_dob"));
	           }
	         else 
	          {
	             c_dob                = $.validator.validateElement($("#c_dob"));
			}
      });
/*DOB ON Iphone-7 */
if ('ontouchstart' in document.documentElement){  
  $('body').css('cursor', 'pointer'); 
}
/*   On Blur Event        */
	$(".validation_check").blur( function(){
	 var prepaid_check           = $("#prepaid_check").val();
	 var number_screen           = $("#number_screen").val();
	 var first_name           = $("#first_name").val();
	 var last_name           = $("#last_name").val();
		var id = "#"+this.id;
    $(checkout_form).validation();
		if(this.id == 'c_dob')
		{
             if(number_screen == "" && prepaid_check==1)
               {
	                var c_dob                = $.validator.validateElement($("#c_dob"));
	           }
	         else 
	          {
	              var c_dob                = $.validator.validateElement($("#c_dob"));
                 if(c_dob == true)
                 {
                    var check_c = date_validation($("#c_dob").val());
	           }
      }
		}
		else if(this.id == 'offer_subscription')
		{
		   if($("#offer_subscription").is(':checked'))
	            {
			      $("#offer_subscription-error").hide();
                  $("#offer_subscription").removeClass('mage-error');
			    }
			 else
			    {
			      $("#offer_subscription-error").show();
	              $("#offer_subscription-error").empty();
                  $("#offer_subscription-error").append($.mage.__('This is a required fields.'));
	              $("#offer_subscription").addClass('mage-error');
			    }
		}
		else if(this.id == 'terms_condition')
		{
		  	if($("#terms_condition").is(':checked'))
	            {
					$("#terms_condition-error").hide();
					$("#terms_condition").removeClass('mage-error');
			    }
			else
			    {
					$("#terms_condition-error").show();
					$("#terms_condition-error").empty();
					$("#terms_condition-error").append($.mage.__('To finalize your order, please read and accept the general terms and conditions of Orange Belgium.'));
					$("#terms_condition").addClass('mage-error');
			    }		
		} else if(id == "#first_name" && first_name !='' && last_name !='') {
			$.validator.validateElement($("#last_name"));
			$.validator.validateElement($("#first_name"));
		} else if(id == "#last_name" && first_name !='' && last_name !='') {
			$.validator.validateElement($("#last_name"));
			$.validator.validateElement($("#first_name"));
		} else {
			$.validator.validateElement($(id));	
	    }
	});
	
	$(".validation_check_dropdown").change( function(){
	 var prepaid_check           = $("#prepaid_check").val();
	 var number_screen           = $("#number_screen").val();
		var id = "#"+this.id;
    $(checkout_form).validation();
		if(this.id == 'c_dob')
		{
             if(number_screen == "" && prepaid_check==1)
               {
	                var c_dob                = $.validator.validateElement($("#c_dob"));
	           }
	         else 
	          {
	              var c_dob                = $.validator.validateElement($("#c_dob"));
                 if(c_dob == true)
                 {
                    var check_c = date_validation($("#c_dob").val());
	           }
      }
		}
		else if(this.id == 'offer_subscription')
		{
		   if($("#offer_subscription").is(':checked'))
	            {
			      $("#offer_subscription-error").hide();
                  $("#offer_subscription").removeClass('mage-error');
			    }
			 else
			    {
			      $("#offer_subscription-error").show();
	              $("#offer_subscription-error").empty();
                  $("#offer_subscription-error").append($.mage.__('This is a required fields.'));
	              $("#offer_subscription").addClass('mage-error');
			    }
		}
		else if(this.id == 'terms_condition')
		{
		  	if($("#terms_condition").is(':checked'))
	            {
					$("#terms_condition-error").hide();
					$("#terms_condition").removeClass('mage-error');
			    }
			else
			    {
					$("#terms_condition-error").show();
					$("#terms_condition-error").empty();
					$("#terms_condition-error").append($.mage.__('To finalize your order, please read and accept the general terms and conditions of Orange Belgium.'));
					$("#terms_condition").addClass('mage-error');
			    }		
		}
		else
		{
      $.validator.validateElement($(id));	
	    }
	});
	
	$('body').on('blur', '#first-accountnumber', function() {
		if($("input[name='transfer_type']:checked" ).val() == 'Domiciliation' ) {
                  if (!$("#first-accountnumber").val()) {				
					  $("#first-accountnumber-error").show();
					  $("#first-accountnumber").addClass('mage-error');
					  return false;
						
				  } else {
				  
				        $("#first-accountnumber").removeClass('mage-error');
						$("#first-accountnumber-error").hide();
						var accountNumberValuee = $("#first-accountnumber").val();
						var accountNumberValue = accountNumberValuee.toUpperCase();
						var slicevalue = accountNumberValue.slice(0,2);
						var orangeFrontNumber = ["BE"];
						var finalVlaue = orangeFrontNumber.indexOf(slicevalue);
						if (finalVlaue < 0 || accountNumberValue.length <19 ) {
							  $("#first-accountnumber-error-data").show();
							  $("#first-accountnumber").addClass('mage-error');
							  return false;
						}
						 $("#first-accountnumber-error-data").hide();
						 $("#first-accountnumber").removeClass('mage-error');
				  }
				}
	});

/* check first step button validation */
var virtualhideDeliveryValue = "2";
var virtualhideOtherOperatorValue = "1";
var totalfirstpostpaidValue = $("#totalvirtualproduct").val();
var firstres = new Array();
if (totalfirstpostpaidValue) {
	if (totalfirstpostpaidValue.indexOf(',') != -1) {
		var firstres = totalfirstpostpaidValue.split(",");
	} else {
		firstres = [totalfirstpostpaidValue];
	}
	 var showValue = 1;
	 var showFirstValue = '';
	 var showSecondValue = '';
	 var showThirdValue = '';
	 var iewChecking = '1';
	 var finalvalue = '';
	 var iewItemsCheck = $("input:hidden[name='iew_items']").val();
	 for (var i = 0; i < firstres.length; i++) {
		//design_sim_number-2445
		if($("input[name='design_sim_number-"+firstres[i]+"']:checked" ).val() == '1' ) {
		  
			if ($("#design_te_existing_number_final_validation_"+firstres[i]).val() == 1 ) {
				showFirstValue = 1;
			} 
			if ($("#design_te_existing_number_final_validation_"+firstres[i]).val() == 2 ) {
				showSecondValue = 4;
				if(iewItemsCheck.indexOf(firstres[i]) <= 0) {
					iewChecking == '2';
				}
			}
			if ($("#design_te_existing_number_final_validation_"+firstres[i]).val() == 3 ) {
				showThirdValue = 2;
			}
			if ($("#design_te_existing_number_final_validation_"+firstres[i]).val() == 0) {
				showValue = 3;
			} 
		} else {
			finalvalue = 4;
		}
		
		if($("input[name='design_sim_number-"+firstres[i]+"']:checked" ).val() == '2' ) {
			showFirstValue = 1;
			$('#designteexistingnumber_'+firstres[i]).hide();
		} else {
			showFirstValue = '';
		}
       }
	 if (showValue == 3) {
		showDivButtons(showValue);
	 } else if (showSecondValue == 4) {
		showDivButtons(showSecondValue);
			if (showSecondValue == 4 && showThirdValue != 2) {
				 showDivButtons(showSecondValue);
				 if (firstres.length > 1 && (showFirstValue ==1 || finalvalue==4)) {
						if ($( "#continue-delete-button" ).hasClass( "continue-delete-step1-existing" )) {
							$("#continue-delete-button").removeClass('continue-delete-step1-existing');
						}
						if (!$( "#continue-delete-button" ).hasClass( "continue-delete-step1" )) {
								$("#continue-delete-button").addClass('continue-delete-step1');
						}
						$("#customer-zone-button").hide();
						$("#customer-zone-des").hide();
						$("#continue-delete-button-top").show();
						$("#continue-delete-button").show();
					} else {
					    $("#customer-zone-button").show();
						$("#customer-zone-des").show();
						$("#continue-delete-button-top").hide();
						$("#continue-delete-button").hide();
					}
			} else if (showThirdValue == 2) {
				if (firstres.length > 1) {
						if ($( "#continue-delete-button" ).hasClass( "continue-delete-step1" )) {
							$("#continue-delete-button").removeClass('continue-delete-step1');
						}
						if (!$( "#continue-delete-button" ).hasClass( "continue-delete-step1-existing" )) {
								$("#continue-delete-button").addClass('continue-delete-step1-existing');
						}
						$("#customer-zone-button").hide();
						$("#customer-zone-des").hide();
						$("#continue-delete-button-top").show();
						$("#continue-delete-button").show();
					} else {
					    $("#customer-zone-button").show();
						$("#customer-zone-des").show();
						$("#continue-delete-button-top").hide();
						$("#continue-delete-button").hide();
					}
			}
		
	 } else if (showThirdValue == 2) {
		showDivButtons(showThirdValue);
	 } else if (showFirstValue == 1) {
	   showDivButtons(showFirstValue);
	 } else {
		showDivButtons(showValue);
	 }
var hideDeliveryValue = $("#hidefreedelivery").val();
for (var i = 0; i < firstres.length; i++) {
	//design_sim_number-2445
		if ($("#design_te_existing_number_final_validation_"+firstres[i]).val() == 3 ||
		$("#design_te_existing_number_final_validation_"+firstres[i]).val() == 1) {
                 $(".postpaid_"+firstres[i]).html($("#designteexistingnumber_"+firstres[i]).val().replace(/[\/. ,:-]+/g, ""));
		}
		
		if ($("#design_te_existing_number_final_validation_"+firstres[i]).val() == 3) {
			virtualhideOtherOperatorValue = "2";
        }		
       
        if ($("#design_te_existing_number_final_validation_"+firstres[i]).val() == 1 && hideDeliveryValue =="1") {
			virtualhideDeliveryValue = "1";
        }	   
	}

}
/**Ajax to load Transfer Screen */
$('body').on('click', '#step_1_exisitng, .continue-delete-step1-existing', function() {
    $(".total-shipping-section").show();
	$("html, body").animate({ scrollTop: 0 }, 600);
	var totalpostpaidValue = $("#totalvirtualproduct").val();
	var res = new Array();
	
	if (totalpostpaidValue.indexOf(',') != -1) {
		var res = totalpostpaidValue.split(",");
	} else {
		res = [totalpostpaidValue];
	}
	
	$(checkout_form).validation();
	// validate existing number
	var  validateresponce = true;
	for (var i = 0; i < res.length; i++) {
	    var iewClassNameCheck = $("input[name='design_sim_number-"+res[i]+"']").attr('class');
		if(~iewClassNameCheck.indexOf("iew_sim")) {
			continue;
		}
	    var validateDesignsimnumber = $("input[name='design_sim_number-"+res[i]+"']:checked").val();
		if (validateDesignsimnumber == '1') {
		    var validate = true;
			if (!$("input[name='design_te_existing_number-"+res[i]+"']").hasClass('valid')) {
				var validate = $.validator.validateElement($("input[name='design_te_existing_number-"+res[i]+"']"));
				if (validate == false) {
					validateresponce = false;
				}
			}
		}
	}
	
	if (validateresponce == false) {
		$('.step-mage-error').show();
		$("html, body").animate({ scrollTop: 0 }, 600);
		return false;
	}

	$('.step-mage-error').hide();
	
	for (var i = 0; i < res.length; i++) {
	//design_sim_number-2445
	   var validateDesignsimnumbervalue = $("input[name='design_sim_number-"+res[i]+"']:checked").val();
	    
		if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 3) {
		    $("#display_div_number_"+res[i]).html($("#designteexistingnumber_"+res[i]).val().replace(/[\/. ,:-]+/g, ""));
               $(".postpaid_"+res[i]).html($("#designteexistingnumber_"+res[i]).val().replace(/[\/. ,:-]+/g, ""));
			$("#transfernumber_"+res[i]).show();
		} else if(validateDesignsimnumbervalue == "2") {
		            $("#transfernumber_"+res[i]).hide();
                    $(".postpaid_"+res[i]).html($(".virtual_new_number").html());
		} 
		
		if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 1) {
			$(".postpaid_"+res[i]).html($("#designteexistingnumber_"+res[i]).val().replace(/[\/. ,:-]+/g, ""));
		}
	}
	
	//$("html, body").animate({ scrollTop: 0 }, 600);
	$("select[name='country_id']").val('BE');
	$("#s_method_freeshipping_freeshipping").trigger('click');
	shippingSaveProcessor.saveShippingInformation(quote.shippingAddress().getType());
	var design_sim_number = $("input[name='design_sim_number']:checked").val();
	$("input:text[name='prefix']").val($("input:radio[name='gender']").val());
	$("input[name='prefix']").keyup();
	//$("input:text[name='custom_attributes[sim_number]']").val(design_sim_number);
	//$("input[name='custom_attributes[sim_number]']").keyup();
	//$("input:text[name='custom_attributes[current_operator]']").val($("#subscription_type option:first").val());
	//$("input:text[name='custom_attributes[current_operator_type]']").val($("input:radio[name='current_operator']").val());
	      var design_te_existing_number_chk = $("#design_te_existing_number").val();
		    var number_url = $("#number_url").val();
			$('#simcard_number').removeAttr( "data-validate" );
			$("#simcard_number").attr("data-validate", "{'custom-required':true, 'validate-number':true}");
		    $("#step1_exiting_num").hide(); 
		    $("#transfer_number").removeClass("disable_div"); 
			$("#transfer_number").removeClass("disable_div"); 
});

  /*Ajax Screen to Load Step2*/
	$('body').on('click', '#step_1,.continue-delete-step1', function() {
	var totalpostpaidValue = $("#totalvirtualproduct").val();
	if (totalpostpaidValue.indexOf(',') != -1) {
		var res = totalpostpaidValue.split(",");
	} else {
		res = [totalpostpaidValue];
	}
	var hideDeliveryValue = $("#hidefreedelivery").val();
	for (var i = 0; i < res.length; i++) {
		if ($("#design_te_existing_number_final_validation_"+res[i]).val() != 1 ) {
				$('.virtualproductonepage_one').val(0);
			}
		if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 3 || $("#design_te_existing_number_final_validation_"+res[i]).val() == 1) {
		    $("#display_div_number_"+res[i]).html($("#designteexistingnumber_"+res[i]).val().replace(/[\/. ,:-]+/g, ""));
               $(".postpaid_"+res[i]).html($("#designteexistingnumber_"+res[i]).val().replace(/[\/. ,:-]+/g, ""));
			$("#transfernumber_"+res[i]).show();
		}
        
        if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 1 && hideDeliveryValue =="1") {
			$(".total-shipping-section").hide();
        } else {
			$(".total-shipping-section").show();
        }		
	}
	
	$("html, body").animate({ scrollTop: 0 }, 600);
	$("select[name='country_id']").val('BE');
	$("#s_method_freeshipping_freeshipping").trigger('click');
	shippingSaveProcessor.saveShippingInformation(quote.shippingAddress().getType());
	var design_sim_number = $("input[name='design_sim_number']:checked").val();
	$("input:text[name='prefix']").val($("input:radio[name='gender']").val());
	$("input[name='prefix']").keyup();
	$("input:text[name='custom_attributes[sim_number]']").val(design_sim_number);
	$("input[name='custom_attributes[sim_number]']").keyup();

	//$("input:text[name='custom_attributes[current_operator]']").val($("#subscription_type option:first").val());
	//$("input:text[name='custom_attributes[current_operator_type]']").val($("input:radio[name='current_operator']").val());

		$('#subs_result').val('1');

   	new_number();
        window.location.hash = "details";

});
function number_validation(numbervalue,deId)
{
			var design_te_existing_number_chk = numbervalue;
			var totalpostpaidValue = $("#totalvirtualproduct").val();
			var res = new Array();
			if(design_te_existing_number_chk.charAt(0) == 0){			
			var stringNewValue = design_te_existing_number_chk.substr(1);
			design_te_existing_number_chk = 32+stringNewValue;					
			}
			if (totalpostpaidValue.indexOf(',') != -1) {
				var res = totalpostpaidValue.split(",");
			} else {
				res = [totalpostpaidValue];
			}
		    var number_url = $("#number_url").val();
			$("#design_te_existing_number_final_validation_"+deId).val('0');
			//showDivButtons
			if (!numbervalue) {
			    showDivButtons(3)
				return false;
			}
		  
	      $.ajax({
           url: number_url,
           data: {design_te_existing_number_chk:design_te_existing_number_chk },
           type: 'get',
           dataType: 'json',
           showLoader: true,
           context: $('#step_1')
           }).done(function(data){
		  $("#design_te_existing_number_final_validation_"+deId).val(data)
		  $('#subs_result').val(data);
	       if(data == 1)
		   {
			  //$("#step1_continue_div_exiting").addClass("disable_div");
			 // $("#step1_continue_div").removeClass("disable_div");
			 // $("#step_1_exisitng").removeClass("disabled");
			  
			 var showValue = 1;
			 var showFirstValue = '';
			 var showSecondValue = '';
			 var showThirdValue = '';
			 for (var i = 0; i < res.length; i++) {
				//design_sim_number-2445
				if($("input[name='design_sim_number-"+res[i]+"']:checked" ).val() == '1' ) {
				  
					if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 1 ) {
						showFirstValue = 1;
					} 
					if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 2 ) {
						showSecondValue = 4;
					}
					if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 3 ) {
						showThirdValue = 2;
					}
					if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 0) {
						showValue = 3;
					} 
				}
				
			 }
			 
			 if (showValue == 3) {
				showDivButtons(showValue)
			 } else if (showSecondValue == 4) {
				//showDivButtons(showThirdValue);
				showDivButtons(showSecondValue);
				
				if (showSecondValue == 4 && showThirdValue != 2) {
				     showDivButtons(showSecondValue);
					 if (res.length > 1) {
							if ($( "#continue-delete-button" ).hasClass( "continue-delete-step1-existing" )) {
								$("#continue-delete-button").removeClass('continue-delete-step1-existing');
							}
							if (!$( "#continue-delete-button" ).hasClass( "continue-delete-step1" )) {
									$("#continue-delete-button").addClass('continue-delete-step1');
							}
							$("#customer-zone-button").hide();
							$("#customer-zone-des").hide();
							$("#continue-delete-button-top").show();
							$("#continue-delete-button").show();
						} else {
						    $("#customer-zone-button").show();
							$("#customer-zone-des").show();
						    $("#continue-delete-button-top").hide();
							$("#continue-delete-button").hide();
						}
				} else if (showThirdValue == 2) {
					if (res.length > 1) {
							if ($( "#continue-delete-button" ).hasClass( "continue-delete-step1" )) {
								$("#continue-delete-button").removeClass('continue-delete-step1');
							}
							if (!$( "#continue-delete-button" ).hasClass( "continue-delete-step1-existing" )) {
									$("#continue-delete-button").addClass('continue-delete-step1-existing');
							}
							$("#customer-zone-button").hide();
							$("#customer-zone-des").hide();
							$("#continue-delete-button-top").show();
							$("#continue-delete-button").show();
						} else {
						    $("#customer-zone-button").show();
								$("#customer-zone-des").show();
						    $("#continue-delete-button-top").hide();
							$("#continue-delete-button").hide();
						}
				}
			 } else if (showThirdValue == 2) {
					showDivButtons(showThirdValue);
			 } else if (showFirstValue == 1) {
			     showDivButtons(showFirstValue);
				if (showSecondValue == 4) {
					 if (res.length > 1) {
							if ($( "#continue-delete-button" ).hasClass( "continue-delete-step1-existing" )) {
								$("#continue-delete-button").removeClass('continue-delete-step1-existing');
							}
							if (!$( "#continue-delete-button" ).hasClass( "continue-delete-step1" )) {
									$("#continue-delete-button").addClass('continue-delete-step1');
							}
							$("#customer-zone-button").hide();
							$("#customer-zone-des").hide();
							$("#continue-delete-button-top").show();
							$("#continue-delete-button").show();
						} else {
						    $("#customer-zone-button").show();
							$("#customer-zone-des").show();
						    $("#continue-delete-button-top").hide();
							$("#continue-delete-button").hide();
						}
				}
			 }
	     
		   }
		   else if(data == 2)
		   {
				 var showValue = 1;
				 var showFirstValue = '';
				 var showSecondValue = '';
				 var showThirdValue = '';
				 var iewChecking = '1';
				 var finalvalue = '';
				 var iewItemsCheck = $("input:hidden[name='iew_items']").val();
				 for (var i = 0; i < res.length; i++) {
					//design_sim_number-2445
					if($("input[name='design_sim_number-"+res[i]+"']:checked" ).val() == '1' ) {
					  
						if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 1 ) {
							showFirstValue = 1;
						} 
						if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 2 ) {
							showSecondValue = 4;
							if(iewItemsCheck.indexOf(res[i]) <= 0) {
								iewChecking == '2';
							}
						}
						if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 3 ) {
							showThirdValue = 2;
						}
						if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 0) {
							showValue = 3;
						} 
					} else {
					    finalvalue = 4;
					}
				 }
				 if (showValue == 3) {
					showDivButtons(showValue);
					return false;
				 } else if (showSecondValue == 4) {
					showDivButtons(showSecondValue);
						if (showSecondValue == 4 && showThirdValue != 2) {
							 showDivButtons(showSecondValue);
							 if (res.length > 1 && (showFirstValue ==1 || finalvalue==4)) {
									if ($( "#continue-delete-button" ).hasClass( "continue-delete-step1-existing" )) {
										$("#continue-delete-button").removeClass('continue-delete-step1-existing');
									}
									if (!$( "#continue-delete-button" ).hasClass( "continue-delete-step1" )) {
											$("#continue-delete-button").addClass('continue-delete-step1');
									}
									$("#customer-zone-button").hide();
									$("#customer-zone-des").hide();
									$("#continue-delete-button-top").show();
									$("#continue-delete-button").show();
								} else {
								     $("#customer-zone-button").show();
								     $("#customer-zone-des").show();
									$("#continue-delete-button-top").hide();
									$("#continue-delete-button").hide();
								}
						} else if (showThirdValue == 2) {
							if (res.length > 1) {
									if ($( "#continue-delete-button" ).hasClass( "continue-delete-step1" )) {
										$("#continue-delete-button").removeClass('continue-delete-step1');
									}
									if (!$( "#continue-delete-button" ).hasClass( "continue-delete-step1-existing" )) {
											$("#continue-delete-button").addClass('continue-delete-step1-existing');
									}
									$("#customer-zone-button").hide();
									$("#customer-zone-des").hide();
									$("#continue-delete-button-top").show();
									$("#continue-delete-button").show();
								} else {
								    $("#customer-zone-button").show();
								    $("#customer-zone-des").show();
									$("#continue-delete-button-top").hide();
									$("#continue-delete-button").hide();
								}
						}
					
				 } else if (showThirdValue == 2) {
					showDivButtons(showThirdValue);
				 } else if (showFirstValue == 1) {
				   showDivButtons(showFirstValue);
				 } else {
					showDivButtons(showValue);
				 }
			
		   }
		      else 
		   {
		     var showValue = 2;
			 var showFirstValue = '';
			 var showSecondValue = '';
			 var showThirdValue = '';
			 for (var i = 0; i < res.length; i++) {
				//design_sim_number-2445
				if($("input[name='design_sim_number-"+res[i]+"']:checked" ).val() == '1' ) {
				  
					if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 1 ) {
						showFirstValue = 1;
					} 
					if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 2 ) {
						showSecondValue = 4;
					}
					if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 3 ) {
						showThirdValue = 2;
					}
					if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 0) {
						showValue = 3;
					} 
				} else {
					if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 0) {
						showFirstValue = 3;
					}
				}
			 }
			 
			 if (showValue == 3) {
				showDivButtons(showValue)
			 } else if (showThirdValue == 2) {
				showDivButtons(showThirdValue);
				if (showSecondValue == 4) {
					showDivButtons(showSecondValue);
				    $("#continue-delete-button").removeClass('continue-delete-step1');
					$("#continue-delete-button").removeClass('continue-delete-step1-existing');
					$("#continue-delete-button").addClass('continue-delete-step1-existing');
					$("#customer-zone-button").hide();
					$("#customer-zone-des").hide();
					$("#continue-delete-button-top").show();
					$("#continue-delete-button").show()
				}
			 } else if (showFirstValue == 1) {
				showDivButtons(showFirstValue);
			 }
			 
		   }
		}); 
}
function new_number()
{
var ajaxStep = "step1";
var checkstepstat = "";
var shippingInformation   = $('form#checkout_form').serializeArray();

	var totalpostpaidValue = $("#totalvirtualproduct").val();
	var res = new Array();
	if (totalpostpaidValue.indexOf(',') != -1) {
		var res = totalpostpaidValue.split(",");
	} else {
		res = [totalpostpaidValue];
	}
	var deleteCartItem = new Array();
	for (var i = 0; i < res.length; i++) {
		if($("#design_te_existing_number_final_validation_"+res[i]).val() == 2  ) {
			deleteCartItem[i] = res[i];
		}
	}
	var deleteCartJoinItem = '';
	if (deleteCartItem) {
		deleteCartJoinItem = deleteCartItem.join(','); 
	} 
   var session_url = $("#session_url").val();
  shippingInformation = JSON.stringify(shippingInformation);
  $.ajax({
        url: session_url,
        data: {saveData:shippingInformation ,aStep: ajaxStep,deletecartJoin: deleteCartJoinItem,checkstepstat: checkstepstat},
        type: 'post',
        dataType: 'json',
        showLoader: true,
        context: $('#step_1')
    }).done(function(data){
	    if (deleteCartJoinItem) {
			window.location.reload();
			return false;
		}
	    $('#step1_number').addClass("disable_div");
		$("#step1_tab" ).removeClass( "first active" );
		$("#step1_tab").addClass("first done");
		$("#step2_tab").addClass("active");
	    $('#step').val("step3");
		$('#step2_information').removeClass("disable_div");
		$(".iwd-opc-shipping-method").show();
		var nationalityCheck = $("input[name='nationality']:checked").val();
		var nationalityCheckreg = $("input[name='registered']:checked").val();
		if(nationalityCheck == "belgian" || nationalityCheck == '' || nationalityCheckreg == '1') {
			$(".shippingaddressbottom").removeClass("disable_div");
		}
		$(".cartitemvalueonepage").hide();
		$(".cartcouponform").hide();
		$('.coupon-message').addClass('disable_div');

		$(".cartitemvalueonepageside").hide();
		$(".iwd-discount-options").hide();
		$('.step-mage-error').hide();
		$("html, body").animate({ scrollTop: 0 }, 600);
        });
}

function final_ajax_call()
{
var ajaxStep = "step1";
var checkstepstat = "";
var shippingInformation   = $('form#checkout_form').serializeArray();
  var session_url = $("#session_url").val();
  shippingInformation = JSON.stringify(shippingInformation);
  $.ajax({
        url: session_url,
        data: {removeFinalStep: "removefinal",checkstepstat: checkstepstat},
        type: 'post',
        dataType: 'json',
        showLoader: false,
        context: $('#step_1')
    }).done(function(data){
        });
}
//IEW Update
			$('body').on('click', '.iew_sim', function() {
				var itemId = $(this).attr('data-itemid');				
				/*$("#step1_continue_div").hide();
				$("#step1_continue_div_exiting").addClass("disable_div");
				$("#step1_continue_div").addClass("disable_div");
				$("#existing_number_postpaid_div").show(); */
				$("#design_te_existing_number_final_validation_"+itemId).val("2");
				var totalpostpaidValue = $("#totalvirtualproduct").val();
				var res = new Array();
				if (totalpostpaidValue.indexOf(',') != -1) {
					var res = totalpostpaidValue.split(",");
				} else {
					res = [totalpostpaidValue];
				}
				
				var data = 2;
				if(data == "2")
				{				  
					var showValue = 4;
					var showFirstValue = '';
					var showSecondValue = '';
					var showThirdValue = '';
					var showfirstValue = '';
				 
					for (var i = 0; i < res.length; i++) {
						//design_sim_number-2445
						if($("input[name='design_sim_number-"+res[i]+"']:checked" ).val() == '1' ) {
						  
							if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 1 ) {
								showFirstValue = 1;
							} 
							if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 2 ) {
								showSecondValue = 4;
							}
							if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 3 ) {
								showThirdValue = 2;
							}
							if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 0) {
								showfirstValue = 3;
							} 
						} else {
							if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 0) {
								showfirstValue = 3;
							}
						}
					}
					if(showFirstValue != 1 && showSecondValue == 4 && showThirdValue !=2 && showfirstValue !=3) {
						showDivButtons(showSecondValue);
						$("#customer-zone-button").show();
						$("#customer-zone-des").show();
						$("#continue-delete-button-top").hide();
						$("#continue-delete-button").hide();
					 }  else if (showValue == 3) {
						showDivButtons(showValue)
					 } else if (showThirdValue == 2) {
						showDivButtons(showThirdValue);
						if (showSecondValue == 4) {
							showDivButtons(showSecondValue);
							 if (res.length > 1) {
								if ($( "#continue-delete-button" ).hasClass( "continue-delete-step1" )) {
									$("#continue-delete-button").removeClass('continue-delete-step1');
								}
								if (!$( "#continue-delete-button" ).hasClass( "continue-delete-step1-existing" )) {
										$("#continue-delete-button").addClass('continue-delete-step1-existing');
								}
								$("#customer-zone-button").hide();
								$("#customer-zone-des").hide();
								$("#continue-delete-button-top").show();
								$("#continue-delete-button").show();
							}
						}
					} else if (showSecondValue == 4) {				 
					showDivButtons(showSecondValue);					
						if (res.length > 1) {
							if ($( "#continue-delete-button" ).hasClass( "continue-delete-step1-existing" )) {
									$("#continue-delete-button").removeClass('continue-delete-step1-existing');
								}
								if (!$( "#continue-delete-button" ).hasClass( "continue-delete-step1" )) {
										$("#continue-delete-button").addClass('continue-delete-step1');
								}
								$("#customer-zone-button").hide();
								$("#customer-zone-des").hide();
								$("#continue-delete-button-top").show();
							$("#continue-delete-button").show();
						} else {
						     $("#customer-zone-button").show();
							 $("#customer-zone-des").show();
							 $("#continue-delete-button-top").hide();
							$("#continue-delete-button").hide();
						}
					} else if (showFirstValue == 1) {				    
						showDivButtons(showFirstValue);
					}
			
				}
			});
			$('body').on('click', '.iew_sim_new', function() {				 				
				//$("#step1_continue_div").addClass("disable_div"); 				
				//$("#existing_number_postpaid_div").hide(); 
			});
			if($('input.iew_sim:radio:checked').length > 0){
			    
				var totalpostpaidValue = $("#totalvirtualproduct").val();
				var res = new Array();
				if (totalpostpaidValue.indexOf(',') != -1) {
					var res = totalpostpaidValue.split(",");
				} else {
					res = [totalpostpaidValue];
				}
				
				    var showValue = 4;
					var showFirstValue = '';
					var showSecondValue = '';
					var showThirdValue = '';
					var showfirstValue = '';
				for (var i = 0; i < res.length; i++) {
						//design_sim_number-2445
						if($("input[name='design_sim_number-"+res[i]+"']:checked" ).val() == '1' ) {
						  
							if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 1 ) {
								showFirstValue = 1;
							} 
							if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 2 ) {
								showSecondValue = 4;
							}
							if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 3 ) {
								showThirdValue = 2;
							}
							if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 0) {
								showfirstValue = 3;
							} 
						} else {
							if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 0) {
								showfirstValue = 3;
							}
						}
					}
			   
			    var totalVirualProduct = $('#totalvirtualproduct').val();
				var totaliewProduct = $("input:hidden[name='iew_items']").val();
				if($('#virtualproductonepage').val() == 1 || totalVirualProduct == totaliewProduct) {
					$("#step1_continue_div_exiting").removeClass("disable_div"); 
					$("#step1_continue_div_exiting").addClass("disable_div");
					$("#step1_continue_div").removeClass("disable_div");
					$("#step1_continue_div").addClass("disable_div");
					$("#existing_number_postpaid_div").show();
					$("#customer-zone-des").show();
					$("#customer-zone-button").show();
                    $("#continue-delete-button-top").hide();
					$("#continue-delete-button").hide();					
				}
				if($('#iewbundle').val() == 1) {
					$("#step1_continue_div_exiting").removeClass("disable_div"); 
					$("#step1_continue_div_exiting").addClass("disable_div");
					$("#step1_continue_div").removeClass("disable_div");
					$("#step1_continue_div").addClass("disable_div");
					$("#existing_number_postpaid_div").show(); 		
				}

			}
			//EOF IEW Update
			
function showDivButtons(showValue) {

	if (showValue == 1) {
		 $('#step1_continue_div').removeClass( "disable_div" );
		 $('#step1_continue_div_exiting').removeClass( "disable_div" );
		 $('#step1_continue_div_exiting').addClass( "disable_div" );
		 $('#step1_continue_div').show();
		 $('#existing_number_postpaid_div').hide();
	 }
	 
	 if (showValue == 2) {
	 
	      // $("#step1_continue_div_exiting").removeClass("disable_div"); 
		  //$("#step1_continue_div_exiting").addClass("disable_div");
		 // $("#step1_continue_div").removeClass("disable_div");
		 // $("#step1_continue_div").addClass("disable_div");
		  $("#existing_number_postpaid_div").hide(); 
		
		  $("#step1_continue_div").addClass("disable_div");
		  $("#step1_continue_div_exiting").removeClass("disable_div");
		  //$("#step_1_exisitng").removeClass("disabled");
	 }
	 
	 if (showValue == 3) {
		  $("#step1_continue_div").addClass("disable_div");
		  $("#step1_continue_div_exiting").removeClass("disable_div");
		   $("#existing_number_postpaid_div").hide(); 
		  //$("#step_1_exisitng").addClass("disabled");
	 }
	 
	 if (showValue == 4) {
		  $("#step1_continue_div_exiting").removeClass("disable_div"); 
		  $("#step1_continue_div_exiting").addClass("disable_div");
		  $("#step1_continue_div").removeClass("disable_div");
		  $("#step1_continue_div").addClass("disable_div");
		  $("#existing_number_postpaid_div").show(); 
	 }

}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////Ajac To Validate Step1 Transfer Screen and Load Step2 Screen/////////////////////////////////////////////////////
	$( "#step_1_number" ).click(function() {
	$("html, body").animate({ scrollTop: 0 }, 600);
	$(checkout_form).validation();
	
	var totalpostpaidValue = $("#totalvirtualproduct").val();
	var simcardAdditional = new Array();
	var res;
	
	if (totalpostpaidValue.indexOf(',') != -1) {
		var res = totalpostpaidValue.split(",");
	} else {
		res = [totalpostpaidValue];
	}
	var simcard_number = new Array();
	var network_customer_number = new Array();
	var subscription_type = new Array();
	var holders_name = new Array();
	var holder_name = new Array();
	for (var i = 0; i < res.length; i++) {
	//design_sim_number-2445
		if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 3 ) {
				
				if($("#simcard_number-"+res[i]+"-error").html()){
				
					simcardAdditional[res[i]] = false;
				}
			//input[name='design_sim_number-"+res[i]+"']:checked
			    var arrayid = res[i];
				simcard_number[arrayid] = $.validator.validateElement($("#simcard_number_"+res[i]));
				var val_bill_in_name = $("input[name='bill_in_name-"+res[i]+"']:checked").val();
				var val_current_operator = $("input[name='current_operator-"+res[i]+"']:checked").val();
				if(val_current_operator == 1) {
					 network_customer_number[res[i]] = $.validator.validateElement($("#network_customer_number_"+res[i]));
				}
				else {
					 network_customer_number[res[i]] = true;
				}
				
				subscription_type[res[i]] = $.validator.validateElement($("#subscription_type_"+res[i]));
				
				if(val_bill_in_name == '1') {
					holders_name[res[i]] = true;
					holder_name[res[i]] = true;
				}
				else {
					 holders_name[res[i]] = $.validator.validateElement($("#holders_name_"+res[i]));
					 holder_name[res[i]] = $.validator.validateElement($("#holder_name_"+res[i]));
				}
			
			} 
	}
	 for (var i = 0; i < res.length; i++) {
	    if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 3 ) {
			if(network_customer_number[res[i]] == false || subscription_type[res[i]] == false || simcard_number[res[i]] == false || holders_name[res[i]] == false || holder_name[res[i]] == false || simcardAdditional[res[i]] == false ) {
			  $("#subscription_div_a" ).removeClass( "btn btn-default dropdown-toggle mage-error" );
			  $("#subscription_div_a").addClass("btn btn-default dropdown-toggle");
			   $("#subscription_div_a_error" ).hide();
			   $('.step-mage-error').show();
			   return false;
			 }
		 }
	 }
	  new_number();
          window.location.hash = "details";
});

//////append all custom values to original magento field values///////
$("input:text[name='first_name']").on('change', function (){
   $("input:text[name='firstname']").val($("input:text[name='first_name']").val());
   $("input[name='firstname']").keyup();
});
$("input:text[name='last_name']").on('change', function (){
   $("input:text[name='lastname']").val($("input:text[name='last_name']").val());
   $("input[name='lastname']").keyup();
});
$("input:text[name='email']").on('change', function (){
   $("#customer-email").val($("input:text[name='email']").val());
   $("#customer-email").change();
});
$("#subscription_type").on('change', function (){
   $("input:text[name='custom_attributes[current_operator]']").val($("#subscription_type option:selected").val());
   $("input:text[name='custom_attributes[current_operator]']").keyup();
});

$(".current_operator").on('click', function (){
    //alert('hello');
   //$("input:text[name='custom_attributes[current_operator_type]']").val(this.value);
   //$("input:text[name='custom_attributes[current_operator_type]']").keyup();
    var str = this.id;
	var res = str.split("_");
	var quote_itemid = res[1] + "_"+res[2];

   if (this.value == '1')
     {
    $('#network_customer_number_'+quote_itemid).removeAttr( "data-validate" );
	$("#network_customer_number_"+quote_itemid).attr("data-validate", "{'custom-required':true, 'min-lenth-validation-newmessage':true}");
	$("#div_network_customer_number_"+quote_itemid).removeClass("disable_div");
	$("#div_bill_in_name_"+quote_itemid).removeClass("disable_div");
	$("#div_holders_name_"+quote_itemid).removeClass("disable_div");
	$("#div_holders_name_"+quote_itemid).addClass("disable_div");
	//$("input:radio[name=bill_in_name-"+quote_itemid+"'").filter('[value=1]').prop('checked', true);
    $("#bill_"+quote_itemid).prop("checked", true);
	$("#network_customer_number_"+quote_itemid ).removeClass( "mage-error" );
	//$("#network_customer_number-error" ).hide();
    $('#holders_name_'+quote_itemid).val("");
	$('#holder_name_'+quote_itemid).val("");
    $("#holders_name_"+quote_itemid ).removeClass( "mage-error" );
	//$("#holders_name-error" ).hide();
	$("#holder_name_"+quote_itemid ).removeClass( "mage-error" );
	//$("#holder_name-error" ).hide();
	
    }
   else
   {
    if(this.value == '2'){
	   $('#network_customer_number_'+quote_itemid).removeAttr( "data-validate" );
	   $("#network_customer_number_"+quote_itemid).attr("data-validate", "{'custom-required':true, 'min-lenth-validation-newmessage':true}");
	}
    $("#bill_"+quote_itemid).prop("checked", true);
   // $('input:radio[name=bill_in_name]').filter('[value=1]').prop('checked', true);
    $('#network_customer_number_'+quote_itemid).val("");
	$('#holders_name_'+quote_itemid).val("");
	$('#holder_name_'+quote_itemid).val("");
	$("#div_network_customer_number_"+quote_itemid).addClass("disable_div");
	$("#div_bill_in_name_"+quote_itemid).addClass("disable_div");
	$("#div_holders_name_"+quote_itemid).addClass("disable_div");
	$("input:radio[name=bill_in_name-"+quote_itemid+"'").filter('[value=0]').prop('checked', true);
   }
});
$(".bill_in_name").on('click', function (){
    var str = this.id;
	var res = str.split("_");
	var quote_itemid = res[1]+"_"+res[2];
	if (this.value == '1')
     {
	   
	   $("#div_holders_name_"+quote_itemid).addClass("disable_div");
	   //$("input:checkbox[name='custom_attributes[bill_in_name]']").prop('checked', true);
	   $('#holders_name_'+quote_itemid).removeAttr( "data-validate" );
	   $('#holder_name_'+quote_itemid).removeAttr( "data-validate" );
	   $('#holders_name_'+quote_itemid).val("");
	   $('#holder_name_'+quote_itemid).val("");
       $('#holders_name_'+quote_itemid).removeClass( "mage-error" );
	   $('#holders_name-error_'+quote_itemid ).hide();
	   $('#holder_name_'+quote_itemid ).removeClass( "mage-error" );
	   $('#holder_name-error').hide();
	 }
	else
	{
	   $("#div_holders_name_"+quote_itemid).removeClass("disable_div");
	   $("#holders_name_"+quote_itemid).removeAttr( "data-validate" );
	   $("#holders_name_"+quote_itemid).attr("data-validate", "{'custom-required':true,'min-lenth-validation':true}");
	   $("#holder_name_"+quote_itemid).removeAttr( "data-validate" );
	   $("#holder_name_"+quote_itemid).attr("data-validate", "{'custom-required':true,'min-lenth-validation':true}");
	   //$("input:checkbox[name='custom_attributes[bill_in_name]']").prop('checked', false);
	  // $("input:text[name='custom_attributes[bill_in_name]']").keyup();
	}   
});
$("input:text[name='network_customer_number']").on('change', function (){
   $("input:text[name='custom_attributes[network_customer_number]']").val($("input:text[name='network_customer_number']").val());
   $("input:text[name='custom_attributes[network_customer_number]']").keyup();
});
$("input:text[name='simcard_number']").on('change', function (){
   $("input:text[name='custom_attributes[simcard_number]']").val($("input:text[name='simcard_number']").val());
   $("input:text[name='custom_attributes[simcard_number]']").keyup();
});
$("input:text[name='holders_name']").on('change', function (){
   $("input:text[name='custom_attributes[holders_name]']").val($("input:text[name='holders_name']").val());
   $("input:text[name='custom_attributes[holders_name]']").keyup();
});
$("input:text[name='holder_name']").on('change', function (){
   $("input:text[name='custom_attributes[holder_name]']").val($("input:text[name='holder_name']").val());
   $("input:text[name='custom_attributes[holder_name]']").keyup();
});
$("input:radio[name='ex_invoice']").on('change', function (){
  
	if (this.value == 'yes')
	{
	  $("input:checkbox[name='custom_attributes[ex_invoice]']").prop('checked', true);
	 $('input:radio[name=cu_ex_invoice_bill_in_name]').filter('[value=1]').prop('checked', true);
     $("#div_ex_invoice_cust_number").removeClass("disable_div");	
	 $("#div_ex_invoice_bill_in_name").removeClass("disable_div");	
	 $("#div_ex_invoice_cust_details").removeClass("disable_div");	
	 $("#div_ex_invoice_cust_details").addClass("disable_div");	
	 $('#cu_ex_invoice_cust_number').removeAttr( "data-validate" );
     $("#cu_ex_invoice_cust_number").attr("data-validate", "{'custom-required':true,'min-lenth-validation-newmessage':true}");
	 $('#cu_ex_invoice_cust_dob').val("");
	 $('#cu_ex_invoice_cust_firstname').val("");
	 $('#cu_ex_invoice_cust_surname').val( "" );
	}
	else
    {
	  $("input:checkbox[name='custom_attributes[ex_invoice]']").prop('checked', false);
	  $('input:radio[name=cu_ex_invoice_bill_in_name]').filter('[value=1]').prop('checked', true);
	  $("input:text[name='custom_attributes[ex_invoice]']").keyup();
	  $("#div_ex_invoice_cust_number").addClass("disable_div");	
	  $("#div_ex_invoice_bill_in_name").addClass("disable_div");
	  $("#div_ex_invoice_cust_details").removeClass("disable_div");	
	  $("#div_ex_invoice_cust_details").addClass("disable_div");	
	  $('#cu_ex_invoice_cust_number').removeAttr( "data-validate" );
	  $('#cu_ex_invoice_cust_number').val( "" );
	  $("#cu_ex_invoice_cust_number" ).removeClass( "mage-error" );
	  $("#cu_ex_invoice_cust_number-error" ).hide();
	  $('#cu_ex_invoice_cust_dob').val( "" );
	  $("#cu_ex_invoice_cust_dob" ).removeClass( "mage-error" );
	  $("#cu_ex_invoice_cust_dob-error" ).hide();
	  $('#cu_ex_invoice_cust_firstname').val( "" );
	  $("#cu_ex_invoice_cust_firstname" ).removeClass( "mage-error" );
	  $("#cu_ex_invoice_cust_firstname-error" ).hide();
	  $('#cu_ex_invoice_cust_surname').val( "" );
	  $("#cu_ex_invoice_cust_surname" ).removeClass( "mage-error" );
	  $("#cu_ex_invoice_cust_surname-error" ).hide();

	}
		  $("input:text[name='custom_attributes[ex_invoice]']").keyup();	
});
$("#cu_ex_invoice_cust_number").on('change', function (){
	$("input:text[name='custom_attributes[ex_invoice_cust_number]']").val($("#cu_ex_invoice_cust_number").val());
	$("input:text[name='custom_attributes[ex_invoice_cust_number]']").keyup();
});
$("input:radio[name='cu_ex_invoice_bill_in_name']").on('change', function (){
	if (this.value == '1')
	{
	   $("#div_ex_invoice_cust_details").addClass("disable_div");	
	   $("input:checkbox[name='custom_attributes[ex_invoice_bill_in_name]']").prop('checked', true);
	   $('#cu_ex_invoice_cust_surname').removeAttr( "data-validate" );
	   $('#cu_ex_invoice_cust_firstname').removeAttr( "data-validate" );
	   $('#cu_ex_invoice_cust_dob').removeAttr( "data-validate" );
	   $('#cu_ex_invoice_cust_dob').val( "" );
	   $('#cu_ex_invoice_cust_firstname').val( "" );
	   $('#cu_ex_invoice_cust_surname').val( "" );
	  $("#cu_ex_invoice_cust_surname" ).removeClass( "mage-error" );
	  $("#cu_ex_invoice_cust_surname-error" ).hide();
	  $("#cu_ex_invoice_cust_firstname" ).removeClass( "mage-error" );
	  $("#cu_ex_invoice_cust_firstname-error" ).hide();
	  $("#cu_ex_invoice_cust_dob" ).removeClass( "mage-error" );
	  $("#cu_ex_invoice_cust_dob-error" ).hide();
	   
	}
	else
    {
	 $('#cu_ex_invoice_cust_surname').removeAttr( "data-validate" );
     $("#cu_ex_invoice_cust_surname").attr("data-validate", "{'custom-required':true, 'validate-novalidation':true,'min-lenth-validation':true}");
	 $('#cu_ex_invoice_cust_firstname').removeAttr( "data-validate" );
     $("#cu_ex_invoice_cust_firstname").attr("data-validate", "{'custom-required':true, 'validate-novalidation':true,'min-lenth-validation':true}");
	 $('#cu_ex_invoice_cust_dob').removeAttr( "data-validate" );
     $("#cu_ex_invoice_cust_dob").attr("data-validate", "{'custom-required':true,'validate-dob-date':true, 'validate-dob-month':true,'dob-date-validation':true}"); 
     $("#div_ex_invoice_cust_details").removeClass("disable_div");	
	 $("input:checkbox[name='custom_attributes[ex_invoice_bill_in_name]']").prop('checked', false);
   }	
	$("input:text[name='custom_attributes[ex_invoice_bill_in_name]']").keyup();
   });
$("#cu_ex_invoice_cust_surname").on('change', function (){
	$("input:text[name='custom_attributes[ex_invoice_cust_surname]']").val($("#cu_ex_invoice_cust_surname").val());
	$("input:text[name='custom_attributes[ex_invoice_cust_surname]']").keyup();
});

$("#cu_ex_invoice_cust_firstname").on('change', function (){
	$("input:text[name='custom_attributes[ex_invoice_cust_firstname]']").val($("#cu_ex_invoice_cust_firstname").val());
	$("input:text[name='custom_attributes[ex_invoice_cust_firstname]']").keyup();
});

$("#cu_ex_invoice_cust_dob").on('change', function (){
	$("input:text[name='custom_attributes[ex_invoice_cust_dob]']").val($("#cu_ex_invoice_cust_dob").val());
	$("input:text[name='custom_attributes[ex_invoice_cust_dob]']").keyup();
});
$("input:radio[name='discount_f']").on('change', function (){
	if (this.value == 'yes')
	{
	 $("input:checkbox[name='custom_attributes[discount_f]']").prop('checked', true);
	 $("input:text[name='custom_attributes[discount_f]']").keyup();
	 $('input:radio[name=cu_discount_f_bill_in_name]').filter('[value=1]').prop('checked', true);
	 $("#div_discount_cust_number").removeClass("disable_div");	
	 $("#div_discount_bill_in_name").removeClass("disable_div");	
	 $("#div_discount_cust_details").removeClass("disable_div");	
	 $("#div_discount_cust_details").addClass("disable_div");	
	 $('#cu_discount_f_cust_number').removeAttr( "data-validate" );
     $("#cu_discount_f_cust_number").attr("data-validate", "{'custom-required':true, 'validate-number':true}");
	 $('#cu_discount_f_cust_surname').val( "" );
	 $('#cu_discount_f_cust_firstname').val( "" );
	 $('#cu_discount_f_cust_dob').val( "" );
	}   
	else
	{
	  $("input:checkbox[name='custom_attributes[discount_f]']").prop('checked', false);
	  $("input:text[name='custom_attributes[discount_f]']").keyup();
	  $('input:radio[name=cu_discount_f_bill_in_name]').filter('[value=1]').prop('checked', true);
	  $("#div_discount_cust_number").addClass("disable_div");	
	  $("#div_discount_bill_in_name").addClass("disable_div");
	  $("#div_discount_cust_details").removeClass("disable_div");	
	  $("#div_discount_cust_details").addClass("disable_div");	
	  $('#cu_discount_f_cust_number').removeAttr( "data-validate" );
	  $('#cu_discount_f_cust_number').val( "" );
	  $("#cu_discount_f_cust_number" ).removeClass( "mage-error" );
	  $("#cu_discount_f_cust_number-error" ).hide();
	  $('#cu_ex_invoice_cust_dob').val( "" );
	  $("#cu_ex_invoice_cust_dob" ).removeClass( "mage-error" );
	  $("#cu_ex_invoice_cust_dob-error" ).hide();
	  $('#cu_discount_f_cust_firstname').val( "" );
	  $("#cu_discount_f_cust_firstname" ).removeClass( "mage-error" );
	  $("#cu_discount_f_cust_firstname-error" ).hide();
	  $('#cu_discount_f_cust_surname').val( "" );
	  $("#cu_discount_f_cust_surname" ).removeClass( "mage-error" );
	  $("#cu_discount_f_cust_surname-error" ).hide();

	 }  
});
$("#cu_discount_f_cust_number").on('change', function (){
	$("input:text[name='custom_attributes[discount_f_cust_number]']").val($("#cu_discount_f_cust_number option:selected").val());
	$("input:text[name='custom_attributes[discount_f_cust_number]']").keyup();
});
$("input:radio[name='cu_discount_f_bill_in_name']").on('change', function (){
	if (this.value == '1')
	{
	   $("input:checkbox[name='custom_attributes[discount_f_bill_in_name]']").prop('checked', true);
	   $("#div_discount_cust_details").addClass("disable_div");
	   $('#cu_discount_f_cust_surname').removeAttr( "data-validate" );
	   $('#cu_discount_f_cust_firstname').removeAttr( "data-validate" );
	   $('#cu_ex_invoice_cust_dob').removeAttr( "data-validate" );
	   $('#cu_ex_invoice_cust_dob').val( "" );
	   $('#cu_discount_f_cust_firstname').val( "" );
	   $('#cu_discount_f_cust_surname').val( "" );
	  $("#cu_discount_f_cust_surname" ).removeClass( "mage-error" );
	  $("#cu_discount_f_cust_surname-error" ).hide();
	  $("#cu_discount_f_cust_firstname" ).removeClass( "mage-error" );
	  $("#cu_discount_f_cust_firstname-error" ).hide();
	  $("#cu_ex_invoice_cust_dob" ).removeClass( "mage-error" );
	  $("#cu_ex_invoice_cust_dob-error" ).hide();
	}
	else
	{
	  $('#cu_discount_f_cust_surname').removeAttr( "data-validate" );
     $("#cu_discount_f_cust_surname").attr("data-validate", "{'custom-required':true, 'validate-alpha':true}");
	 $('#cu_discount_f_cust_firstname').removeAttr( "data-validate" );
     $("#cu_discount_f_cust_firstname").attr("data-validate", "{'custom-required':true, 'validate-alpha':true}");
	 $('#cu_ex_invoice_cust_dob').removeAttr( "data-validate" );
     $("#cu_ex_invoice_cust_dob").attr("data-validate", "{'custom-required':true}"); 
     $("#div_discount_cust_details").removeClass("disable_div");	
	   $("input:checkbox[name='custom_attributes[discount_f_bill_in_name]']").prop('checked', false);
	}   
	$("input:text[name='custom_attributes[discount_f_bill_in_name]']").keyup();
});


$("#cu_discount_f_cust_surname").on('change', function (){
	$("input:text[name='custom_attributes[discount_f_cust_surname]']").val($("#cu_discount_f_cust_surname").val());
	$("input:text[name='custom_attributes[discount_f_cust_surname]']").keyup();
});

$("#cu_discount_f_cust_firstname").on('change', function (){
	$("input:text[name='custom_attributes[discount_f_cust_firstname]']").val($("#cu_discount_f_cust_firstname").val());
	$("input:text[name='custom_attributes[discount_f_cust_firstname]']").keyup();
});

$("#cu_discount_f_cust_dob").on('change', function (){
	$("input:text[name='custom_attributes[discount_f_cust_dob]']").val($("#cu_discount_f_cust_dob").val());
	$("input:text[name='custom_attributes[discount_f_cust_dob]']").keyup();
});
$("input:checkbox[name='i_ind_copm']").on('change', function (){
	if (this.checked)
	{
		
	 $("input:checkbox[name='custom_attributes[i_ind_copm]']").prop('checked', true);
	 $('#tx_profile_dropdown').removeAttr( "data-validate" );
     $("#tx_profile_dropdown").attr("data-validate", "{'custom-required':true}"); 
	}
	else
	{
		$('#company_name').removeClass( "mage-error");
		$('#company_name-error').hide();
		$('#vat_number').removeClass( "mage-error");
		$('#vat_number-error').hide();
	   $("#tx_profile_dropdown" ).removeClass( "mage-error" );
	   $("#tx_profile_dropdown-error" ).hide();
	   $("input:checkbox[name='custom_attributes[i_ind_copm]']").prop('checked', false);
	}
	$("input:text[name='custom_attributes[i_ind_copm]']").keyup();
});
$("#tx_profile_dropdown").on('change', function (){
	$("input:text[name='custom_attributes[tx_drop_down]']").val($("#tx_profile_dropdown option:selected").text());
	$("input:text[name='custom_attributes[tx_drop_down]']").keyup();
});
$("#legal_status").on('change', function (){
	$("input:text[name='custom_attributes[legal_status]']").val($("#legal_status option:selected").val());
	$("input:text[name='custom_attributes[legal_status]']").keyup();
});
$("input:text[name='company_name']").on('change', function (){
   $("input:text[name='custom_attributes[company_name]']").val($("input:text[name='company_name']").val());
   $("input:text[name='custom_attributes[company_name]']").keyup();
});
$("input:text[name='vat_number']").on('change', function (){
   $("input:text[name='custom_attributes[vat_number]']").val($("input:text[name='vat_number']").val());
   $("input:text[name='custom_attributes[vat_number]']").keyup();
});
$("input:radio[name='gender']").on('change', function (){
   $("input:text[name='prefix']").val(this.value);
   $("input:text[name='prefix']").keyup();
});
$("input:radio[name='registered']").on('change', function (){
	if (this.value == '1')
	{
	   $("input:checkbox[name='custom_attributes[registered]']").prop('checked', true);
	    $('#passport_div').addClass('disable_div');
		$('#error_div').addClass('disable_div');
	   $('#residence_div').removeClass('disable_div');
	   $('#passport_number').removeClass('mage-error');
	   $('#passport_number-error').hide();
	   $('.bundle-hide').removeClass('disable_div');
	}
	else if(this.value == '0') {
	    $("#residence_number").val('');
	    $("#residence_number").removeClass("mage-error");
		$('#id_number-error').hide();
		var bundle_error = $("#bundle-error").val();
		if(bundle_error == 1) {
			$('#residence_div').addClass('disable_div');
			$('#passport_div').addClass('disable_div');
			$('#passport_number').addClass('disable_div');
			$('#error_div').removeClass('disable_div');
			$('.bundle-hide').addClass('disable_div');
		} else {
			$('#passport_div').removeClass('disable_div');
			$('#passport_number').removeClass('disable_div');
			$('#error_div').addClass('disable_div');
			$('#id_number').addClass('disable_div');
			$('#residence_div').addClass('disable_div');
			$('.bundle-hide').removeClass('disable_div');
			$("input:checkbox[name='custom_attributes[registered]']").prop('checked', false);
			$("input:text[name='custom_attributes[registered]']").keyup();
		}
	}
	else
	{
		$('#passport_div').removeClass('disable_div');
		$('#passport_number').removeClass('disable_div');
		$('#id_number').addClass('disable_div');
		$('#error_div').addClass('disable_div');
		$('#residence_div').addClass('disable_div');
		$('.bundle-hide').removeClass('disable_div');
		$("input:checkbox[name='custom_attributes[registered]']").prop('checked', false);
		$("input:text[name='custom_attributes[registered]']").keyup();
	 }
});
$("input[name='residence_number']").on('change', function (){
   $("input[name='custom_attributes[residence_number]']").val($("input[name='residence_number']").val());
   $("input[name='custom_attributes[residence_number]']").keyup();
});
$("input:text[name='passport_number']").on('change', function (){
   $("input:text[name='custom_attributes[passport_number]']").val($("input:text[name='passport_number']").val());
   $("input:text[name='custom_attributes[passport_number]']").keyup();
});
$("input[name='id_number']").on('change', function (){
   $("input:text[name='custom_attributes[passport_number]']").val($("input[name='id_number']").val());
   $("input:text[name='custom_attributes[passport_number]']").keyup();
});

$("input:text[name='s_name']").on('change', function (){
    if($('input[name=c_delivery_address]:radio:checked').val() == 1){
        $("#billing-new-address-form input:text[name='lastname']").val($("input:text[name='s_name']").val());   
        $("#billing-new-address-form input[name='lastname']").keyup();
    }else{
        $("#shipping-new-address-form input:text[name='lastname']").val($("input:text[name='s_name']").val());   
         $("#shipping-new-address-form input[name='lastname']").keyup();
    }
   
});

$("input:text[name='s_firstname']").on('change', function (){
   if($('input[name=c_delivery_address]:radio:checked').val() == 1){
        $("#billing-new-address-form input:text[name='firstname']").val($("input:text[name='s_firstname']").val());   
        $("#billing-new-address-form input[name='firstname']").keyup();
    }else{
        $("#shipping-new-address-form input:text[name='firstname']").val($("input:text[name='s_firstname']").val());   
         $("#shipping-new-address-form input[name='firstname']").keyup();
    }
   
});
	  
$("input[name='s_postcode_city']").on('change', function (){
	var postalCity = $("input[name='s_postcode_city']").val();
       if($('input[name=c_delivery_address]:radio:checked').val() == 1){
            $("#billing-new-address-form input:text[name='postcode']").val(postalCity);
	$("#billing-new-address-form input[name='postcode']").keyup();
        }else{
            $("#shipping-new-address-form input:text[name='postcode']").val(postalCity);
	$("#shipping-new-address-form input[name='postcode']").keyup();
        }
	
});
$("input[name='s_city']").on('change', function (){
     if($('input[name=c_delivery_address]:radio:checked').val() == 1){
          $("#billing-new-address-form input:text[name='city']").val($("input:text[name='s_city']").val());
		$("#billing-new-address-form input:text[name='region']").val($("input:text[name='s_city']").val());
	$("input[name='city']").keyup();
	$("input[name='region']").keyup();
      }else{
          $("#shipping-new-address-form input:text[name='city']").val($("input:text[name='s_city']").val());
		$("#shipping-new-address-form input:text[name='region']").val($("input:text[name='s_city']").val());
	$("input[name='city']").keyup();
	$("input[name='region']").keyup();
      }
		
});
$("input:text[name='s_box']").on('change', function (){
   if($('input[name=c_delivery_address]:radio:checked').val() == 1){
          $("#billing-new-address-form input:text[name='street[1]']").val($("input:text[name='s_box']").val());   
   $("#billing-new-address-form input[name='street[1]']").keyup();
     }else{
          $("#shipping-new-address-form input:text[name='street[1]']").val($("input:text[name='s_box']").val());   
   $("#shipping-new-address-form input[name='street[1]']").keyup();
     }
  
});
$("input:text[name='s_street']").on('change', function (){
     if($('input[name=c_delivery_address]:radio:checked').val() == 1){
          $("#billing-new-address-form input:text[name='street[0]']").val($("input:text[name='s_street']").val());
   $("#billing-new-address-form input[name='street[0]']").keyup();
     }else{
          $("#shipping-new-address-form input:text[name='street[0]']").val($("input:text[name='s_street']").val());
   $("#shipping-new-address-form input[name='street[0]']").keyup();
     }
    
  
});
$("input:text[name='s_number']").on('change', function (){
    if($('input[name=c_delivery_address]:radio:checked').val() == 1){
      $("#billing-new-address-form input:text[name='custom_attributes[street_number]']").val($("input:text[name='s_number']").val());
   $("#billing-new-address-form input:text[name='custom_attributes[street_number]']").keyup();  
    }else{
        $("#shipping-new-address-form input:text[name='custom_attributes[street_number]']").val($("input:text[name='s_number']").val());
   $("#shipping-new-address-form input:text[name='custom_attributes[street_number]']").keyup();
    }
   
});
$("input[name='cust_telephone']").on('change', function (){
   $("input:text[name='telephone']").val($("input[name='cust_telephone']").val());
   $("input[name='telephone']").keyup();
});
$("input:text[name='cust_birthplace']").on('change', function (){
   $("input:text[name='custom_attributes[birth_place]']").val($("input:text[name='cust_birthplace']").val());
   $("input:text[name='custom_attributes[birth_place]']").keyup();
});
$("input[name='b_postcode_city']").on('change', function (){
	var postalCity = $("input[name='b_postcode_city']").val();
	$("input:text[name='postcode']").val(postalCity);
	$("input[name='postcode']").keyup();
});
$("input[name='b_city']").on('change', function (){
		$("input:text[name='city']").val($("input:text[name='b_city']").val());
		$("input:text[name='region']").val($("input:text[name='b_city']").val());
	$("input[name='city']").keyup();
	$("input[name='region']").keyup();
});
$("input:text[name='b_box']").on('change', function (){
   $("input:text[name='street[1]']").val($("input:text[name='b_box']").val());   
   $("input[name='street[1]']").keyup();
});
$("input:text[name='b_street']").on('change', function (){
   $("input:text[name='street[0]']").val($("input:text[name='b_street']").val());
   $("input[name='street[0]']").keyup();
});
$("input:text[name='b_number']").on('change', function (){
   $("input:text[name='custom_attributes[street_number]']").val($("input:text[name='b_number']").val());
   $("input:text[name='custom_attributes[street_number]']").keyup();
});
$(document).on("change", "input:text[name='client_code']", function (e) {
   $("input:text[name='custom_attributes[client_code]']").val($("input:text[name='client_code']").val());
   $("input:text[name='custom_attributes[client_code]']").keyup();
});
$(document).on("change", "input:text[name='client_name']", function (e) {
   $("input:text[name='custom_attributes[client_name]']").val($("input:text[name='client_name']").val());
    $("input:text[name='custom_attributes[client_name]']").keyup();
});
$(document).on("change", "input[name='offer_subscription']", function (e) {
	if (this.checked)            
	   $("input:checkbox[name='custom_attributes[newsletter_subscription]']").val(1);
	else
	   $("input:checkbox[name='custom_attributes[newsletter_subscription]']").val(0);   
	$("input:text[name='custom_attributes[newsletter_subscription]']").keyup();
});
$("input[name='c_dob']").on('change', function (){
	var c_dob_val = $(this).val();
	var c_dob_s    = c_dob_val.split('/');
	var day     = c_dob_s[0];
	var month   =  c_dob_s[1];
	var year    =  c_dob_s[2];
	var f_cdob  =   month+"/"+day+"/"+year;
	$("input:text[name='custom_attributes[dob]']").val(f_cdob);
	$("input:text[name='custom_attributes[dob]']").change();
});

$(document).on("click", "input:radio[id='bank_transfer_type']", function (e) {
  $("#domiciliation_textbox_content").show();
  $(".domiciliation-message").show();
});

$(document).on("click", "input:radio[id='edit-nationality-virement']", function (e) {
  $("#domiciliation_textbox_content").hide();
  $(".domiciliation-message").hide();
  if ($(this).val() == "Virement") {
        $("#first-accountnumber").removeClass('mage-error');
		$("#first-accountnumber-error").hide();
		$("#first-accountnumber-error-data").hide();
        $("#first-accountnumber").val('BE');
		var session_url = $("#session_url").val();
		$.ajax({
			url: session_url,
			data: {transfertype: 'Virement'},
			type: 'POST',
			dataType: 'json',  
			showLoader: true,					
			context: $('#step2')
		});	
  }
});

$(document).on("change", "input:radio[name='transfer_type']", function (e) {
	   $("input:text[name='custom_attributes[transfer_type]']").val(this.value);
	   $("input:text[name='custom_attributes[transfer_type]']").keyup();
});
var $teneuroboxes = $('input[type=radio].is_teneuro:checked');
$teneuroboxes.each(function(){
	var itemId = $(this).attr('data-itemid');
	$("#iew_telephone_"+itemId).keypress(function() {
		if (this.value.length > 10) {
			return false;
		}
	});
});
$("#cust_telephone").keypress(function() {
	/*if (this.value.length > 10) {
		return false;
	}*/
});

$(document).on("click", "#place_order_btn", function (e) {
	 var payment_id = $('.payment-method-title input.radio:checked').val();
	 $("#error-message-payment-bottom").hide();
	 $('.step-mage-error').hide(); 
	 if(payment_id!="undefined" || payment_id!="")
	 {
	  $(".payment-method._active .payment-method-content .action.primary.checkout").click();
	  if (typeof(payment_id) == "undefined") {
		$("html, body").animate({ scrollTop: 0 }, 600);
			$('.step-mage-error').show(); 
		$("#error-message-payment-bottom").show();
	  }
	 }
	 else{
	 alert("Please select any one of the payment method");
	 }
});
$(document).on("click", ".ogone_payment", function (e) {
	$("#error-message-payment-bottom").hide();
});

function date_validation(value)
{
    var c_dob_v    = value;
	var c_dob_s    = c_dob_v.split('/');
	var day     = c_dob_s[0];
	var month  =  c_dob_s[1];
	var year   =  c_dob_s[2];
	var age    =  18;
 
  var mydate = new Date();
	mydate.setFullYear(year, month-1, day);
 
	var currdate = new Date();
	currdate.setFullYear(currdate.getFullYear() - age);
 
	return (currdate - mydate < 0 ? false : true);
}

	/////////////////////Ajax Screen to Load Step3/////////////////////////////////////////////////////////////
	$(document).on("click", '#step2', function (e) {
	$("html, body").animate({ scrollTop: 0 }, 600);
	
	if (!$("input:text[name='firstname']").val() && $("input:text[name='first_name']").val()) {
		$("input:text[name='firstname']").val($("input:text[name='first_name']").val());
		$("input[name='firstname']").keyup();
	}
	
	if (!$("input:text[name='lastname']").val() && $("input:text[name='last_name']").val()) {
		$("input:text[name='lastname']").val($("input:text[name='last_name']").val());
		$("input[name='lastname']").keyup();
	}
	
	if (!$("input:text[name='telephone']").val() && $("input:text[name='cust_telephone']").val()) {
		$("input:text[name='telephone']").val($("input[name='cust_telephone']").val());
		$("input[name='telephone']").keyup();
	}
	
	if (!$("input:text[name='postcode']").val() && $("input[name='b_postcode_city']").val()) {
		var postalCity = $("input[name='b_postcode_city']").val();
		$("input:text[name='postcode']").val(postalCity);
		$("input[name='postcode']").keyup();
	}
	
	if (!$("#customer-email").val() && $("input[name='email']").val()) {
	   $("#customer-email").val($("input[name='email']").val());
	   $("#customer-email").change();
	}
	
	if (!$("input:text[name='city']").val() && $("input:text[name='b_city']").val()) {
		$("input:text[name='city']").val($("input:text[name='b_city']").val());
		$("input:text[name='region']").val($("input:text[name='b_city']").val());
		$("input[name='city']").keyup();
		$("input[name='region']").keyup();
	}
	if (!$("input:text[name='street[0]']").val() && $("input:text[name='b_street']").val()) {
	     $("input:text[name='street[0]']").val($("input:text[name='b_street']").val());
		$("input[name='street[0]']").keyup();
		 $("input:text[name='street[1]']").val($("input:text[name='b_box']").val());   
		$("input[name='street[1]']").keyup();
	}
	
	if (!$("input:text[name='custom_attributes[street_number]']").val() && $("input:text[name='b_number']").val()) {
		$("input:text[name='custom_attributes[street_number]']").val($("input:text[name='b_number']").val());
		$("input:text[name='custom_attributes[street_number]']").keyup();
	}
	
	if (!$("input:text[name='custom_attributes[client_code]']").val() && $("input:text[name='client_code']").val()) {
		$("input:text[name='custom_attributes[client_code]']").val($("input:text[name='client_code']").val());
		$("input:text[name='custom_attributes[client_code]']").keyup();
	}
	
	if (!$("input:text[name='custom_attributes[client_name]']").val() && $("input:text[name='client_name']").val()) {
		$("input:text[name='custom_attributes[client_name]']").val($("input:text[name='client_name']").val());
		$("input:text[name='custom_attributes[client_name]']").keyup();
	}
	
	$("input:text[name='custom_attributes[dob]']").change();
	//$("#s_method_freeshipping_freeshipping").trigger('click');
    var val_c_delivery_address = $("input[name='c_delivery_address']:checked").val();
	var val_last_name       = 	$('#last_name').val();
	var val_b_postcode_city = 	$('#b_postcode_city').val();
	var val_b_street        = 	$('#b_street').val();
	var val_b_number        = 	$('#b_number').val();
	var val_b_box           = 	$('#b_box').val();
	var id_number         = true; 
	if(val_c_delivery_address == 1)
	{
	    $('#s_name').val(val_last_name);
		$('#s_firstname').val($('#first_name').val());    
		$('#s_postcode_city').val(val_b_postcode_city);
		$('#s_street').val(val_b_street);
		$('#s_number').val(val_b_number);
		$('#s_box').val(val_b_box);
		$('#s_edit_city').val($("input:text[name='b_city']").val());
		$("#s_attention_n").val('');
		$('#isdeliveryMethod').val("0");
		$("input:text[name='custom_attributes[bpost_postal_location]']").val('');
		$("input:text[name='custom_attributes[bpost_method]']").val('');
		$("#s_method_freeshipping_freeshipping").trigger('click');
	} else if(val_c_delivery_address == 3) {
		$("#s_method_bpost_bpost").trigger('click');
		var customerPostalLocation = $('#customerPostalLocation').val();		
		var customerFirstName = $('#customerFirstName').val();
		var customerLastName = $('#customerLastName').val();
		var customerStreet = $('#customerStreet').val();
		var customerStreetNumber = $('#customerStreetNumber').val();
		var customerPostalCode = $('#customerPostalCode').val();
		var customerCity = $('#customerCity').val();
		var deliveryMethod = $('#deliveryMethod').val();
		$('#isdeliveryMethod').val("1");
		$('#s_number').removeAttr( "data-validate" );
		$('#s_firstname').val(customerFirstName);
		$('#s_name').val(customerLastName);
		$('#s_postcode_city').val(customerPostalCode);
		$('#s_street').val(customerStreet);
        $('#s_edit_city').val(customerCity);
		$('#s_number').val(customerStreetNumber);
		$('#s_box').val('');
		$("#s_attention_n").val('');
		$("input:text[name='custom_attributes[bpost_postal_location]']").val(customerPostalLocation);
		$("input:text[name='street[0]']").val(customerStreet+" "+customerStreetNumber);   
		$("input:text[name='street[1]']").val('');   
		$("input[name='postcode']").val(customerPostalCode);
		$("input:text[name='region']").val(customerCity);
		$("input:text[name='city']").val(customerCity);
		$("input:text[name='firstname']").val($("input:text[name='first_name']").val());
		$("input:text[name='lastname']").val($("input:text[name='last_name']").val());
		$("input:text[name='custom_attributes[bpost_method]']").val(deliveryMethod);
	} else if(val_c_delivery_address == 2) {
		$("#s_method_freeshipping_freeshipping").trigger('click');
		$("input:text[name='custom_attributes[bpost_postal_location]']").val("");
		$("input:text[name='custom_attributes[bpost_method]']").val('');
		$('#isdeliveryMethod').val("0");		
		$("input:text[name='firstname']").val($("input:text[name='first_name']").val());
		$("input:text[name='lastname']").val($("input:text[name='last_name']").val());
		$("input:text[name='city']").val($("input:text[name='s_city']").val());
		$("input:text[name='region']").val($("input:text[name='s_city']").val());
		$("input[name='postcode']").val($("input:text[name='s_postcode_city']").val());	
	}
	$("input:text[name='firstname']").keyup();
	$("input:text[name='lastname']").keyup();
	$("input:text[name='street[0]']").keyup();  
	$("input:text[name='street[1]']").keyup();
	$("input[name='postcode']").keyup();
	$("input:text[name='region']").keyup();
	$("input:text[name='city']").keyup();
	$("input:text[name='prefix']").val($("input:radio[name='gender']").val());
	$("input:text[name='prefix']").keyup();
	$("input:text[name='custom_attributes[bpost_method]']").keyup();
	$("input:text[name='custom_attributes[bpost_postal_location]']").keyup();
    var nationalityCheck = $("input[name='nationality']:checked").val();
    var nationalityCheckreg = $("input[name='registered']:checked").val();
	var tx_profile_dropdown = $("#tx_profile_dropdown").val();
	var virtualproductonepage = $("#virtualproductonepage").val();
	var prepaid_check           = $("#prepaid_check").val();
	var number_screen           = $("#number_screen").val();
	$(checkout_form).validation();
	var first_name              = $.validator.validateElement($("#first_name"));	
	var last_name               = $.validator.validateElement($("#last_name"));	
	var email                   = $.validator.validateElement($("#email"));	
	var b_postcode_city         = $.validator.validateElement($("#b_postcode_city"));	
	var b_street                = $.validator.validateElement($("#b_street"));
	var b_city                  = $.validator.validateElement($("#b_city"));
	var b_number                = $.validator.validateElement($("#b_number"));	
	var val_ex_invoice                 = $("input[name='ex_invoice']:checked").val();
	var val_cu_ex_invoice_bill_in_name = $("input[name='cu_ex_invoice_bill_in_name']:checked").val();
	var val_discount_f                 = $("input[name='discount_f']:checked").val();
	var val_cu_discount_f_bill_in_name = $("input[name='cu_discount_f_bill_in_name']:checked").val();
	var c_date_v = $("#c_dob").val();
	$("input[name='transfer_type'][value='Virement']").prop('checked', 'checked');
	if(val_ex_invoice == 'yes')
	{
	   var cu_ex_invoice_cust_number      = $.validator.validateElement($("#cu_ex_invoice_cust_number"));	
	   if(val_cu_ex_invoice_bill_in_name == '1')
	   {
		var cu_ex_invoice_cust_surname     = true;	
  	    var cu_ex_invoice_cust_firstname   = true;		
	    var cu_ex_invoice_cust_dob         = true;	
	   }
	   else
	   {
	   var cu_ex_invoice_cust_surname     = $.validator.validateElement($("#cu_ex_invoice_cust_surname"));	
	   var cu_ex_invoice_cust_firstname   = $.validator.validateElement($("#cu_ex_invoice_cust_firstname"));	
	   var cu_ex_invoice_cust_dob         = $.validator.validateElement($("#cu_ex_invoice_cust_dob"));	
	   }
	}
	 else
	 {
	var cu_ex_invoice_cust_surname     = true;	
	var cu_ex_invoice_cust_firstname   = true;		
	var cu_ex_invoice_cust_dob         = true;		
	var cu_ex_invoice_cust_number      = true;	
	}
	if(val_discount_f == 'yes')
	{
	   var cu_discount_f_cust_number      = $.validator.validateElement($("#cu_discount_f_cust_number"));	
	   if(val_cu_discount_f_bill_in_name == '1')
	   {
		var cu_discount_f_cust_surname     = true;	
  	    var cu_discount_f_cust_firstname   = true;		
	    var cu_discount_f_cust_dob         = true;	
	   }
	   else
	   {
	   var cu_discount_f_cust_surname     = $.validator.validateElement($("#cu_discount_f_cust_surname"));	
	   var cu_discount_f_cust_firstname   = $.validator.validateElement($("#cu_discount_f_cust_firstname"));	
	   var cu_discount_f_cust_dob         = $.validator.validateElement($("#cu_discount_f_cust_dob"));	
	   }
	}
	 else
	 {
	var cu_discount_f_cust_surname     = true;	
	var cu_discount_f_cust_firstname   = true;		
	var cu_discount_f_cust_dob         = true;		
	var cu_discount_f_cust_number      = true;	
	}

	if(virtualproductonepage == 1 || prepaid_check == 1)
	{
	var s_name                  = true;	
	var s_postcode_city         = true;	
	var s_street                = true;	
	var s_city                  = true;	
	var s_number                = true;	
	var s_street_validation     =  0;
	var s_zipcode_validation    =  0;
	}
	else
	{
	if (val_c_delivery_address == 2) {
		var s_name                  = $.validator.validateElement($("#s_name"));	
		var s_postcode_city         = $.validator.validateElement($("#s_postcode_city"));	
		var s_street                = $.validator.validateElement($("#s_street"));	
		var s_number                = $.validator.validateElement($("#s_number"));
		var s_city                   = $.validator.validateElement($("#s_edit_city"));
		var s_street_validation    = $("#s_street_validation").val();
		var s_zipcode_validation    = $("#s_zipcode_validation").val();	
		
	 } else {
		var s_name                  = true;	
		var s_postcode_city         = true;	
		var s_street                = true;	
		var s_city                  = true;	
		var s_number                = true;	
		var s_street_validation     =  0;
		var s_zipcode_validation    =  0;
	 }
	}

	
	var b_zipcode_validation    = $("#b_zipcode_validation").val();
	var b_street_validation     = $("#b_street_validation").val();
if(number_screen == 1)	
{
    
    if(nationalityCheck == "belgian")
	  {
	  var id_number         = $.validator.validateElement($("#id_number"));
	  var passport_number = true;	
	  var residence_number = true;
      }     
	else
	  {
	   if(nationalityCheckreg == 1)
	   { 
	   var id_number         = true; 
       var passport_number = true;	   
       var residence_number = $.validator.validateElement($("#residence_number")); 
	   }
	   else
	   {
	    var residence_number = true;
		var id_number         = true; 
	    var passport_number         = $.validator.validateElement($("#passport_number"));
	   }
	  }
 
 }
else
{
  var residence_number = true;
  var passport_number = true;
} 
if(number_screen == "" && prepaid_check == 0 )	
{
 	var c_dob                = true;	

 }
else
{   

     if(number_screen == "" && prepaid_check==1)
     {
	  var c_dob                = $.validator.validateElement($("#c_dob"));
	 }
	 else if(number_screen == 1)
	 {
	   var c_dob                = $.validator.validateElement($("#c_dob"));
      }
	  else
	  {
	   var c_dob                = $.validator.validateElement($("#c_dob"));
	  }

 

} 

	 if(prepaid_check == 1)
	 {
	 var cust_birthplace = $.validator.validateElement($("#cust_birthplace")); 
	 var cust_telephone = true; 
	 $("input:text[name='telephone']").val('9999999999');
     $("input[name='telephone']").keyup();
	 }
	 else
	 {
	 var cust_birthplace = true; 
	 var cust_telephone = $.validator.validateElement($("#cust_telephone")); 
	 }
	 var vat_number_chk = $("#vat_number").val();
	if($("#i_ind_copm").is(':checked'))
	{
	var tx_profile_dropdown_val = $.validator.validateElement($("#tx_profile_dropdown")); 
	     if(tx_profile_dropdown == "")
	   {
    
             var legal_status_val = true; 
		     var vat_number = true;
			  var company_name = true;
 
	   }
	   		else if(tx_profile_dropdown == 1 || tx_profile_dropdown == 4)
	   {
            var legal_status_val = true; 
		     var vat_number = $.validator.validateElement($("#vat_number"));
			  var company_name = true;

	   }
	    	else if(tx_profile_dropdown == 2)
	   {
	         var legal_status_val = true; 
		     var vat_number = true;
			  var company_name = true;

	   }
	      	else if(tx_profile_dropdown == 3)
	   {
	  
		  	 var legal_status_val = $.validator.validateElement($("#legal_status")); 
		     var vat_number = $.validator.validateElement($("#vat_number"));
			  var company_name = $.validator.validateElement($("#company_name"));

	   }
	   else
	   {
	          var legal_status_val = true; 
		      var vat_number = true;
			  var company_name = true;
	   }
	   if ($('#vat_number_validation_error').html()) {
			var vat_number = false;
	   }

	}
	else
	{
    if(tx_profile_dropdown == 3) {
		  	 var legal_status_val = $.validator.validateElement($("#legal_status")); 
		     var vat_number = $.validator.validateElement($("#vat_number"));
			 var company_name = $.validator.validateElement($("#company_name"));

	   }
	var tx_profile_dropdown_val = true; 
	var legal_status_val = true; 
	var vat_number = true;
	var company_name = true;
	}
	/** IEW Validation **/
	var iew_validation_status = true;
	var $teneuroboxes = $('input[type=radio].is_teneuro:checked');
	$teneuroboxes.each(function(){
		if($(this).attr('value') == 'yes')
		{
		var itemId = $(this).attr('data-itemid');
		var iew_phone = $.validator.validateElement($("#iew_telephone_"+itemId));
		if(!iew_phone) {
			iew_validation_status = false;
		}
		}
	});
	var $contractboxes = $('input[type=radio].iew_contract:checked');
	$contractboxes.each(function(){
		if($(this).attr('value') == 0)
		{
			var itemId = $(this).attr('data-itemid');
			if($('#is_teneuro_'+itemId).is(":checked"))
			{
				var iew_fname = $.validator.validateElement($("#iew_first_name_"+itemId));
				var iew_lname = $.validator.validateElement($("#iew_last_name_"+itemId));
				var iew_dob = $.validator.validateElement($("#iew_dob_"+itemId));
				if(!iew_fname || !iew_lname || !iew_dob)
				{
					iew_validation_status = false;					
				}
			}
		}		
	});
	
		  	if($("#terms_condition").is(':checked'))
	            {
					$("#terms_condition-error").hide();
					$("#terms_condition").removeClass('mage-error');
					var vald_terms_condition  = true;
			    }
			else
			    {
					$("#terms_condition-error").show();
					$("#terms_condition-error").empty();
					$("#terms_condition-error").append($.mage.__('To finalize your order, please read and accept the general terms and conditions of Orange Belgium.'));
					$("#terms_condition").addClass('mage-error');
					var vald_terms_condition  = false;
			    }
	 /* Validator Starts*/
		/**** Billing Address Validation on proceedding to next step*/	
		$("input:text[id='b_postcode_city']").keyup();
		if($("#b_zipcode_validation").val() == 1) {
			$("#b_zip_validation").text($.mage.__("Zipcode doesn't exist"));
		}
		else{
			$("#b_zip_validation").text("");
		}
		
		if($("#b_zipcode_city_validation").val() == 1) {
			$("#b_city_validation").text($.mage.__("City does not exist"));
		}
		else {
			$("#b_city_validation").text("");
		}
		if($("#b_street_validation").val() == 1) {
			$("#street_validation").text($.mage.__("Street doesn't exist"));
		}
		else {
			$("#street_validation").text("");
		}
		/**** Shipping Address Validation on proceedding to next step*/		
		if(val_c_delivery_address == 2) {
			$("input:text[id='s_postcode_city']").keyup();
			if($("#s_zipcode_validation").val() == 1) {
				$("#s_zip_validation").text($.mage.__("Zipcode doesn't exist"));				
			}
			else{
				$("#s_zip_validation").text("");				
			}
			
			if($("#s_zipcode_city_validation").val() == 1) {
				$("#s_city_validation").text($.mage.__("City does not exist"));				
			}
			else {
				$("#s_city_validation").text("");				
			}
			if($("#s_street_validation").val() == 1) {
				$("#street_validation_s").text($.mage.__("Street doesn't exist"));				
			}
			else {
				$("#street_validation_s").text("");				
			}
		}
		/******/
		var bpost_selected  = true;
		if(val_c_delivery_address == 3) {
			var isBpost = $("#customerPostalLocation").val();
			if(isBpost != "") {
				$("#c_delivery_address_bpost-error").hide();
				var bpost_selected  = true;
			} else {
				$("#c_delivery_address_bpost-error").show();
				var bpost_selected  = false;
			}
		}
	   var b_city_validation = $("#b_city_validation").text();
	   var b_zip_validation = $("#b_zip_validation").text();
	   var street_validation = $("#street_validation").text();

		if( b_city_validation.trim() != "" || b_zip_validation.trim() != "" || street_validation.trim() != "" || vald_terms_condition == false  || b_zipcode_validation == 1 || s_zipcode_validation == 1 || b_street_validation == 1 || s_street_validation == 1 || id_number == false || cust_telephone == false|| cust_birthplace == false || cu_discount_f_cust_number == false || cu_discount_f_cust_surname == false || cu_discount_f_cust_firstname == false || cu_discount_f_cust_dob == false || cu_ex_invoice_cust_firstname == false || cu_ex_invoice_cust_dob == false ||cu_ex_invoice_cust_surname == false || cu_ex_invoice_cust_number == false || legal_status_val == false || tx_profile_dropdown_val == false ||company_name == false || vat_number == false || passport_number == false || first_name == false || last_name == false || email == false || c_dob == false || b_street == false || b_number == false || b_city == false || s_name == false || s_postcode_city == false || s_street == false || s_city == false  || s_number == false || iew_validation_status ==false || bpost_selected ==false)
		{
			$("html, body").animate({ scrollTop: 0 }, 600);
			$('.step-mage-error').show();
			return false;
		}
		else 
		{
			$('#chk_step_stat').val('final');
			$('.step-mage-error').hide();
		}
		
	           var fName = $("#first_name").val(); 
                var lName = $("#last_name").val();                              
                var cdob = $("#c_dob").val();     
                var scoringTypeCheck = $("#number_screen").val();
                if(scoringTypeCheck == '1'){
                   var  scoringType = "Subsidized";          
                } else {
                    scoringType = "Classic";
                }
                var hsCount = $("#scoring_handset_count").val();
                var misidn = $("#design_te_existing_number").val();                 
                var idCardNumber = $("#id_number").val();
				var idType = 'eID'; 
				var nationality = $("input[name='nationality']:checked" ).val();
				if (nationality == "other") {
				    var registeredbelgium = $("input[name='registered']:checked" ).val();
					if (registeredbelgium == "1") {
						var idType = 'eID foreign';
						var idCardNumber = $("#residence_number").val();
					} else {
						var idType = 'ID foreign';
						var idCardNumber = $("#passport_number").val();
					}
				}
                var email = $("#email").val();
                var streetname = $("#b_street").val();
                var streeetnumber = $("#b_number").val();
                var zip = $("#b_postcode_city").val();
                var city = $("#b_city").val();
                var nbrsimcard = '1';
                var scoringResponse = "";  
                var postpaid =$("#design_sim_number").val(); 
                var refusal_page =$("#scoring_redirect").val();	
                var scoring_url =$("#scoring_url").val();					
                if (idCardNumber.length !== 0  && fName.length !==0 && lName.length!==0 && cdob.length!==0 && scoringTypeCheck.length!==0) 
				{    
                  //ajax calls to scoring controller then to scoring server                  
           
                    $.ajax({
                        url: scoring_url,
                        type: "POST",
						showLoader: true,
                        data: "id_card_numer=" + idCardNumber + "&fname=" + fName + "&lname=" + lName + "&dob=" + cdob + "&scoring_type=" + scoringType + "&hs_count=" + hsCount + "&misidn=" + misidn + "&email=" + email + "&street_name=" + streetname + "&street_number=" + streeetnumber + "&zip=" + zip + "&city=" + city + "&nbr_sim_cards=" + nbrsimcard+"&id_type="+idType,
                        success: function (result) {                                     
                            scoringResponse = result;  
                           $("#scoringResponse").val(scoringResponse);								
                            if(scoringResponse == 'FALSE'){
                                window.location.href = refusal_page;
                            } else if(!scoringResponse) {
								window.location.href = refusal_page;
							} else {
								step2Ajaxcall();
							}
                        },
                        complete: function () {                            
                        }
                    });                                    
                }
				else
				{
				  step2Ajaxcall();
				}

});
function step2Ajaxcall()
{
 var shippingInformation   = $('form#checkout_form').serializeArray();
 var ajaxStep = "step2";
 var session_url = $("#session_url").val();
 var checkstepstat = "final";
 var val_c_delivery_address = $("input[name='c_delivery_address']:checked").val();
 /** Hide coupon message in total section if no rule applied**/
	if(window.checkoutConfig.quoteData.applied_rule_ids == null || window.checkoutConfig.quoteData.applied_rule_ids == ""){
		$('.coupon-message').addClass('disable_div');
	} else {
		$('.coupon-message').removeClass('disable_div');
	}
  shippingInformation = JSON.stringify(shippingInformation);
  $.ajax({
        url: session_url,
        data: {saveData:shippingInformation ,aStep: ajaxStep,checkstepstat:checkstepstat},
        type: 'POST',
        dataType: 'json',
        showLoader: true,
        context: $('#step2')
    }).done(function(data){
	//  $('#session_step').val("final");
	      if(val_c_delivery_address == 1)
	{
	$("#s_method_freeshipping_freeshipping").trigger('click');
	} else if(val_c_delivery_address == 3) {
		$("#s_method_bpost_bpost").trigger('click');
	} else {
		$("#s_method_freeshipping_freeshipping").trigger('click');
	} 
	     $('#step2_information').addClass("disable_div");
		$("#step2_tab" ).removeClass( "active" );
		$("#step2_tab").addClass("done");
		$("#step3_tab" ).removeClass( "last" );
		$("#step3_tab").addClass("last active");
		if($("#step3_tab").hasClass("active")){
		   $('.newpayment-cart').show();
		 $('.removepayment-cart').hide();
		}
        window.location.hash = "payment";
	    $('#step').val("final");
		$('#step3_payment').removeClass("disable_div");
		$(".iwd-opc-shipping-method").hide();
		$(".shippingaddressbottom").addClass("disable_div");
		$('.cartitemvalueonepage').html($('.onepagetopcart').html());
		$('.cartcouponform').html($('.cart_coupon_from').html());
		$('#subscription-cart-total-iwd').html($('.subscription-cart-total-iwd').html());
		$('#subsidy-cart-total-iwd').html($('.subsidy-cart-total-iwd').html());
		if($('.discount-cart-total-iwd').length) {			
			$('.discount-cart-coupon').html($('.discount-cart-total-iwd').html());					
		}
		else {			
			var subscriptiontotal = window.checkoutConfig.quoteData.subscription_total;
			var orisubscriptiontotal = window.checkoutConfig.quoteData.ori_subscription_total;
			var subscriptionDiscount = orisubscriptiontotal - subscriptiontotal;
			subscriptionDiscount = subscriptionDiscount * -1;
			var pricePattern = window.checkoutConfig.priceFormat.pattern;
			var formattedprice = pricePattern.replace('%s', subscriptionDiscount).replace(/^\s\s*/, '').replace(/\s\s*$/, '');			
			$('.discount-cart-coupon').html(formattedprice);
		}
		//$('.discount-cart-coupon-code').html($('.discount-coupon-code').html());
		$('.cartcouponform').html($('.cart_coupon_from').html());
		$('.cartcouponform').show();
		if(window.checkoutConfig.quoteData.applied_rule_ids == null || window.checkoutConfig.quoteData.applied_rule_ids == ""){
			$('.coupon-message').addClass('disable_div');
		} else {
			$('.coupon-message').removeClass('disable_div');
		}
		$('.discount-cart-coupon-code').html(window.checkoutConfig.quoteData.coupon_description);
		$('#iwd-grand-total-item').html($('.iwd-grand-total-item').html()); 
		$('#iwd-grand-total-item').addClass('iwd-grand-total-item');
		if ($(".shipping-method-title-onepage")[0]){
			$('.shipping-method-rightside').html($('.shipping-method-title-onepage').html());
		} else {
		   //$(".total-shipping-section").hide();
		}
		$('#iwd-grand-total-item').addClass('iwd-grand-total-item');
		$("span.iwd-grand-total-item").addClass('orange');
		
		if ($('.virtualproductonepage').val()==1) {
				$("#payment .action-update").trigger('click');
				$(".creditcard-label").hide();
				$(".netbanking-label").hide();
		}
		if ($('.virtualproductonepage').val()==1 && $('.virtualproductonepage_one').val()==1) {
			$(".total-shipping-section").hide();
		}
		$("#payment").show();
		$(".cartitemvalueonepage").show();
		$(".cartitemvalueonepageside").show();
		$(".cartcouponform").show();
		if(window.checkoutConfig.quoteData.applied_rule_ids == null || window.checkoutConfig.quoteData.applied_rule_ids == ""){
			$('.coupon-message').addClass('disable_div');
		} else {
			$('.coupon-message').removeClass('disable_div');
		}
		 if($('#chk_step_stat').val() == "final"){
		 $('.newpayment-cart').show();
		 $('.removepayment-cart').hide();
		 }
		 else{
		 $('.newpayment-cart').hide();
		 $('.removepayment-cart').show();
		 }
		if(window.checkoutConfig.quoteData.coupon_code)
		{
			//$(".iwd-coupon-code-item").show();
			$('.coupon_summary').show();
		}
		$(".iwd-discount-options").show();
		
        });
		$('.step-mage-error').hide();
		if($("#edit-nationality-virement").is(':checked')) {
			 $(".domiciliation-message").hide();
		}
		var grandTotal = window.checkoutConfig.quoteData.grand_total;
		if(grandTotal < 1) {
			$('#checkout-payment-method-load').hide();
		}
		$("html, body").animate({ scrollTop: 0 }, 600);
}
/////////////////////////////////////////////////////////////
$('input:radio[name="c_delivery_address"]').change(function(){
        if (this.checked && this.value == '1') {
          $("input[name='billing-address-same-as-shipping']").prop('checked', true);		
          var last_name       = 	$('#last_name').val();
		  var b_postcode_city = 	$('#b_postcode_city').val();
		  var b_street        = 	$('#b_street').val();
		  var b_number        = 	$('#b_number').val();
		  var b_box           = 	$('#b_box').val();
		  $('#s_name').val(last_name);
		  $('#s_postcode_city').val(b_postcode_city);
		  $('#s_street').val(b_street);
		  $('#s_number').val(b_number);
		  $('#s_box').val(b_box);
		  $("#div_shipping_address" ).removeClass("disable_div");
		  $("#div_shipping_address").addClass("disable_div");
		  $("#bpost").addClass("disable_div");
		  //$("#s_method_freeshipping_freeshipping").trigger('click');
        } else if (this.checked && this.value == '3') {
			//$("#s_method_bpost_bpost").trigger('click');
			$("#div_shipping_address" ).removeClass("disable_div");
			$("#div_shipping_address").addClass("disable_div");
			$("#bpost").removeClass("disable_div");
		} else {
			//$("#s_method_freeshipping_freeshipping").trigger('click');
			$("#bpost").addClass("disable_div");
			$("#div_shipping_address" ).removeClass("disable_div");
			$("input[name='billing-address-same-as-shipping']").prop('checked', false);
			$("input[name='billing-address-same-as-shipping']:checked").click();
			$('#s_name').val('');
			$('#s_firstname').val('');
			$('#s_edit_city').val('');
			$('#s_attention_n').val('');
			$('#s_postcode_city').val('');
			$('#s_street').val('');
			$('#s_number').val('');
			$('#s_box').val('');
		}
    });
	//Subscription Change////////////////////////////////////////////////////////
			$('.a_subscription_type').click(function(){
				 var subscriptionType = $(this).attr('data-dir');
				 $('#subscription_type').val(subscriptionType);
				 $('a#subscription_div_a').text(subscriptionType);
	
			});
	/////////////////////////////////////////////////////////////////////////		
	//Simcard Change
	$('.design_sim_number').on('click', function (){
		//$("#step1_continue_div").hide();
		var id = this.id;
		var designsimid = id.split("_");
		var id = designsimid[1]+"_"+designsimid[2];
		var totalpostpaidValue = $("#totalvirtualproduct").val();
		var res;
		
		if (totalpostpaidValue.indexOf(',') != -1) {
			var res = totalpostpaidValue.split(",");
		} else {
			res = [totalpostpaidValue];
		}
		if (this.checked && this.value == '2') {
			$("#designteexistingnumber_"+id+"-error").html('');
			$("#design_te_existing_number_final_validation_"+id).val('0');
        }
        if (this.checked && this.value == '1') {
		 $("#designteexistingnumber_"+id).show();
		 $("#designteexistingnumber_"+id ).removeClass( "form-control required valid mage-error");
	     $("#designteexistingnumber_"+id).addClass( "form-control" );
		 // validation missing
		 $("#designteexistingnumber_"+id).removeAttr( "data-validate" );
         $("#designteexistingnumber_"+id).attr("data-validate", "{'custom-required':true,'validate-orangenumber':true,'validate-orangenumber-total':true,'restriction-postpaid-number':true}");
		 if ($("#design_te_existing_number_final_validation_"+id).val() == 0) {
				showDivButtons(3);
			 }
        }
		else
		{
		 $("#designteexistingnumber_"+id).removeAttr( "data-validate" );
		 $('#design_te_existing_number-error').hide();
		 $("#designteexistingnumber_"+id).hide();
		 $("#designteexistingnumber_"+id).val('');
		 $("#design_te_existing_number_final_validation_"+id).val('0');
		 $("#network_customer_number_"+id ).val('');
		 $("#simcard_number_"+id ).val('');
		 $("#holders_name_"+id ).val('');
		 $("input:text[name='custom_attributes[holders_name]']").val('');
		 $("input:text[name='custom_attributes[holders_name]']").keyup();	
		 $("#holder_name_"+id).val('');
		 existing = '';
		//$("input:text[name='custom_attributes[holder_name]']").val('');
		//$("input:text[name='custom_attributes[holder_name]']").keyup();
		/* var showValue = 1;
         for (var i = 0; i < res.length; i++) {
			//design_sim_number-2445
			if($("input[name='design_sim_number-"+res[i]+"']:checked" ).val() == '1' ) {
			  
			  	if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 3 ) {
					showValue = 2;
				} else if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 0) {
					showValue = 3;
				} 
			}
         }*/
		 
		 
		         var showValue = 1;
				 var showFirstValue = '';
				 var showSecondValue = '';
				 var showThirdValue = '';
				 var iewChecking = '1';
				 var iewItemsCheck = $("input:hidden[name='iew_items']").val();
				 for (var i = 0; i < res.length; i++) {
					//design_sim_number-2445
					if($("input[name='design_sim_number-"+res[i]+"']:checked" ).val() == '1' ) {
					  
						if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 1 ) {
							showFirstValue = 1;
						} 
						if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 2 ) {
							showSecondValue = 4;
							if(iewItemsCheck.indexOf(res[i]) <= 0) {
								iewChecking == '2';
							}
						}
						if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 3 ) {
							showThirdValue = 2;
						}
						if ($("#design_te_existing_number_final_validation_"+res[i]).val() == 0) {
							showValue = 3;
						} 
					}
				 }
				 if (showValue == 3) {
					showDivButtons(showValue);
				 } else if (showSecondValue == 4) {	
					showDivButtons(showSecondValue);
					if (res.length > 1) {
					        if (iewChecking == "1") {
									if ($( "#continue-delete-button" ).hasClass( "continue-delete-step1-existing" )) { //continue-delete-step1
										$("#continue-delete-button").removeClass('continue-delete-step1-existing');
									}
									if (!$( "#continue-delete-button" ).hasClass( "continue-delete-step1" )) {
											$("#continue-delete-button").addClass('continue-delete-step1');
									}
									$("#customer-zone-button").hide();
									$("#customer-zone-des").hide();
									$("#continue-delete-button-top").show();
									$("#continue-delete-button").show();
							} else {
								if ($( "#continue-delete-button" ).hasClass( "continue-delete-step1" )) {
									$("#continue-delete-button").removeClass('continue-delete-step1');
								}
								if (!$( "#continue-delete-button" ).hasClass( "continue-delete-step1-existing" )) {
										$("#continue-delete-button").addClass('continue-delete-step1-existing');
								}
								$("#customer-zone-button").hide();
					            $("#customer-zone-des").hide();
								$("#continue-delete-button-top").show();
								$("#continue-delete-button").show();
							}
						}
				 } else if (showThirdValue == 2) {
					showDivButtons(showThirdValue);
				 } else if (showFirstValue == 1) {
				   showDivButtons(showFirstValue);
				 } else {
					showDivButtons(showValue);
				 }
				 //IEW UPDATE
				if($('input.iew_sim:radio:checked').length > 0 && showValue != 3){
				    $("#customer-zone-button").hide();
					$("#customer-zone-des").hide();
					$("#continue-delete-button-top").show();
					$("#continue-delete-button").show();
					$("#step1_continue_div").hide();
					$('#existing_number_postpaid_div').show();
					$("#step1_continue_div_exiting").addClass("disable_div");
				}
				//EOF IEW Update
		 
         //showDivButtons(showValue);
		 //validatepostpaidValidation();
		}
		
/* 		$("input[name='design_te_existing_number']").on('change', function (){
			number_validation();
			$("input:text[name='custom_attributes[te_existing_number]']").val($("input[name='design_te_existing_number']").val());
			$("input[name='custom_attributes[te_existing_number]']").keyup();

		}); */
    });
	
	$(".design_te_existing_number").keyup( function(){
	    var str = this.value;
		var replaced = str.replace(/[\. ,:_/]+/g, "");
		var id = this.id;
		var designsimid = id.split("_");
		var id = designsimid[1] + "_"+designsimid[2];
		var existingNumberfinal = $("#design_te_existing_number_final_validation_"+id).val();
		$("#design_te_existing_number_final_validation_"+id).val('0');
	    if (replaced.length < 10) {
			showDivButtons(3);
			existing = '';
		} else {
		    validatepostpaidValidation();
			$(checkout_form).validation();
			var validate = $.validator.validateElement(this);
			if (validate == false) {
				return false;
			}
			if (existing == this.value) {
			    $("#design_te_existing_number_final_validation_"+id).val(existingNumberfinal);
				return false;
			}
			existing = this.value;
			$("#design_te_existing_number_final_validation_"+id).val('0');
			number_validation(this.value,id);
			
		}
	});
	$(".design_te_existing_number").blur( function(){
		var id = this.id;
		var designsimid = id.split("_");
		var id = designsimid[1];
		$(checkout_form).validation();
		var validate = $.validator.validateElement(this);
		if (validate == false) {
			return false;
		}
	   // number_validation(this.value,id);
	//$("input:text[name='custom_attributes[te_existing_number]']").val($("input[name='design_te_existing_number']").val());
	//$("input[name='custom_attributes[te_existing_number]']").keyup();		
	});
	//Tab Hide
	$('#step2_tab').click(function(){
      var stepValue =  $('#step').val();
	  $('.newpayment-cart').hide();
	 $('.removepayment-cart').show();
	  final_ajax_call();
	  if(stepValue == 'final')
	  {
	       $('#chk_step_stat').val('step3');
           $('#session_step').val('step3');
		   $('#step3_payment').addClass("disable_div");
		   $("#step1_tab" ).removeClass( "first active" );
		   $("#step1_tab").addClass("first done");
		   $("#step2_tab" ).removeClass( "done" );
		   $("#step2_tab").addClass("active");
		   $("#step3_tab" ).removeClass( "last active" );
		   $("#step3_tab").addClass("last");
	       $('#step').val("step3");
		   $('#step2_information').removeClass("disable_div");
		   $("#step1_exiting_num").show(); 
		   $("#transfer_number").addClass("disable_div"); 
           $('.cartcouponform').hide();
		$(".iwd-opc-shipping-method").show();
		var nationalityCheck = $("input[name='nationality']:checked").val();
		var nationalityCheckreg = $("input[name='registered']:checked").val();
		if(nationalityCheck == "belgian" || nationalityCheck == '' || nationalityCheckreg == '1') {
			$(".shippingaddressbottom").removeClass("disable_div");
		}
		$("#payment").hide();
		$(".cartitemvalueonepage").hide();
		$(".cartitemvalueonepageside").hide();
		$(".cartcouponform").hide();		
		$('.coupon-message').addClass('disable_div');
		$(".iwd-discount-options").hide();
	  }
	  $("#coupon-error-message").hide();
	  $(".iwd-discount-options").hide();
	  
           if($('#step2_tab').hasClass("active")){
               window.location.hash = "details";
           }
	    
    });
	
		//Tab Hide
	$('#step1_tab').click(function(){
		$('.newpayment-cart').hide();
		$('.removepayment-cart').show();
	  final_ajax_call();
      var stepValue =  $('#step').val();
	  if(stepValue == 'final' || stepValue == 'step3' )
	  {
           $('#session_step').val("step2");
		   $('#step2_information').addClass("disable_div");
		   $('#step3_payment').addClass("disable_div");
		   $("#step1_tab" ).removeClass( "first done" );
		   $("#step1_tab").addClass("first active");
		   $("#step2_tab" ).removeClass( "done" );
		   $("#step2_tab" ).removeClass( "active" );
		   $("#step3_tab" ).removeClass( "last active" );
		   $("#step3_tab").addClass("last");
	       $('#step').val("step2");
		   $('#step1_number').removeClass("disable_div");
		   $("#step1_exiting_num").show(); 
		   $("#transfer_number").addClass("disable_div"); 
	       $(".iwd-opc-shipping-method").hide();
		   $(".shippingaddressbottom").addClass("disable_div");
		   $("#payment").hide();
		   $(".cartitemvalueonepage").hide();
			$('.cartcouponform').hide();			
			$('.coupon-message').addClass('disable_div');
			$(".cartitemvalueonepageside").hide();
			$(".iwd-discount-options").hide();
	  }
	   $("#coupon-error-message").hide();
	  $(".iwd-discount-options").hide();
	     
          if($('#step1_tab').hasClass("active")){
               window.location.hash = "number";
           }
	     
    });
	
	 // Profile Dropdown ONCHANGE Event
  // ====================
               $("#tx_profile_dropdown").change(function () {
            var profileStatus = $('#tx_profile_dropdown').val();
			    if(profileStatus == 1)
				   {
				 	var profilename = "Profession libérale sans numéro de TVA";
					$('#legal_status').val('');
					$("input:text[name='custom_attributes[legal_status]']").val('');
					$("input:text[name='custom_attributes[legal_status]']").keyup();
					$('#company_name').val('');
					$("input:text[name='custom_attributes[company_name]']").val('');
					$("input:text[name='custom_attributes[company_name]']").keyup();
					$('#vat_number').val('');
					$("input:text[name='custom_attributes[vat_number]']").val('');
					$("input:text[name='custom_attributes[vat_number]']").keyup();		
					$("#company_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#legal_status_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#vat_number_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#company_div" ).addClass( "row margin-sm-b-s disable_div" );
					$("#legal_status_div" ).addClass( "row margin-sm-b-s disable_div" );
					$("#vat_number_div").addClass("row margin-sm-b-s");
					$("#vat_number" ).removeClass( "form-control required mage-error" );
	                $("#vat_number" ).addClass( "form-control" );
					$('#vat_number').removeAttr( "data-validate" );
					$("#vat_number").attr("data-validate", "{'custom-required':true, 'validate-new-alphanum':true,'vat-number-min-max-lenth-validation':true}");
     				$('#company_name').removeAttr( "data-validate" );
	                $("#legal_status").removeAttr( "data-validate" ); 
				 }
				 else if(profileStatus == 2)
				 {
				    var profilename = "Profession libérale avec numéro de TVA";
					$('#legal_status').val('');
					$("input:text[name='custom_attributes[legal_status]']").val('');
					$("input:text[name='custom_attributes[legal_status]']").keyup();
					$('#company_name').val('');
					$("input:text[name='custom_attributes[company_name]']").val('');
					$("input:text[name='custom_attributes[company_name]']").keyup();
					$('#vat_number').val('');
					$("input:text[name='custom_attributes[vat_number]']").val('');
					$("input:text[name='custom_attributes[vat_number]']").keyup();
					$("#company_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#legal_status_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#vat_number_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#company_div" ).addClass( "row margin-sm-b-s disable_div" );
					$("#legal_status_div" ).addClass( "row margin-sm-b-s disable_div" );
					$("#vat_number_div" ).addClass( "row margin-sm-b-s disable_div" );
					$('#vat_number_validation_error').html('');
					$('#vat_number').removeAttr( "data-validate" );
					$('#company_name').removeAttr( "data-validate" );
	                $("#legal_status").removeAttr( "data-validate" ); 
	
		   
				 }
				  else if(profileStatus == 3)
				 {
				    var profilename = "Entreprise";
				  	$('#legal_status').val('');
					$("input:text[name='custom_attributes[legal_status]']").val('');
					$("input:text[name='custom_attributes[legal_status]']").keyup();
					$('#company_name').val('');
					$("input:text[name='custom_attributes[company_name]']").val('');
					$("input:text[name='custom_attributes[company_name]']").keyup();
					$('#vat_number').val('');
					$("input:text[name='custom_attributes[vat_number]']").val('');
					$("input:text[name='custom_attributes[vat_number]']").keyup();	
					$("#vat_number_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#company_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#legal_status_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#vat_number_div").addClass("row margin-sm-b-s");
					$("#company_div").addClass("row margin-sm-b-s");
					$("#legal_status_div").addClass("row margin-sm-b-s");
					$('#vat_number').removeAttr( "data-validate" );
					$("#vat_number").attr("data-validate", "{'custom-required':true, 'validate-new-alphanum':true,'vat-number-min-max-lenth-validation':true}");
					$('#company_name').removeAttr( "data-validate" );
					$("#company_name").attr("data-validate", "{'custom-required':true}");
					//$('#company_name').removeAttr( "data-validate" );
				    $("#legal_status").removeAttr( "data-validate" ); 
					$("#legal_status").attr("data-validate", "{'custom-required':true}");
	           	
					
				 
				 }
				  else if(profileStatus == 4)
				 {
				    var profilename = "Indépendant";
					$('#legal_status').val('');
					$("input:text[name='custom_attributes[legal_status]']").val('');
					$("input:text[name='custom_attributes[legal_status]']").keyup();
					$('#company_name').val('');
					$("input:text[name='custom_attributes[company_name]']").val('');
					$("input:text[name='custom_attributes[company_name]']").keyup();
					$('#vat_number').val('');
					$("input:text[name='custom_attributes[vat_number]']").val('');
					$("input:text[name='custom_attributes[vat_number]']").keyup();	
					$("#company_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#legal_status_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#vat_number_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#vat_number_div").addClass("row margin-sm-b-s");
					$("#company_div").addClass("row margin-sm-b-s disable_div");
					$("#legal_status_div").addClass("row margin-sm-b-s disable_div");
					$('#vat_number').removeAttr( "data-validate" );
					$("#vat_number").attr("data-validate", "{'custom-required':true, 'validate-new-alphanum':true,'vat-number-min-max-lenth-validation':true}");
					$('#company_name').removeAttr( "data-validate" );
			        $("#legal_status").removeAttr( "data-validate" );
				 }
				 	  else 
				 {
	                $('#legal_status').val('');
					$("input:text[name='custom_attributes[legal_status]']").val('');
					$("input:text[name='custom_attributes[legal_status]']").keyup();
					$('#company_name').val('');
					$("input:text[name='custom_attributes[company_name]']").val('');
					$("input:text[name='custom_attributes[company_name]']").keyup();
					$('#vat_number').val('');
					$("input:text[name='custom_attributes[vat_number]']").val('');
					$("input:text[name='custom_attributes[vat_number]']").keyup();
					$("#company_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#legal_status_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#vat_number_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#company_div" ).addClass( "row margin-sm-b-s disable_div" );
					$("#legal_status_div" ).addClass( "row margin-sm-b-s disable_div" );
					$("#vat_number_div" ).addClass( "row margin-sm-b-s disable_div" );
					$('#vat_number_validation_error').html('');
					$('#vat_number').removeAttr( "data-validate" );
					$('#company_name').removeAttr( "data-validate" );
		            $("#legal_status").removeAttr( "data-validate" ); 				 
				 }
    });

	    	 //Nationality Check Event
  // ====================
	 
		$('input:radio[name="nationality"]').change(
		    function(){
        if (this.checked && this.value == 'belgian') {
                 	$("#passport_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#residence_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#q_registered_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#code_div" ).removeClass( "row margin-xs-t-l disable_div" );
				    $("#code_div" ).addClass( "row margin-xs-t-l" );
					$("#passport_div").addClass("col-xs-12 col-sm-6");
					$("#residence_div").addClass("col-xs-12 col-sm-6 col-sm-offset-6 disable_div");
					$("#q_registered_div").addClass("col-xs-12 col-sm-6 margin-xs-t-s disable_div");
					$("#residence_number" ).removeClass( "form-control required mage-error" );
	                $("#residence_number" ).addClass( "form-control" );
					$('#residence_number').removeAttr( "data-validate" );
		            $('#residence_number-error').hide();
					$('#passport_number-error').hide();
					$('#passport_number').val('');
					$('#passport_number').removeClass('mage-error');
					$('#id_number').val('');
					$('#residence_number').val('');
					$("input:text[name='custom_attributes[passport_number]']").val('');
					$("input:text[name='custom_attributes[passport_number]']").keyup();
					$("input[name='custom_attributes[residence_number]']").val('');
					$("input[name='custom_attributes[residence_number]']").keyup();
					$("#label_idnumber_div" ).removeClass( "disable_div" );
					$("#id_number" ).removeClass( "disable_div" );
					$("#label_passport_div" ).addClass( "disable_div" );
					$("#id_number" ).addClass( "disable_div" );
					$("#label_idnumber_div" ).removeClass( "disable_div" );
					$("#passport_number" ).addClass( "disable_div" );
					$("#id_number" ).removeClass( "disable_div" );
					$('.bundle-hide').removeClass('disable_div');
					$('#error_div').addClass('disable_div');
        }
		else
		{
		         	$("#passport_div" ).removeClass( "disable_div" );
					$('#id_number-error').hide();
					$("#label_passport_div" ).removeClass( "disable_div" );
					$("#label_idnumber_div" ).addClass( "disable_div" );
					$("#passport_number" ).removeClass( "id_number_class" );
					$("#residence_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#q_registered_div" ).removeClass( "row margin-sm-b-s disable_div" );
					$("#code_div" ).removeClass( "row margin-xs-t-l disable_div" );
				    $("#code_div" ).addClass( "row margin-xs-t-l disable_div" );
					$("#passport_div").addClass("disable_div");
					$("#residence_div").addClass("col-xs-12 col-sm-6 col-sm-offset-6");
					$("#q_registered_div").addClass("col-xs-12 col-sm-6 margin-xs-t-s");
				    $("#residence_number").attr("data-validate", "{'custom-required':true,'validate-residence':true}");
					$('#passport_number').val('');
					$('#id_number').val('');
					$('#id_number').removeClass("mage-error");
					$('#residence_number').val('');
					$('#client_code').val('');
					$('#client_name').val('');
					$("input:text[name='custom_attributes[passport_number]']").val('');
					$("input:text[name='custom_attributes[passport_number]']").keyup();
					$("input[name='custom_attributes[residence_number]']").val('');
					$("input[name='custom_attributes[residence_number]']").keyup();
					$("input:text[name='custom_attributes[client_code]']").val('');
					$("input:text[name='custom_attributes[client_code]']").keyup();
					$("input:text[name='custom_attributes[client_name]']").val('');
					$("input:text[name='custom_attributes[client_name]']").keyup();					
		}
    });
	  
	
	function validatepostpaidValidation() {
	        var totalpostpaidValue = $("#totalvirtualproduct").val();
			var res = new Array();
			if (totalpostpaidValue.indexOf(',') != -1) {
				var res = totalpostpaidValue.split(",");
			} else {
				res = [totalpostpaidValue];
			}
			$(checkout_form).validation();
			
			for (var i = 0; i < res.length; i++) {
				var iewClassNameCheck = $("input[name='design_sim_number-"+res[i]+"']").attr('class');
				if(~iewClassNameCheck.indexOf("iew_sim")) {
					continue;
				}
				var validateDesignsimnumber = $("input[name='design_sim_number-"+res[i]+"']:checked").val();
				if (validateDesignsimnumber == '1') {
					var validate = true;
					if ($("input[name='design_te_existing_number-"+res[i]+"']").hasClass('mage-error')) {
						$.validator.validateElement($("input[name='design_te_existing_number-"+res[i]+"']"));
					}
				}
			 }
	}

	  	var finalvalueLoad = 0;
		var addresscheckload = 0;
		var paymentcheckload = 0;
		var loaderon         = 0;
		var deliveryaddressValue = 1;
		 setInterval(function(){
			 var postcode = $("input:text[name='postcode']").val();
		 if (paymentcheckload ==0 && typeof  postcode != 'undefined') {
		 
		 var session_step  = $("#session_step").val();
		 
		
		 if(session_step == 'final')
		 {
		 	 if(loaderon == 0)
		 {
		 $('[data-role="pannel"]').trigger('show.loader');
		 loaderon = 1;
		 }
	     var pre_onepage_trigger = $("#pre_onepage_trigger").val();
		 	if(pre_onepage_trigger  == 1)
		{
		 onepage_trigger();
		}
		 var prepaid_check = $("#prepaid_check").val();
         var oFirstName    =  $("input:text[name='first_name']").val();
		 var oLastName     =  $("input:text[name='last_name']").val();
		 var postalCity    = $("input[name='b_postcode_city']").val();
		  if(prepaid_check == 1)
	      {
		  var otelephone    = $("input[name='cust_telephone']").val(9999999999);
	      }
		  else
		  {
		  var otelephone    = $("input[name='cust_telephone']").val();
		  }
		
	
	if (!$("input:text[name='firstname']").val() && $("input:text[name='first_name']").val()) {
		$("input:text[name='firstname']").val($("input:text[name='first_name']").val());
		$("input[name='firstname']").keyup();
	}
	
	if (!$("input:text[name='lastname']").val() && $("input:text[name='last_name']").val()) {
		$("input:text[name='lastname']").val($("input:text[name='last_name']").val());
		$("input[name='lastname']").keyup();
	}
    if(prepaid_check == 1)
	{
	  if (!$("input:text[name='telephone']").val()) {
		$("input:text[name='telephone']").val(9999999999);
		$("input[name='telephone']").keyup();
	  }
	}
	else
	{
	  if (!$("input:text[name='telephone']").val() && otelephone) {
		$("input:text[name='telephone']").val(otelephone);
		$("input[name='telephone']").keyup();
	  }
	
	}
	if (!$("input:text[name='postcode']").val() && $("input[name='b_postcode_city']").val()) {
		var postalCity = $("input[name='b_postcode_city']").val();
		$("input:text[name='postcode']").val(postalCity);
		$("input[name='postcode']").keyup();
	}
	
	if (!$("#customer-email").val() && $("input[name='email']").val()) {
	   $("#customer-email").val($("input[name='email']").val());
	   $("#customer-email").change();
	}
	
	if (!$("input:text[name='city']").val() && $("input:text[name='b_city']").val()) {
		$("input:text[name='city']").val($("input:text[name='b_city']").val());
		$("input:text[name='region']").val($("input:text[name='b_city']").val());
		$("input[name='city']").keyup();
		$("input[name='region']").keyup();
	}
	if (!$("input:text[name='street[0]']").val() && $("input:text[name='b_street']").val()) {
	     $("input:text[name='street[0]']").val($("input:text[name='b_street']").val());
		$("input[name='street[0]']").keyup();
		 $("input:text[name='street[1]']").val($("input:text[name='b_box']").val());   
		$("input[name='street[1]']").keyup();
	}
	
	if (!$("input:text[name='custom_attributes[street_number]']").val() && $("input:text[name='b_number']").val()) {
		$("input:text[name='custom_attributes[street_number]']").val($("input:text[name='b_number']").val());
		$("input:text[name='custom_attributes[street_number]']").keyup();
	}
	
	if (!$("input:text[name='custom_attributes[client_code]']").val() && $("input:text[name='client_code']").val()) {
		$("input:text[name='custom_attributes[client_code]']").val($("input:text[name='client_code']").val());
		$("input:text[name='custom_attributes[client_code]']").keyup();
	}
	
	if (!$("input:text[name='custom_attributes[client_name]']").val() && $("input:text[name='client_name']").val()) {
		$("input:text[name='custom_attributes[client_name]']").val($("input:text[name='client_name']").val());
		$("input:text[name='custom_attributes[client_name]']").keyup();
	}
	
	$("input:text[name='custom_attributes[dob]']").change();
	//$("#s_method_freeshipping_freeshipping").trigger('click');
    var val_c_delivery_address = $("input[name='c_delivery_address']:checked").val();
	var val_last_name       = 	$('#last_name').val();
	var val_b_postcode_city = 	$('#b_postcode_city').val();
	var val_b_street        = 	$('#b_street').val();
	var val_b_number        = 	$('#b_number').val();
	var val_b_box           = 	$('#b_box').val();
	var id_number         = true; 
	if(val_c_delivery_address == 1)
	{
	    $('#s_name').val(val_last_name);
		$('#s_firstname').val($('#first_name').val());    
		$('#s_postcode_city').val(val_b_postcode_city);
		$('#s_street').val(val_b_street);
		$('#s_number').val(val_b_number);
		$('#s_box').val(val_b_box);
		$('#s_city').val($("input:text[name='b_city']").val());
		$("input:text[name='custom_attributes[bpost_postal_location]']").val('');
		$("input:text[name='custom_attributes[bpost_method]']").val('');

	} else if(val_c_delivery_address == 3) {
		var customerPostalLocation = $('#customerPostalLocation').val();		
		var customerFirstName = $('#customerFirstName').val();
		var customerLastName = $('#customerLastName').val();
		var customerStreet = $('#customerStreet').val();
		var customerStreetNumber = $('#customerStreetNumber').val();
		var customerPostalCode = $('#customerPostalCode').val();
		var customerCity = $('#customerCity').val();
		var deliveryMethod = $('#deliveryMethod').val();
		$('#s_number').removeAttr( "data-validate" );
		$('#s_firstname').val(customerFirstName);
		$('#s_name').val(customerLastName);
		$('#s_postcode_city').val(customerPostalCode);
		$('#s_street').val(customerStreet);
        $('#s_edit_city').val(customerCity);
		$('#s_number').val(customerStreetNumber);
		$('#s_box').val('');
		$("input:text[name='custom_attributes[bpost_postal_location]']").val(customerPostalLocation);
		$("input:text[name='street[0]']").val(customerStreet+" "+customerStreetNumber);   
		$("input:text[name='street[1]']").val('');   
		$("input[name='postcode']").val(customerPostalCode);
		$("input:text[name='region']").val(customerCity);
		$("input:text[name='city']").val(customerCity);
		$("input:text[name='firstname']").val($("input:text[name='first_name']").val());
		$("input:text[name='lastname']").val($("input:text[name='last_name']").val());
		$("input:text[name='custom_attributes[bpost_method]']").val(deliveryMethod);
	} else if(val_c_delivery_address == 2) {
		$("input:text[name='custom_attributes[bpost_postal_location]']").val("");
		$("input:text[name='custom_attributes[bpost_method]']").val('');		
		$("input:text[name='firstname']").val($("input:text[name='first_name']").val());
		$("input:text[name='lastname']").val($("input:text[name='last_name']").val());
		$("input:text[name='city']").val($("input:text[name='s_city']").val());
		$("input:text[name='region']").val($("input:text[name='s_city']").val());
		$("input[name='postcode']").val($("input:text[name='s_postcode_city']").val());	
	}
	
	$("input[name='transfer_type'][value='Virement']").prop('checked', 'checked');
	$("input:text[name='firstname']").keyup();
	$("input:text[name='lastname']").keyup();
	$("input:text[name='street[0]']").keyup();  
	$("input:text[name='street[1]']").keyup();
	$("input[name='postcode']").keyup();
	$("input:text[name='region']").keyup();
	$("input:text[name='city']").keyup();
	$("input:text[name='prefix']").val($("input:radio[name='gender']").val());
	$("input:text[name='prefix']").keyup();
	$("input:text[name='custom_attributes[bpost_method]']").keyup();
	$("input:text[name='custom_attributes[bpost_postal_location]']").keyup();
	if (virtualhideOtherOperatorValue == "1" && virtualhideDeliveryValue =="1") {
			$(".total-shipping-section").hide();
        }
		$('.newpayment-cart').show();
		$('.removepayment-cart').hide();
			if($("#edit-nationality-virement").is(':checked')) {
				$(".domiciliation-message").hide();
			}
			if($("#bank_transfer_type").is(':checked')) {
				$(".domiciliation-message").show();
			}
		var grandTotal = window.checkoutConfig.quoteData.grand_total;
		if(grandTotal < 1) {
			 $('#checkout-payment-method-load').hide();
		}
	    $("#step1_number" ).removeClass( "disable_div" );
		$('#step1_number').addClass("disable_div");
		$("#step2_information" ).removeClass( "disable_div" );
		$('#step2_information').addClass("disable_div");
		$("#step1_tab" ).removeClass( "first active" );
		$("#step1_tab").addClass("first done");
		$("#step2_tab" ).removeClass( "active" );
		$("#step2_tab").addClass("done");
		$("#step3_tab" ).removeClass( "last" );
		$("#step3_tab").addClass("last active");
        window.location.hash = "payment";
	    $('#step').val("final");
		$('#step3_payment').removeClass("disable_div");
		$(".iwd-opc-shipping-method").hide();
		$(".shippingaddressbottom").addClass("disable_div");
		$('.cartitemvalueonepage').html($('.onepagetopcart').html());
		$('#subscription-cart-total-iwd').html($('.subscription-cart-total-iwd').html());
		$('#subsidy-cart-total-iwd').html($('.subsidy-cart-total-iwd').html());
		$('.cartcouponform').html($('.cart_coupon_from').html());
		$('.cartcouponform').show();
		if(window.checkoutConfig.quoteData.applied_rule_ids == null || window.checkoutConfig.quoteData.applied_rule_ids == ""){
			$('.coupon-message').addClass('disable_div');
		} else {
			$('.coupon-message').removeClass('disable_div');
		}
		$('#iwd-grand-total-item').html($('.iwd-grand-total-item').html()); 
		$('#iwd-grand-total-item').addClass('iwd-grand-total-item');
		if ($(".shipping-method-title-onepage")[0]){
			$('.shipping-method-rightside').html($('.shipping-method-title-onepage').html());
		} else {
		   //$(".total-shipping-section").hide();
		}
		if ($('.virtualproductonepage').val()==1) {
				$("#payment .action-update").trigger('click');
				$(".creditcard-label").hide();
				$(".netbanking-label").hide();
		}
		if ($('.virtualproductonepage').val()==1 && $('.virtualproductonepage_one').val()==1) {
			$(".total-shipping-section").hide();
		}
		laststepshow();
		
		var blockcvalueonepage = $(".cartitemvalueonepage").css('display');
		var blockConepageside  = $(".cartitemvalueonepageside").css('display');
  
		if( blockcvalueonepage == "none" || blockConepageside == "none" )
		{
        thirdStep();
		}
		else
		{
	   thirdStep();
		}
		var classstep2_information = $('#step2_information').attr('class');
		var classstep1_number      =  $('#step1_number').attr('class');
		if(blockcvalueonepage == "block" && blockConepageside == "block" && $("#payment").css('display')=="block"  )
		{
           var grandtotalfinalval = $("#grandtotalfinalval").val(); 
           if(grandtotalfinalval > 0)
		   { 
		    var shippingFinalClick = $("#s_method_freeshipping_freeshipping").val();
			var bpostFinalClick = $("#s_method_freeshipping_freeshipping").val();
			if (deliveryaddressValue == 1 && typeof shippingFinalClick!='undefined') {
				if(val_c_delivery_address == 1){
					$("#s_method_freeshipping_freeshipping").trigger('click');
					deliveryaddressValue = 2
				} else if(val_c_delivery_address == 3) {
					$("#s_method_bpost_bpost").trigger('click');
					deliveryaddressValue = 2
				} else {
					$("#s_method_freeshipping_freeshipping").trigger('click');
					deliveryaddressValue = 2
				}
			}
			  
		      var payment_radio = $('input[name="payment[method]"]').val()
			  if(typeof payment_radio != 'undefined' ) {
			   paymentcheckload = 1;
			   $("#session_step").val('step2');
			   $('[data-role="pannel"]').trigger('hide.loader');
			 }
				 
			} else {
				 loadcheckouttrigger();
				 paymentcheckload = 1;
				 $("#session_step").val('step2');
				 $('[data-role="pannel"]').trigger('hide.loader');
			}
			
		}
		}
		}
	
	}, 200); 
function thirdStep()
{
   
	    $("#step1_number" ).removeClass( "disable_div" );
		$('#step1_number').addClass("disable_div");
		$("#step2_information" ).removeClass( "disable_div" );
		$('#step2_information').addClass("disable_div");
		$("#step1_tab" ).removeClass( "first active" );
		$("#step1_tab").addClass("first done");
		$("#step2_tab" ).removeClass( "active" );
		$("#step2_tab").addClass("done");
		$("#step3_tab" ).removeClass( "last" );
		$("#step3_tab").addClass("last active");
        window.location.hash = "payment";
	    $('#step').val("final");
		$('#step3_payment').removeClass("disable_div");
		$(".iwd-opc-shipping-method").hide();
		$(".shippingaddressbottom").addClass("disable_div");
		$('.cartitemvalueonepage').html($('.onepagetopcart').html());
		$('#subscription-cart-total-iwd').html($('.subscription-cart-total-iwd').html());
		$('#subsidy-cart-total-iwd').html($('.subsidy-cart-total-iwd').html());
		$('.cartcouponform').html($('.cart_coupon_from').html());
		$('.cartcouponform').show();
		if(window.checkoutConfig.quoteData.applied_rule_ids == null || window.checkoutConfig.quoteData.applied_rule_ids == ""){
		$('.coupon-message').addClass('disable_div');
		} else {
			$('.coupon-message').removeClass('disable_div');
		}
		$('#iwd-grand-total-item').html($('.iwd-grand-total-item').html()); 
		$('#iwd-grand-total-item').addClass('iwd-grand-total-item');
		if ($(".shipping-method-title-onepage")[0]){
			$('.shipping-method-rightside').html($('.shipping-method-title-onepage').html());
		} else {
		   //$(".total-shipping-section").hide();
		}
		if ($('.virtualproductonepage').val()==1) {
				$("#payment .action-update").trigger('click');
				$(".creditcard-label").hide();
				$(".netbanking-label").hide();
		}
		if ($('.virtualproductonepage').val()==1 && $('.virtualproductonepage_one').val()==1) {
			$(".total-shipping-section").hide();
		}
		
		laststepshow();

}
function onepage_trigger()
{
var shippingInformation   = $('form#checkout_form').serializeArray();
 var ajaxStep = "step2";
 var session_url = $("#session_url").val();
 var val_c_delivery_address = $("input[name='c_delivery_address']:checked").val();
 /** Hide coupon message in total section if no rule applied**/
	if(window.checkoutConfig.quoteData.applied_rule_ids == null || window.checkoutConfig.quoteData.applied_rule_ids == ""){
		$('.coupon-message').addClass('disable_div');
	} else {
		$('.coupon-message').removeClass('disable_div');
	}
 shippingInformation = JSON.stringify(shippingInformation);
  $.ajax({
        url: session_url,
        data: {saveData:shippingInformation ,aStep: ajaxStep},
        type: 'POST',
        dataType: 'json',
        showLoader: true,
        context: $('#step2')
    }).done(function(data){
	 $('#pre_onepage_trigger').val("2");
	    });
		$('.step-mage-error').hide();
		if($("#edit-nationality-virement").is(':checked')) {
			 $(".domiciliation-message").hide();
		}
		var grandTotal = window.checkoutConfig.quoteData.grand_total;
		if(grandTotal < 1) {
			$('#checkout-payment-method-load').hide();
		}
		$("html, body").animate({ scrollTop: 0 }, 600);

}
function laststephide()
{
		$('#payment').removeAttr('style');
		$("#payment").attr('style', 'display:none !important; ');
		$('.cartitemvalueonepage').removeAttr('style');
		$(".cartitemvalueonepage").attr('style', 'display:none !important; ');
		$('.cartitemvalueonepageside').removeAttr('style');
		$(".cartitemvalueonepageside").attr('style', 'display:none !important; ');
		$('.cartcouponform').removeAttr('style');
		$(".cartcouponform").attr('style', 'display:none !important; ');
}      
function laststepshow()
{
        $('#payment').removeAttr('style');
		$("#payment").attr('style', 'display:block !important; ');
		$('.cartitemvalueonepage').removeAttr('style');
		$(".cartitemvalueonepage").attr('style', 'display:block !important; ');
		$('.cartitemvalueonepageside').removeAttr('style');
		$(".cartitemvalueonepageside").attr('style', 'display:block !important; ');
		$('.cartcouponform').removeAttr('style');
		$(".cartcouponform").attr('style', 'display:block !important; ');
}
function loadcheckouttrigger()
{ 
               var val_c_delivery_address=$("input[name='c_delivery_address']:checked").val();
               if(val_c_delivery_address==1)
                {
				 $("#s_method_freeshipping_freeshipping").trigger('click');
				 }
				 else if(val_c_delivery_address==3)
				 {
				 $("#s_method_bpost_bpost").trigger('click');
				 }
				 else{$("#s_method_freeshipping_freeshipping").trigger('click');}
            	
			  
}
});


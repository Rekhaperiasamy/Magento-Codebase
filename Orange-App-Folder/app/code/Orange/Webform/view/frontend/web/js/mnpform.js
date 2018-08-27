require(['jquery',
      'jquery/ui',
      'mage/translate',
	  'mage/url',
	  "mage/calendar",
	  'js/tooltipform',
	  ], function ($) {	
		$('[data-toggle="tooltip"]').tooltip();
		$("#calendar_inputField").calendar({
              buttonText:"<?php echo __('Select Date') ?>",
         });
		// $("#edit-first-name").blur(function(){
	    // var fnameValue = $("#edit-first-name").val();
			// if (fnameValue == '') {
			// $('#fnameerror').text("Dit is een verplicht veld.")
			// }
			// else{$('#fnameerror').text("")}
			
		// });
		// $("#calendar_inputField1").blur(function(){
			// var dob = $("#calendar_inputField1").val();
			// if (dob == '') {
				// $('#lnameerror').text("Dit is een verplicht veld.")
			// }
			// else{$('#lnameerror').text("")}
		// });
		$(document).on("click", "#submit", function (e) {
			$(activerform).validation();
			var title = $(document).find('title').text();
			var onum = $("#edit-first-name").val();
			var dob = $('#calendar_inputField1').val();
			var confirmid = $('#edit-confirmid').val();
			var requestid = $('#edit-requestid').val();
			var url = $('#activer-form').attr("action");
			if(onum !== "" && dob !== "" && !$('#calendar_inputField1').hasClass('mage-error')){
			$.ajax({
				type: 'POST',
				url: url,
				showLoader: true,
				data: {onum:onum,dob:dob,confirmid:confirmid,requestid:requestid,title:title},
				success: function(data) {				
					data = jQuery.parseJSON(data);
   				if(data.id === "success"){	
					var baseUrl = data.code;
						window.location.href = baseUrl+"webform/index/Success/"+onum;					
					}else{
						$('.step-mage-error').show();
						$("#edit-first-name-validation-error").empty();
						$("#edit-first-name-validation-error").text(data.value);
						$("#edit-first-name-validation-error").show();
						$("html, body").animate({ scrollTop: 0 }, 600);
					}
					/* }else if(data == "Your date of birth not matched in your order"){
						$('#webformerror').text(data);
					}
					else if(data == "Your order id is invalid"){
						$('#webformerror').text(data);
					}	 */					
				}
			});
			}
		});
		});

require(['jquery', 'Magento_Checkout/js/model/quote', 'Magento_Checkout/js/model/shipping-save-processor','Magento_Checkout/js/model/full-screen-loader', "mage/mage", 'mage/translate'], function ($, quote, shippingSaveProcessor, fullscreenLoader) {
    'use strict';
    var existing = '';
    /*DOB ON Change Validation*/
    $('#c_dob').change(function () {
        var prepaid_check = $("#prepaid_check").val();
        var number_screen = $("#number_screen").val();
        if (number_screen == "" && prepaid_check == 1)
        {
            var c_dob = $.validator.validateElement($("#c_dob"));
        } else
        {
            c_dob = $.validator.validateElement($("#c_dob"));
        }
    });
    /*   On Blur Event        */
    $(".validation_check").blur(function () {
        var prepaid_check = $("#prepaid_check").val();
        var number_screen = $("#number_screen").val();
        var first_name = $("#first_name").val();
        var last_name = $("#last_name").val();
		var s_firstname = $("#s_firstname").val();
        var s_name = $("#s_name").val();
        var cu_ex_invoice_cust_surname = $("#cu_ex_invoice_cust_surname").val();
        var cu_ex_invoice_cust_firstname = $("#cu_ex_invoice_cust_firstname").val();
		var iew_first_name = $(".iew_first_name").val();
        var iew_last_name = $(".iew_last_name").val();
        var id = "#" + this.id;
		var name = this.name;
		var holdersId = name.split("-");
		var iewId = name.split("_");
		var holders_name = $("#holders_name"+holdersId[1]).val();
        var holder_name = $("#holder_name"+holdersId[1]).val();
        $(checkout_form).validation();
        if (this.id == 'c_dob')
        {
            if (number_screen == "" && prepaid_check == 1)
            {
                var c_dob = $.validator.validateElement($("#c_dob"));
            } else
            {
                var c_dob = $.validator.validateElement($("#c_dob"));
                if (c_dob == true)
                {
                    var check_c = date_validation($("#c_dob").val());
                }
            }
        } else if (this.id == 'offer_subscription')
        {
            if ($("#offer_subscription").is(':checked'))
            {
                $("#offer_subscription-error").hide();
                $("#offer_subscription").removeClass('mage-error');
            } else
            {
                $("#offer_subscription-error").show();
                $("#offer_subscription-error").empty();
                $("#offer_subscription-error").append($.mage.__('This is a required fields.'));
                $("#offer_subscription").addClass('mage-error');
            }
        } else if (this.id == 'terms_condition')
        {
            if ($("#terms_condition").is(':checked'))
            {
                $("#terms_condition-error").hide();
                $("#terms_condition").removeClass('mage-error');
            } else
            {
                $("#terms_condition-error").show();
                $("#terms_condition").addClass('mage-error');
            }
        } else if (id == "#first_name" && first_name != '' && last_name != '') {
            $.validator.validateElement($("#last_name"));
            $.validator.validateElement($("#first_name"));
        } else if (id == "#last_name" && first_name != '' && last_name != '') {
            $.validator.validateElement($("#last_name"));
            $.validator.validateElement($("#first_name"));
        } else if (id == "#s_firstname" && s_firstname != '' && s_name != '') {
            $.validator.validateElement($("#s_name"));
            $.validator.validateElement($("#s_firstname"));
        } else if (id == "#s_name" && s_firstname != '' && s_name != '') {
            $.validator.validateElement($("#s_name"));
            $.validator.validateElement($("#s_firstname"));
        } else if (id == "#cu_ex_invoice_cust_surname" && cu_ex_invoice_cust_surname != '' && cu_ex_invoice_cust_firstname != '') {
            $.validator.validateElement($("#cu_ex_invoice_cust_firstname"));
            $.validator.validateElement($("#cu_ex_invoice_cust_surname"));
        } else if (id == "#cu_ex_invoice_cust_firstname" && cu_ex_invoice_cust_surname != '' && cu_ex_invoice_cust_firstname != '') {
            $.validator.validateElement($("#cu_ex_invoice_cust_firstname"));
            $.validator.validateElement($("#cu_ex_invoice_cust_surname"));
        }  else if (id == "#iew_first_name_"+iewId[3] && iew_first_name != '' && iew_last_name != '') {
            $.validator.validateElement($("#iew_last_name_"+iewId[3]));
            $.validator.validateElement($("#iew_first_name_"+iewId[3]));
        } else if (id == "#iew_last_name_"+iewId[3] && iew_first_name != '' && iew_last_name != '') {
            $.validator.validateElement($("#iew_last_name_"+iewId[3]));
            $.validator.validateElement($("#iew_first_name_"+iewId[3]));
        } else if (id == '#holders_name_'+ holdersId[1] && holders_name != '' && holder_name != '') {
            $.validator.validateElement($('#holder_name_'+ holdersId[1]));
            $.validator.validateElement($('#holders_name_'+ holdersId[1]));
        } else if (id == '#holder_name_'+ holdersId[1] && holders_name != '' && holder_name != '') {
            $.validator.validateElement($('#holder_name_'+ holdersId[1]));
            $.validator.validateElement($('#holders_name_'+ holdersId[1]));
        } else {
            $.validator.validateElement($(id));
        }
    });

    $(".validation_check_dropdown").change(function () {
        var prepaid_check = $("#prepaid_check").val();
        var number_screen = $("#number_screen").val();
        var id = "#" + this.id;
        $(checkout_form).validation();
        if (this.id == 'c_dob')
        {
            if (number_screen == "" && prepaid_check == 1)
            {
                var c_dob = $.validator.validateElement($("#c_dob"));
            } else
            {
                var c_dob = $.validator.validateElement($("#c_dob"));
                if (c_dob == true)
                {
                    var check_c = date_validation($("#c_dob").val());
                }
            }
        } else if (this.id == 'offer_subscription')
        {
            if ($("#offer_subscription").is(':checked'))
            {
                $("#offer_subscription-error").hide();
                $("#offer_subscription").removeClass('mage-error');
            } else
            {
                $("#offer_subscription-error").show();
                $("#offer_subscription-error").empty();
                $("#offer_subscription-error").append($.mage.__('This is a required fields.'));
                $("#offer_subscription").addClass('mage-error');
            }
        } else if (this.id == 'terms_condition')
        {
            if ($("#terms_condition").is(':checked'))
            {
                $("#terms_condition-error").hide();
                $("#terms_condition").removeClass('mage-error');
            } else
            {
                $("#terms_condition-error").show();
                $("#terms_condition").addClass('mage-error');
            }
        } else
        {
            $.validator.validateElement($(id));
        }
    });

    $('body').on('blur', '#first-accountnumber', function () {
        if ($("input[name='transfer_type']:checked").val() == 'Domiciliation') {
            if (!$("#first-accountnumber").val()) {
                $("#first-accountnumber-error").show();
                $("#first-accountnumber").addClass('mage-error');
                return false;

            } else {

                $("#first-accountnumber").removeClass('mage-error');
                $("#first-accountnumber-error").hide();
                var accountNumberValuee = $("#first-accountnumber").val();
                var accountNumberValue = accountNumberValuee.toUpperCase();
                var slicevalue = accountNumberValue.slice(0, 2);
                var orangeFrontNumber = ["BE"];
                var finalVlaue = orangeFrontNumber.indexOf(slicevalue);
                if (finalVlaue < 0 || accountNumberValue.length < 19) {
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
    var virtualhideDeliveryValue = "1";
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
            if ($("input[name='design_sim_number-" + firstres[i] + "']:checked").val() == '1') {

                if ($("#design_te_existing_number_final_validation_" + firstres[i]).val() == 1) {
                    showFirstValue = 1;
                }
                if ($("#design_te_existing_number_final_validation_" + firstres[i]).val() == 2) {
                    showSecondValue = 4;
                    if (iewItemsCheck.indexOf(firstres[i]) <= 0) {
                        iewChecking == '2';
                    }
                }
                if ($("#design_te_existing_number_final_validation_" + firstres[i]).val() == 3) {
                    showThirdValue = 2;
                }
                if ($("#design_te_existing_number_final_validation_" + firstres[i]).val() == 0) {
                    showValue = 3;
                }
            } else {
                finalvalue = 4;
            }

            if ($("input[name='design_sim_number-" + firstres[i] + "']:checked").val() == '2') {
                showFirstValue = 1;
                $('#designteexistingnumber_' + firstres[i]).hide();
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
                if (firstres.length > 1 && (showFirstValue == 1 || finalvalue == 4)) {
                    if ($("#continue-delete-button").hasClass("continue-delete-step1-existing")) {
                        $("#continue-delete-button").removeClass('continue-delete-step1-existing');
                    }
                    if (!$("#continue-delete-button").hasClass("continue-delete-step1")) {
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
                    if ($("#continue-delete-button").hasClass("continue-delete-step1")) {
                        $("#continue-delete-button").removeClass('continue-delete-step1');
                    }
                    if (!$("#continue-delete-button").hasClass("continue-delete-step1-existing")) {
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
            if ($("#design_te_existing_number_final_validation_" + firstres[i]).val() == 3 ||
                    $("#design_te_existing_number_final_validation_" + firstres[i]).val() == 1) {
                $(".postpaid_" + firstres[i]).html($("#designteexistingnumber_" + firstres[i]).val().replace(/[\/. ,:-]+/g, ""));
            }

            if ($("#design_te_existing_number_final_validation_" + firstres[i]).val() == 3) {
                virtualhideOtherOperatorValue = "2";
            }

            if ($("#design_te_existing_number_final_validation_" + firstres[i]).val() != 1) {
                virtualhideDeliveryValue = "2";
            }
        }

    }
    /**Ajax to load Transfer Screen */
    $('body').on('click', '#step_1_exisitng, .continue-delete-step1-existing', function () {
        $(".total-shipping-section").show();
        $("html, body").animate({scrollTop: 0}, 600);
        var totalpostpaidValue = $("#totalvirtualproduct").val();
        var res = new Array();
        var cart_url = $("#cart_url").val();
        $.ajax({
            url: $("#quoteexpire_url").val(),
            type: "POST",
            showLoader: true,
            async: false,
            success: function (result) {
                if (result == 0) {
                    window.location.href = cart_url;
                    return false;
                }
            }
        });
        if (totalpostpaidValue.indexOf(',') != -1) {
            var res = totalpostpaidValue.split(",");
        } else {
            res = [totalpostpaidValue];
        }

        $(checkout_form).validation();
        // validate existing number
        var validateresponce = true;
        for (var i = 0; i < res.length; i++) {
            var iewClassNameCheck = $("input[name='design_sim_number-" + res[i] + "']").attr('class');
            if (~iewClassNameCheck.indexOf("iew_sim")) {
                continue;
            }
            var validateDesignsimnumber = $("input[name='design_sim_number-" + res[i] + "']:checked").val();
            if (validateDesignsimnumber == '1') {
                var validate = true;
                if (!$("input[name='design_te_existing_number-" + res[i] + "']").hasClass('valid')) {
                    var validate = $.validator.validateElement($("input[name='design_te_existing_number-" + res[i] + "']"));
                    if (validate == false) {
                        validateresponce = false;
                    }
                }
            }
        }

        if (validateresponce == false) {
            $('.step-mage-error').show();
            $("html, body").animate({scrollTop: 0}, 600);
            return false;
        }

        $('.step-mage-error').hide();

        for (var i = 0; i < res.length; i++) {
            //design_sim_number-2445
            var validateDesignsimnumbervalue = $("input[name='design_sim_number-" + res[i] + "']:checked").val();

            if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 3) {
                $("#display_div_number_" + res[i]).html($("#designteexistingnumber_" + res[i]).val().replace(/[\/. ,:-]+/g, ""));
                $(".postpaid_" + res[i]).html($("#designteexistingnumber_" + res[i]).val().replace(/[\/. ,:-]+/g, ""));
                if ($("#existing_number_" + res[i]).val() != '') {
                    if ($("#existing_number_" + res[i]).val() != $("#designteexistingnumber_" + res[i]).val()) {
                        $('#subscription_type_' + res[i]).prop('selectedIndex', 0);
                        $("input[name=current_operator-" + res[i] + "][value=1]").prop('checked', true);
                        $("#network_customer_number_" + res[i]).val('');
						$("#div_network_customer_number_" + res[i]).removeClass('disable_div');
                        $("#simcard_number_" + res[i]).val('');
                        //$("input[name=bill_in_name-"+res[i]+"][value=1]").prop('checked', true);
                        $("#div_holders_name_" + res[i]).addClass('disable_div');
                        $("#holders_name_" + res[i]).val('');
                        $("#holder_name_" + res[i]).val('');
                    }
                }
                //alert($("#existing_number_"+res[i]).val()+"----"+$("#designteexistingnumber_"+res[i]).val());
                $("#transfernumber_" + res[i]).show();
            } else if (validateDesignsimnumbervalue == "2") {
                $("#transfernumber_" + res[i]).hide();
                $(".postpaid_" + res[i]).html($(".virtual_new_number").html());
            }

            if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 1) {
                $(".postpaid_" + res[i]).html($("#designteexistingnumber_" + res[i]).val().replace(/[\/. ,:-]+/g, ""));
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
        $('#simcard_number').removeAttr("data-validate");
        $("#simcard_number").attr("data-validate", "{'custom-required':true, 'validate-number':true}");
        $("#step1_exiting_num").hide();
        $("#transfer_number").removeClass("disable_div");
        $("#transfer_number").removeClass("disable_div");
    });

    /*Ajax Screen to Load Step2*/
    $('body').on('click', '#step_1,.continue-delete-step1', function () {
        var virtualhideDeliveryValuestep1 = "1";
        var totalpostpaidValue = $("#totalvirtualproduct").val();
        if (totalpostpaidValue.indexOf(',') != -1) {
            var res = totalpostpaidValue.split(",");
        } else {
            res = [totalpostpaidValue];
        }

        var tealium_status = $('input[name="tealium_status"]').val();
        if (tealium_status > 0) {
          var tealium_number_type = get_number_type_value();
          var current_operator = $('#tealium_current_operator').val();
          var num_type = [];
          for (var n in tealium_number_type) {
            num_type.push(tealium_number_type[n]);
          }
          // Personal Details Tab.
          var tealium_data = $('input[name="tealium_values"]').val();
          var d = $.parseJSON(tealium_data);
          d.page_name = "checkout personal details";
          d.checkout_step = "personal_details";
          d.number_type = num_type;
          d.current_operator = current_operator.toLowerCase();
          utag.view(d);
        }

        var hideDeliveryValue = $("#hidefreedelivery").val();
        var cart_url = $("#cart_url").val();
        $.ajax({
            url: $("#quoteexpire_url").val(),
            type: "POST",
            showLoader: true,
            async: false,
            success: function (result) {
                if (result == 0) {
                    window.location.href = cart_url;
                    return false;
                }
            }
        });
        for (var i = 0; i < res.length; i++) {
            if ($("#design_te_existing_number_final_validation_" + res[i]).val() != 1) {
                $('.virtualproductonepage_one').val(0);
            }
            if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 3 || $("#design_te_existing_number_final_validation_" + res[i]).val() == 1) {
                $("#display_div_number_" + res[i]).html($("#designteexistingnumber_" + res[i]).val().replace(/[\/. ,:-]+/g, ""));
                $(".postpaid_" + res[i]).html($("#designteexistingnumber_" + res[i]).val().replace(/[\/. ,:-]+/g, ""));
                $("#transfernumber_" + res[i]).show();
            } else {
                $(".postpaid_" + res[i]).html($.mage.__('nouveau numéro'));
            }
            if ($("#design_te_existing_number_final_validation_" + res[i]).val() != 1) {
                virtualhideDeliveryValuestep1 = "2";
            }
        }
        if (virtualhideDeliveryValuestep1 == "1" && $('.virtualproductonepage').val() == 1) {
            $(".total-shipping-section").hide();
        }

        $("html, body").animate({scrollTop: 0}, 600);
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
    function number_validation(numbervalue, deId)
    {
        var design_te_existing_number_chk = numbervalue;
        var totalpostpaidValue = $("#totalvirtualproduct").val();
        var res = new Array();
        if (design_te_existing_number_chk.charAt(0) == 0) {
            var stringNewValue = design_te_existing_number_chk.substr(1);
            design_te_existing_number_chk = 32 + stringNewValue;
        }
        if (totalpostpaidValue.indexOf(',') != -1) {
            var res = totalpostpaidValue.split(",");
        } else {
            res = [totalpostpaidValue];
        }
        var number_url = $("#number_url").val();
        $("#design_te_existing_number_final_validation_" + deId).val('0');
        //showDivButtons
        if (!numbervalue) {
            showDivButtons(3);
            return false;
        }

        $.ajax({
            url: number_url,
            data: {design_te_existing_number_chk: design_te_existing_number_chk},
            type: 'get',
            dataType: 'json',
            showLoader: true,
            context: $('#step_1')
        }).done(function (data) {
            $("#design_te_existing_number_final_validation_" + deId).val(data)
            $('#subs_result').val(data);
            if (data == 1)
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
                    if ($("input[name='design_sim_number-" + res[i] + "']:checked").val() == '1') {

                        if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 1) {
                            showFirstValue = 1;
                        }
                        if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 2) {
                            showSecondValue = 4;
                        }
                        if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 3) {
                            showThirdValue = 2;
                        }
                        if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 0) {
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
                            if ($("#continue-delete-button").hasClass("continue-delete-step1-existing")) {
                                $("#continue-delete-button").removeClass('continue-delete-step1-existing');
                            }
                            if (!$("#continue-delete-button").hasClass("continue-delete-step1")) {
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
                            if ($("#continue-delete-button").hasClass("continue-delete-step1")) {
                                $("#continue-delete-button").removeClass('continue-delete-step1');
                            }
                            if (!$("#continue-delete-button").hasClass("continue-delete-step1-existing")) {
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
                            if ($("#continue-delete-button").hasClass("continue-delete-step1-existing")) {
                                $("#continue-delete-button").removeClass('continue-delete-step1-existing');
                            }
                            if (!$("#continue-delete-button").hasClass("continue-delete-step1")) {
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

            } else if (data == 2)
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
                    if ($("input[name='design_sim_number-" + res[i] + "']:checked").val() == '1') {

                        if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 1) {
                            showFirstValue = 1;
                        }
                        if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 2) {
                            showSecondValue = 4;
                            if (iewItemsCheck.indexOf(res[i]) <= 0) {
                                iewChecking == '2';
                            }
                        }
                        if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 3) {
                            showThirdValue = 2;
                        }
                        if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 0) {
                            showValue = 3;
                        }
                    } else {
                        finalvalue = 4;
                    }
                }
                if (showValue == 3) {
                    showDivButtons(showValue);
                    /* @Defect 5738
                     * When ordering 2 or more postpaid tariff plans, if customer enters an Orange postpaid number for the first postpaid
                     * TP in the number config step, the error message redirecting him to the CZ does not appear.
                     * @Solution - QC-5738
                     * @Author - Mohan YM
                     * @Date - 28/11/2017
                     */
                    if (res.length > 1 && showSecondValue == 4) {
                        $("#step1_continue_div_exiting").addClass("disable_div");
                        $("#existing_number_postpaid_div").show();
                    }
                    return false;
                } else if (showSecondValue == 4) {
                    showDivButtons(showSecondValue);
                    if (showSecondValue == 4 && showThirdValue != 2) {
                        showDivButtons(showSecondValue);
                        if (res.length > 1 && (showFirstValue == 1 || finalvalue == 4)) {
                            if ($("#continue-delete-button").hasClass("continue-delete-step1-existing")) {
                                $("#continue-delete-button").removeClass('continue-delete-step1-existing');
                            }
                            if (!$("#continue-delete-button").hasClass("continue-delete-step1")) {
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
                            if ($("#continue-delete-button").hasClass("continue-delete-step1")) {
                                $("#continue-delete-button").removeClass('continue-delete-step1');
                            }
                            if (!$("#continue-delete-button").hasClass("continue-delete-step1-existing")) {
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

            } else
            {
                var showValue = 2;
                var showFirstValue = '';
                var showSecondValue = '';
                var showThirdValue = '';
                for (var i = 0; i < res.length; i++) {
                    //design_sim_number-2445
                    if ($("input[name='design_sim_number-" + res[i] + "']:checked").val() == '1') {

                        if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 1) {
                            showFirstValue = 1;
                        }
                        if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 2) {
                            showSecondValue = 4;
                        }
                        if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 3) {
                            showThirdValue = 2;
                        }
                        if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 0) {
                            showValue = 3;
                        }
                    } else {
                        if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 0) {
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
        var shippingInformation = $('form#checkout_form').serializeArray();

        var totalpostpaidValue = $("#totalvirtualproduct").val();
        var res = new Array();
        if (totalpostpaidValue.indexOf(',') != -1) {
            var res = totalpostpaidValue.split(",");
        } else {
            res = [totalpostpaidValue];
        }
        var deleteCartItem = new Array();
        for (var i = 0; i < res.length; i++) {
            if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 2) {
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
            data: {saveData: shippingInformation, aStep: ajaxStep, deletecartJoin: deleteCartJoinItem, checkstepstat: checkstepstat},
            type: 'post',
            dataType: 'json',
            showLoader: true,
            context: $('#step_1')
        }).done(function (data) {
            if (deleteCartJoinItem) {
                window.location.reload();
                return false;
            }
            $('#step1_number').addClass("disable_div");
            $("#step1_tab").removeClass("first active");
            $("#step1_tab").addClass("first done");
            $("#step2_tab").addClass("active");
            $('#step').val("step3");
            $('#step2_information').removeClass("disable_div");
            $(".iwd-opc-shipping-method").show();
            var nationalityCheck = $("input[name='nationality']:checked").val();
            //var nationalityCheckreg = $("input[name='registered']:checked").val();
            if (nationalityCheck == "belgian" || nationalityCheck == '') {
                $(".shippingaddressbottom").removeClass("disable_div");
            }
            $(".cartitemvalueonepage").hide();
            $(".cartcouponform").hide();
            $('.coupon-message').addClass('disable_div');

            $(".cartitemvalueonepageside").hide();
            $(".iwd-discount-options").hide();
            $('.step-mage-error').hide();
            $("html, body").animate({scrollTop: 0}, 600);
        });
    }

    function final_ajax_call()
    {
        var ajaxStep = "step1";
        var checkstepstat = "";
        var shippingInformation = $('form#checkout_form').serializeArray();
        var session_url = $("#session_url").val();
        shippingInformation = JSON.stringify(shippingInformation);
        $.ajax({
            url: session_url,
            data: {removeFinalStep: "removefinal", checkstepstat: checkstepstat},
            type: 'post',
            dataType: 'json',
            showLoader: false,
            context: $('#step_1')
        }).done(function (data) {
        });
    }
//IEW Update
    $('body').on('click', '.iew_sim', function () {
        var itemId = $(this).attr('data-itemid');
        /*$("#step1_continue_div").hide();
         $("#step1_continue_div_exiting").addClass("disable_div");
         $("#step1_continue_div").addClass("disable_div");
         $("#existing_number_postpaid_div").show(); */
        $("#design_te_existing_number_final_validation_" + itemId).val("2");
        var totalpostpaidValue = $("#totalvirtualproduct").val();
        var res = new Array();
        if (totalpostpaidValue.indexOf(',') != -1) {
            var res = totalpostpaidValue.split(",");
        } else {
            res = [totalpostpaidValue];
        }

        var data = 2;
        if (data == "2")
        {
            var showValue = 4;
            var showFirstValue = '';
            var showSecondValue = '';
            var showThirdValue = '';
            var showfirstValue = '';

            for (var i = 0; i < res.length; i++) {
                //design_sim_number-2445
                if ($("input[name='design_sim_number-" + res[i] + "']:checked").val() == '1') {

                    if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 1) {
                        showFirstValue = 1;
                    }
                    if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 2) {
                        showSecondValue = 4;
                    }
                    if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 3) {
                        showThirdValue = 2;
                    }
                    if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 0) {
                        showfirstValue = 3;
                    }
                } else {
                    if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 0) {
                        showfirstValue = 3;
                    }
                }
            }
            if (showFirstValue != 1 && showSecondValue == 4 && showThirdValue != 2 && showfirstValue != 3) {
                showDivButtons(showSecondValue);
                $("#customer-zone-button").show();
                $("#customer-zone-des").show();
                $("#continue-delete-button-top").hide();
                $("#continue-delete-button").hide();
            } else if (showValue == 3) {
                showDivButtons(showValue)
            } else if (showThirdValue == 2) {
                showDivButtons(showThirdValue);
                if (showSecondValue == 4) {
                    showDivButtons(showSecondValue);
                    if (res.length > 1) {
                        if ($("#continue-delete-button").hasClass("continue-delete-step1")) {
                            $("#continue-delete-button").removeClass('continue-delete-step1');
                        }
                        if (!$("#continue-delete-button").hasClass("continue-delete-step1-existing")) {
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
                    if ($("#continue-delete-button").hasClass("continue-delete-step1-existing")) {
                        $("#continue-delete-button").removeClass('continue-delete-step1-existing');
                    }
                    if (!$("#continue-delete-button").hasClass("continue-delete-step1")) {
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
    $('body').on('click', '.iew_sim_new', function () {
        //$("#step1_continue_div").addClass("disable_div"); 				
        //$("#existing_number_postpaid_div").hide(); 
    });
    if ($('input.iew_sim:radio:checked').length > 0) {

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
            if ($("input[name='design_sim_number-" + res[i] + "']:checked").val() == '1') {

                if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 1) {
                    showFirstValue = 1;
                }
                if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 2) {
                    showSecondValue = 4;
                }
                if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 3) {
                    showThirdValue = 2;
                }
                if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 0) {
                    showfirstValue = 3;
                }
            } else {
                if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 0) {
                    showfirstValue = 3;
                }
            }
        }

        var totalVirualProduct = $('#totalvirtualproduct').val();
        var totaliewProduct = $("input:hidden[name='iew_items']").val();
        if ($('#virtualproductonepage').val() == 1 || totalVirualProduct == totaliewProduct) {
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
        if ($('#iewbundle').val() == 1) {
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
            $('#step1_continue_div').removeClass("disable_div");
            $('#step1_continue_div_exiting').removeClass("disable_div");
            $('#step1_continue_div_exiting').addClass("disable_div");
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
    $("#step_1_number").click(function () {
        $("html, body").animate({scrollTop: 0}, 600);
        $(checkout_form).validation();
        var totalpostpaidValue = $("#totalvirtualproduct").val();
        var simcardAdditional = new Array();
        var res;

        if (totalpostpaidValue.indexOf(',') != -1) {
            var res = totalpostpaidValue.split(",");
        } else {
            res = [totalpostpaidValue];
        }
        var tealium_status = $('input[name="tealium_status"]').val();
        if (tealium_status > 0) {
          var tealium_number_type = get_number_type_value();
          var current_operator = $('#tealium_current_operator').val();
          var num_type = [];
          for (var n in tealium_number_type) {
            num_type.push(tealium_number_type[n]);
          }
          // Personal Details Tab.
          var tealium_data = $('input[name="tealium_values"]').val();
          var d = $.parseJSON(tealium_data);
          d.page_name = "checkout personal details";
          d.checkout_step = "personal_details";
          d.number_type = num_type;
          d.current_operator = current_operator.toLowerCase();
          utag.view(d);
        }

        var simcard_number = new Array();
        var network_customer_number = new Array();
        var subscription_type = new Array();
        var holders_name = new Array();
        var holder_name = new Array();
        for (var i = 0; i < res.length; i++) {
            if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 3) {
                $("#simcard_number-" + res[i] + "-error").show();
                $("#simcard_number-" + res[i] + "-error").empty();
                var card_vale = $("#simcard_number_" + res[i]).val();
                var attrtocheck = $('#subscription_type_' + res[i]).val();
                if (card_vale != "") {
                    var length1 = card_vale.length;
                    if (length1 > 12) {
                        if (attrtocheck == 'Proximus') {
                            var result_prox_main = simcard_proxi_main(card_vale);
                            if (result_prox_main == false) {
                                //console.log('NOT VALID'+"#simcard_number_"+res[i]+"-error");
                                $("#simcard_number-" + res[i] + "-error").show();
                                $("#simcard_number-" + res[i] + "-error").empty();
                                $("#simcard_number_" + res[i]).removeClass('form-control validation_check  valid simcard_number');
                                $("#simcard_number_" + res[i]).addClass('form-control simcard_number mage-error');
                                $("#simcard_number-" + res[i] + "-error").html($.mage.__('Current SIM card number and operator not matched.'));
                                return false;
                            } else {
                                $("#simcard_number-" + res[i] + "-error").hide();
                                $("#simcard_number-" + res[i] + "-error").empty();
                            }
                        } else if (attrtocheck == 'Other' || attrtocheck == 'Autre' || attrtocheck == 'Andere') {
                            var result_others_main = simcard_others_main(card_vale);
                            if (!result_others_main) {
                                $("#simcard_number-" + res[i] + "-error").show();
                                $("#simcard_number-" + res[i] + "-error").empty();
                                $("#simcard_number_" + res[i]).removeClass('form-control validation_check simcard_number');
                                $("#simcard_number_" + res[i]).addClass('form-control simcard_number mage-error');
                                $("#simcard_number-" + res[i] + "-error").html($.mage.__('Current SIM card number and operator not matched.'));
                                return false;
                            } else {
                                $("#simcard_number-" + res[i] + "-error").hide();
                                $("#simcard_number-" + res[i] + "-error").empty();
                            }


                        } else if (attrtocheck == 'Base') {
                            var result_base_main = simcard_base_main(card_vale);
                            if (!result_base_main) {
                                $("#simcard_number-" + res[i] + "-error").show();
                                $("#simcard_number-" + res[i] + "-error").empty();
                                $("#simcard_number_" + res[i]).removeClass('form-control validation_check simcard_number');
                                $("#simcard_number_" + res[i]).addClass('form-control simcard_number mage-error');
                                $("#simcard_number-" + res[i] + "-error").html($.mage.__('Current SIM card number and operator not matched.'));
                                return false;
                            } else {
                                $("#simcard_number-" + res[i] + "-error").hide();
                                $("#simcard_number-" + res[i] + "-error").empty();
                            }
                        } else if (attrtocheck == 'Telenet') {
                            var result_telnet_main = simcar_telnet_main(card_vale);
                            if (!result_telnet_main) {
                                $("#simcard_number-" + res[i] + "-error").show();
                                $("#simcard_number-" + res[i] + "-error").empty();
                                $("#simcard_number_" + res[i]).removeClass('form-control validation_check simcard_number');
                                $("#simcard_number_" + res[i]).addClass('form-control simcard_number mage-error');
                                $("#simcard_number-" + res[i] + "-error").html($.mage.__('Current SIM card number and operator not matched.'));
                                return false;
                            } else {
                                $("#simcard_number-" + res[i] + "-error").hide();
                                $("#simcard_number-" + res[i] + "-error").empty();
                            }
                        }
                    }
                }
            }

        }


        for (var i = 0; i < res.length; i++) {
            if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 3) {
                if (!$("#simcard_number_" + res[i]).hasClass('mage-error')) {
                    $("#simcard_number_" + res[i] + "-error").html('');
                }
            }
        }
        for (var i = 0; i < res.length; i++) {
            //design_sim_number-2445
            if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 3) {

                if ($("#simcard_number_" + res[i] + "-error").html()) {

                    simcardAdditional[res[i]] = false;
                } else {
                    simcardAdditional[res[i]] = true;
                }
                //input[name='design_sim_number-"+res[i]+"']:checked
                var arrayid = res[i];
                simcard_number[arrayid] = $.validator.validateElement($("#simcard_number_" + res[i]));
                var val_bill_in_name = $("input[name='bill_in_name-" + res[i] + "']:checked").val();
                var val_current_operator = $("input[name='current_operator-" + res[i] + "']:checked").val();
                if (val_current_operator == 1) {
                    network_customer_number[res[i]] = $.validator.validateElement($("#network_customer_number_" + res[i]));
                } else {
                    network_customer_number[res[i]] = true;
                }

                subscription_type[res[i]] = $.validator.validateElement($("#subscription_type_" + res[i]));

                if (val_bill_in_name == '1') {
                    holders_name[res[i]] = true;
                    holder_name[res[i]] = true;
                } else {
                    holders_name[res[i]] = $.validator.validateElement($("#holders_name_" + res[i]));
                    holder_name[res[i]] = $.validator.validateElement($("#holder_name_" + res[i]));
                }

            }
        }
        for (var i = 0; i < res.length; i++) {
            if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 3) {

                if (network_customer_number[res[i]] == false || subscription_type[res[i]] == false ||
                        simcard_number[res[i]] == false || holder_name[res[i]] == false ||
                        simcardAdditional[res[i]] == false) {
                    $("#subscription_div_a").removeClass("btn btn-default dropdown-toggle mage-error");
                    $("#subscription_div_a").addClass("btn btn-default dropdown-toggle");
                    $("#subscription_div_a_error").hide();
                    $('.step-mage-error').show();
                    return false;
                }
            }
        }
        var cart_url = $("#cart_url").val();
        $.ajax({
            url: $("#quoteexpire_url").val(),
            type: "POST",
            showLoader: true,
            async: false,
            success: function (result) {
                if (result == 0) {
                    window.location.href = cart_url;
                    return false;
                }
            }
        });
        $('.step-mage-error').hide();
        new_number();
        window.location.hash = "details";
    });

//////append all custom values to original magento field values///////
    $("input:text[name='first_name']").on('change', function () {
        $("input:text[name='firstname']").val($("input:text[name='first_name']").val());
        $("input[name='firstname']").keyup();
    });
    $("input:text[name='last_name']").on('change', function () {
        $("input:text[name='lastname']").val($("input:text[name='last_name']").val());
        $("input[name='lastname']").keyup();
    });
    $("input:text[name='email']").on('change', function () {
        $("#customer-email").val($("input:text[name='email']").val());
        $("#customer-email").change();
    });
    $("#subscription_type").on('change', function () {
        $("input:text[name='custom_attributes[current_operator]']").val($("#subscription_type option:selected").val());
        $("input:text[name='custom_attributes[current_operator]']").keyup();
    });

    $(".current_operator").on('click', function () {

        $('.step-mage-error').hide();
        var str = this.id;
        var res = str.split("_");
        var quote_itemid = res[1] + "_" + res[2];
        //console.log('#network_customer_number_'+quote_itemid+'-error');
        //console.log('#network_customer_number_'+quote_itemid+'-error');
        $('#network_customer_number_' + quote_itemid + '-error').empty();
        $('#simcard_number_' + quote_itemid + '-error').empty()

        if (this.value == '1')
        {
            var valn1 = $('#network_customer_number_' + quote_itemid).val();
            var valn2 = $('#simcard_number_' + quote_itemid).val();
            if (valn1 == "") {
                $('#network_customer_number_' + quote_itemid).removeClass("mage-error");
                $('#network_customer_number_' + quote_itemid + '-error').empty();
            }
            if (valn2 == "") {
                $('#simcard_number_' + quote_itemid).removeClass("mage-error");
                $('#simcard_number_' + quote_itemid + '-error').empty()
            }

            $('#network_customer_number_' + quote_itemid).removeAttr("data-validate");
            $("#network_customer_number_" + quote_itemid).attr("data-validate", "{'custom-required':true, 'min-lenth-validation-newmessage':true}");
            $("#div_network_customer_number_" + quote_itemid).removeClass("disable_div");
            $("#div_bill_in_name_" + quote_itemid).removeClass("disable_div");
            $("#div_holders_name_" + quote_itemid).removeClass("disable_div");
            $("#div_holders_name_" + quote_itemid).addClass("disable_div");
            //$("input:radio[name=bill_in_name-"+quote_itemid+"'").filter('[value=1]').prop('checked', true);
            //$("#bill_"+quote_itemid).prop("checked", true);
            $("#network_customer_number_" + quote_itemid).removeClass("mage-error");
            //$("#network_customer_number-error" ).hide();
            $('#holders_name_' + quote_itemid).val("");
            $('#holder_name_' + quote_itemid).val("");
            $("#holders_name_" + quote_itemid).removeClass("mage-error");
            //$("#holders_name-error" ).hide();
            $("#holder_name_" + quote_itemid).removeClass("mage-error");
            //$("#holder_name-error" ).hide();
            if ($("input:radio[name='bill_in_name-" + quote_itemid + "']:radio:checked").val() == '1')
            {
                $("#div_holders_name_" + quote_itemid).addClass("disable_div");
                //$("input:checkbox[name='custom_attributes[bill_in_name]']").prop('checked', true);
                $('#holders_name_' + quote_itemid).removeAttr("data-validate");
                $('#holder_name_' + quote_itemid).removeAttr("data-validate");
                $('#holders_name_' + quote_itemid).val("");
                $('#holder_name_' + quote_itemid).val("");
                $('#holders_name_' + quote_itemid).removeClass("mage-error");
                $('#holders_name-error_' + quote_itemid).hide();
                $('#holder_name_' + quote_itemid).removeClass("mage-error");
                $('#holders_name_' + quote_itemid + '-error').hide();
                $('#holder_name_' + quote_itemid + '-error').hide();
                $('#holder_name-error').hide();
            } else
            {
                $("#div_holders_name_" + quote_itemid).removeClass("disable_div");
                $("#holders_name_" + quote_itemid).removeAttr("data-validate");
                $("#holders_name_" + quote_itemid).attr("data-validate", "{'custom-required':true,'validate-novalidation':true,'min-lenth-validation':true,'validate-slash':true,'holders-name':true}");
                $("#holder_name_" + quote_itemid).removeAttr("data-validate");
                $("#holder_name_" + quote_itemid).attr("data-validate", "{'custom-required':true,'validate-novalidation':true,'min-lenth-validation':true,'validate-slash':true,'holders-name':true}");
                //$("input:checkbox[name='custom_attributes[bill_in_name]']").prop('checked', false);
                // $("input:text[name='custom_attributes[bill_in_name]']").keyup();
            }


        } else
        {
            if (this.value == '2') {
                $('#network_customer_number_' + quote_itemid).removeAttr("data-validate");
                $("#network_customer_number_" + quote_itemid).attr("data-validate", "{'custom-required':true, 'min-lenth-validation-newmessage':true}");
                $(".sim_custom_number").removeAttr("data-validate");
                //$(".sim_custom_number").attr("data-validate", "{'custom-required':true, 'min-lenth-validation-newmessage':true}");
            }
            var valn1 = $('#network_customer_number_' + quote_itemid).val();
            var valn2 = $('#simcard_number_' + quote_itemid).val();
            if (valn1 == "") {
                $('#network_customer_number_' + quote_itemid).removeClass("mage-error");
                $('#network_customer_number_' + quote_itemid + '-error').empty();
            }
            if (valn2 == "") {
                $('#simcard_number_' + quote_itemid).removeClass("mage-error");
                $('#simcard_number_' + quote_itemid + '-error').empty()
            }
            $('#network_customer_number_' + quote_itemid).removeClass("mage-error");
            $('#simcard_number_' + quote_itemid).removeClass("mage-error");
            //$("#bill_"+quote_itemid).prop("checked", true);
            // $('input:radio[name=bill_in_name]').filter('[value=1]').prop('checked', true);
            $('#network_customer_number_' + quote_itemid).val("");
            $('#holders_name_' + quote_itemid).val("");
            $('#holder_name_' + quote_itemid).val("");
            $("#div_network_customer_number_" + quote_itemid).addClass("disable_div");
            $("#div_bill_in_name_" + quote_itemid).addClass("disable_div");
            $("#div_holders_name_" + quote_itemid).addClass("disable_div");
            $("input:radio[name='bill_in_name-" + quote_itemid + "']").filter('[value=1]').prop('checked', true);
        }
    });
    $(".bill_in_name").on('click', function () {
        var str = this.id;
        var res = str.split("_");
        var quote_itemid = res[1] + "_" + res[2];
        if (this.value == '1')
        {

            $("#div_holders_name_" + quote_itemid).addClass("disable_div");
            //$("input:checkbox[name='custom_attributes[bill_in_name]']").prop('checked', true);
            $('#holders_name_' + quote_itemid).removeAttr("data-validate");
            $('#holder_name_' + quote_itemid).removeAttr("data-validate");
            $('#holders_name_' + quote_itemid).val("");
            $('#holder_name_' + quote_itemid).val("");
            $('#holders_name_' + quote_itemid).removeClass("mage-error");
            $('#holders_name-error_' + quote_itemid).hide();
            $('#holder_name_' + quote_itemid).removeClass("mage-error");
            $('#holders_name_' + quote_itemid + '-error').hide();
            $('#holder_name_' + quote_itemid + '-error').hide();
            $('#holder_name-error').hide();
        } else
        {
            $("#div_holders_name_" + quote_itemid).removeClass("disable_div");
            $("#holders_name_" + quote_itemid).removeAttr("data-validate");
            $("#holders_name_" + quote_itemid).attr("data-validate", "{'custom-required':true,'validate-novalidation':true,'min-lenth-validation':true,'validate-slash':true,'holders-name':true}");
            $("#holder_name_" + quote_itemid).removeAttr("data-validate");
            $("#holder_name_" + quote_itemid).attr("data-validate", "{'custom-required':true,'validate-novalidation':true,'min-lenth-validation':true,'validate-slash':true,'holders-name':true}");
            //$("input:checkbox[name='custom_attributes[bill_in_name]']").prop('checked', false);
            // $("input:text[name='custom_attributes[bill_in_name]']").keyup();
        }
    });

    $("input:text[name='network_customer_number']").on('change', function () {
        $("input:text[name='custom_attributes[network_customer_number]']").val($("input:text[name='network_customer_number']").val());
        $("input:text[name='custom_attributes[network_customer_number]']").keyup();
    });
    $("input:text[name='simcard_number']").on('change', function () {
        $("input:text[name='custom_attributes[simcard_number]']").val($("input:text[name='simcard_number']").val());
        $("input:text[name='custom_attributes[simcard_number]']").keyup();
    });
    $("input:text[name='holders_name']").on('change', function () {
        $("input:text[name='custom_attributes[holders_name]']").val($("input:text[name='holders_name']").val());
        $("input:text[name='custom_attributes[holders_name]']").keyup();
    });
    $("input:text[name='holder_name']").on('change', function () {
        $("input:text[name='custom_attributes[holder_name]']").val($("input:text[name='holder_name']").val());
        $("input:text[name='custom_attributes[holder_name]']").keyup();
    });
    $("input:radio[name='ex_invoice']").on('change', function () {

        if (this.value == 'yes')
        {
            $("input:checkbox[name='custom_attributes[ex_invoice]']").prop('checked', true);
            //$('input:radio[name=cu_ex_invoice_bill_in_name]').filter('[value=1]').prop('checked', true);
            $("#div_ex_invoice_cust_number").removeClass("disable_div");
            $("#div_ex_invoice_bill_in_name").removeClass("disable_div");
            //$("#div_ex_invoice_cust_details").removeClass("disable_div");	
            //$("#div_ex_invoice_cust_details").addClass("disable_div");	
            $('#cu_ex_invoice_cust_number').removeAttr("data-validate");
            $("#cu_ex_invoice_cust_number").attr("data-validate", "{'custom-required':true,'min-lenth-validation-newmessage':true}");
            $('#cu_ex_invoice_cust_dob').val("");
            $('#cu_ex_invoice_cust_firstname').val("");
            $('#cu_ex_invoice_cust_surname').val("");
            if ($("input:radio[name='cu_ex_invoice_bill_in_name']:radio:checked").val() == '1')
            {
                $("#div_ex_invoice_cust_details").addClass("disable_div");
                $("input:checkbox[name='custom_attributes[ex_invoice_bill_in_name]']").prop('checked', true);
                $('#cu_ex_invoice_cust_surname').removeAttr("data-validate");
                $('#cu_ex_invoice_cust_firstname').removeAttr("data-validate");
                $('#cu_ex_invoice_cust_dob').removeAttr("data-validate");
                $('#cu_ex_invoice_cust_dob').val("");
                $('#cu_ex_invoice_cust_firstname').val("");
                $('#cu_ex_invoice_cust_surname').val("");
                $("#cu_ex_invoice_cust_surname").removeClass("mage-error");
                $("#cu_ex_invoice_cust_surname-error").hide();
                $("#cu_ex_invoice_cust_firstname").removeClass("mage-error");
                $("#cu_ex_invoice_cust_firstname-error").hide();
                $("#cu_ex_invoice_cust_dob").removeClass("mage-error");
                $("#cu_ex_invoice_cust_dob-error").hide();

            } else
            {
                $('#cu_ex_invoice_cust_surname').removeAttr("data-validate");
                $("#cu_ex_invoice_cust_surname").attr("data-validate", "{'custom-required':true, 'validate-novalidation':true,'min-lenth-validation':true', 'validate-slash':true,'cu_ex_invoice_cust_surname':true}");
                $('#cu_ex_invoice_cust_firstname').removeAttr("data-validate");
                $("#cu_ex_invoice_cust_firstname").attr("data-validate", "{'custom-required':true, 'validate-novalidation':true,'min-lenth-validation':true, 'validate-slash':true,'cu_ex_invoice_cust_firstname':true}");
                $('#cu_ex_invoice_cust_dob').removeAttr("data-validate");
                $("#cu_ex_invoice_cust_dob").attr("data-validate", "{'custom-required':true,'validate-dob-date':true, 'validate-dob-month':true,'dob-date-validation':true}");
                $("#div_ex_invoice_cust_details").removeClass("disable_div");
                $("input:checkbox[name='custom_attributes[ex_invoice_bill_in_name]']").prop('checked', false);
            }
        } else
        {
            $("#cust_number_validation_error").hide();
            $("input:checkbox[name='custom_attributes[ex_invoice]']").prop('checked', false);
            //$('input:radio[name=cu_ex_invoice_bill_in_name]').filter('[value=1]').prop('checked', true);
            $("input:text[name='custom_attributes[ex_invoice]']").keyup();
            $("#div_ex_invoice_cust_number").addClass("disable_div");
            $("#div_ex_invoice_bill_in_name").addClass("disable_div");
            $("#div_ex_invoice_cust_details").removeClass("disable_div");
            $("#div_ex_invoice_cust_details").addClass("disable_div");
            $('#cu_ex_invoice_cust_number').removeAttr("data-validate");
            $('#cu_ex_invoice_cust_number').val("");
            $("#cu_ex_invoice_cust_number").removeClass("mage-error");
            $("#cu_ex_invoice_cust_number-error").hide();
            $('#cu_ex_invoice_cust_dob').val("");
            $("#cu_ex_invoice_cust_dob").removeClass("mage-error");
            $("#cu_ex_invoice_cust_dob-error").hide();
            $('#cu_ex_invoice_cust_firstname').val("");
            $("#cu_ex_invoice_cust_firstname").removeClass("mage-error");
            $("#cu_ex_invoice_cust_firstname-error").hide();
            $('#cu_ex_invoice_cust_surname').val("");
            $("#cu_ex_invoice_cust_surname").removeClass("mage-error");
            $("#cu_ex_invoice_cust_surname-error").hide();

        }
        $("input:text[name='custom_attributes[ex_invoice]']").keyup();
    });
    $("#cu_ex_invoice_cust_number").on('change', function () {
        $("input:text[name='custom_attributes[ex_invoice_cust_number]']").val($("#cu_ex_invoice_cust_number").val());
        $("input:text[name='custom_attributes[ex_invoice_cust_number]']").keyup();
    });
    $("input:radio[name='cu_ex_invoice_bill_in_name']").on('change', function () {
        if (this.value == '1')
        {
            $("#div_ex_invoice_cust_details").addClass("disable_div");
            $("input:checkbox[name='custom_attributes[ex_invoice_bill_in_name]']").prop('checked', true);
            $('#cu_ex_invoice_cust_surname').removeAttr("data-validate");
            $('#cu_ex_invoice_cust_firstname').removeAttr("data-validate");
            $('#cu_ex_invoice_cust_dob').removeAttr("data-validate");
            $('#cu_ex_invoice_cust_dob').val("");
            $('#cu_ex_invoice_cust_firstname').val("");
            $('#cu_ex_invoice_cust_surname').val("");
            $("#cu_ex_invoice_cust_surname").removeClass("mage-error");
            $("#cu_ex_invoice_cust_surname-error").hide();
            $("#cu_ex_invoice_cust_firstname").removeClass("mage-error");
            $("#cu_ex_invoice_cust_firstname-error").hide();
            $("#cu_ex_invoice_cust_dob").removeClass("mage-error");
            $("#cu_ex_invoice_cust_dob-error").hide();

        } else
        {
            $('#cu_ex_invoice_cust_surname').removeAttr("data-validate");
            $("#cu_ex_invoice_cust_surname").attr("data-validate", "{'custom-required':true, 'validate-novalidation':true,'min-lenth-validation':true, 'validate-slash':true,'cu_ex_invoice_cust_surname':true}");
            $('#cu_ex_invoice_cust_firstname').removeAttr("data-validate");
            $("#cu_ex_invoice_cust_firstname").attr("data-validate", "{'custom-required':true, 'validate-novalidation':true,'min-lenth-validation':true, 'validate-slash':true,'cu_ex_invoice_cust_firstname':true}");
            $('#cu_ex_invoice_cust_dob').removeAttr("data-validate");
            $("#cu_ex_invoice_cust_dob").attr("data-validate", "{'custom-required':true,'validate-dob-date':true, 'validate-dob-month':true,'dob-date-validation':true}");
            $("#div_ex_invoice_cust_details").removeClass("disable_div");
            $("input:checkbox[name='custom_attributes[ex_invoice_bill_in_name]']").prop('checked', false);
        }
        $("input:text[name='custom_attributes[ex_invoice_bill_in_name]']").keyup();
    });
    $("#cu_ex_invoice_cust_surname").on('change', function () {
        $("input:text[name='custom_attributes[ex_invoice_cust_surname]']").val($("#cu_ex_invoice_cust_surname").val());
        $("input:text[name='custom_attributes[ex_invoice_cust_surname]']").keyup();
    });

    $("#cu_ex_invoice_cust_firstname").on('change', function () {
        $("input:text[name='custom_attributes[ex_invoice_cust_firstname]']").val($("#cu_ex_invoice_cust_firstname").val());
        $("input:text[name='custom_attributes[ex_invoice_cust_firstname]']").keyup();
    });

    $("#cu_ex_invoice_cust_dob").on('change', function () {
        $("input:text[name='custom_attributes[ex_invoice_cust_dob]']").val($("#cu_ex_invoice_cust_dob").val());
        $("input:text[name='custom_attributes[ex_invoice_cust_dob]']").keyup();
    });
    $("input:radio[name='discount_f']").on('change', function () {
        if (this.value == 'yes')
        {
            $("input:checkbox[name='custom_attributes[discount_f]']").prop('checked', true);
            $("input:text[name='custom_attributes[discount_f]']").keyup();
            $('input:radio[name=cu_discount_f_bill_in_name]').filter('[value=1]').prop('checked', true);
            $("#div_discount_cust_number").removeClass("disable_div");
            $("#div_discount_bill_in_name").removeClass("disable_div");
            $("#div_discount_cust_details").removeClass("disable_div");
            $("#div_discount_cust_details").addClass("disable_div");
            $('#cu_discount_f_cust_number').removeAttr("data-validate");
            $("#cu_discount_f_cust_number").attr("data-validate", "{'custom-required':true, 'validate-number':true}");
            $('#cu_discount_f_cust_surname').val("");
            $('#cu_discount_f_cust_firstname').val("");
            $('#cu_discount_f_cust_dob').val("");
        } else
        {
            $("input:checkbox[name='custom_attributes[discount_f]']").prop('checked', false);
            $("input:text[name='custom_attributes[discount_f]']").keyup();
            $('input:radio[name=cu_discount_f_bill_in_name]').filter('[value=1]').prop('checked', true);
            $("#div_discount_cust_number").addClass("disable_div");
            $("#div_discount_bill_in_name").addClass("disable_div");
            $("#div_discount_cust_details").removeClass("disable_div");
            $("#div_discount_cust_details").addClass("disable_div");
            $('#cu_discount_f_cust_number').removeAttr("data-validate");
            $('#cu_discount_f_cust_number').val("");
            $("#cu_discount_f_cust_number").removeClass("mage-error");
            $("#cu_discount_f_cust_number-error").hide();
            $('#cu_ex_invoice_cust_dob').val("");
            $("#cu_ex_invoice_cust_dob").removeClass("mage-error");
            $("#cu_ex_invoice_cust_dob-error").hide();
            $('#cu_discount_f_cust_firstname').val("");
            $("#cu_discount_f_cust_firstname").removeClass("mage-error");
            $("#cu_discount_f_cust_firstname-error").hide();
            $('#cu_discount_f_cust_surname').val("");
            $("#cu_discount_f_cust_surname").removeClass("mage-error");
            $("#cu_discount_f_cust_surname-error").hide();

        }
    });
    $("#cu_discount_f_cust_number").on('change', function () {
        $("input:text[name='custom_attributes[discount_f_cust_number]']").val($("#cu_discount_f_cust_number option:selected").val());
        $("input:text[name='custom_attributes[discount_f_cust_number]']").keyup();
    });
    $("input:radio[name='cu_discount_f_bill_in_name']").on('change', function () {
        if (this.value == '1')
        {
            $("input:checkbox[name='custom_attributes[discount_f_bill_in_name]']").prop('checked', true);
            $("#div_discount_cust_details").addClass("disable_div");
            $('#cu_discount_f_cust_surname').removeAttr("data-validate");
            $('#cu_discount_f_cust_firstname').removeAttr("data-validate");
            $('#cu_ex_invoice_cust_dob').removeAttr("data-validate");
            $('#cu_ex_invoice_cust_dob').val("");
            $('#cu_discount_f_cust_firstname').val("");
            $('#cu_discount_f_cust_surname').val("");
            $("#cu_discount_f_cust_surname").removeClass("mage-error");
            $("#cu_discount_f_cust_surname-error").hide();
            $("#cu_discount_f_cust_firstname").removeClass("mage-error");
            $("#cu_discount_f_cust_firstname-error").hide();
            $("#cu_ex_invoice_cust_dob").removeClass("mage-error");
            $("#cu_ex_invoice_cust_dob-error").hide();
        } else
        {
            $('#cu_discount_f_cust_surname').removeAttr("data-validate");
            $("#cu_discount_f_cust_surname").attr("data-validate", "{'custom-required':true, 'validate-alpha':true}");
            $('#cu_discount_f_cust_firstname').removeAttr("data-validate");
            $("#cu_discount_f_cust_firstname").attr("data-validate", "{'custom-required':true, 'validate-alpha':true}");
            $('#cu_ex_invoice_cust_dob').removeAttr("data-validate");
            $("#cu_ex_invoice_cust_dob").attr("data-validate", "{'custom-required':true}");
            $("#div_discount_cust_details").removeClass("disable_div");
            $("input:checkbox[name='custom_attributes[discount_f_bill_in_name]']").prop('checked', false);
        }
        $("input:text[name='custom_attributes[discount_f_bill_in_name]']").keyup();
    });


    $("#cu_discount_f_cust_surname").on('change', function () {
        $("input:text[name='custom_attributes[discount_f_cust_surname]']").val($("#cu_discount_f_cust_surname").val());
        $("input:text[name='custom_attributes[discount_f_cust_surname]']").keyup();
    });

    $("#cu_discount_f_cust_firstname").on('change', function () {
        $("input:text[name='custom_attributes[discount_f_cust_firstname]']").val($("#cu_discount_f_cust_firstname").val());
        $("input:text[name='custom_attributes[discount_f_cust_firstname]']").keyup();
    });

    $("#cu_discount_f_cust_dob").on('change', function () {
        $("input:text[name='custom_attributes[discount_f_cust_dob]']").val($("#cu_discount_f_cust_dob").val());
        $("input:text[name='custom_attributes[discount_f_cust_dob]']").keyup();
    });
    $("input:checkbox[name='i_ind_copm']").on('change', function () {
        if (this.checked)
        {
            $("#i_ind_copm_hidden").val('1');
            $("input:checkbox[name='custom_attributes[i_ind_copm]']").prop('checked', true);
            $('#tx_profile_dropdown').removeAttr("data-validate");
            $("#tx_profile_dropdown").attr("data-validate", "{'custom-required':true}");
            if ($("#customer_type").val() == "SOHO") {
                $('#i_ind_copm-error').hide();
            }
        } else
        {
            if ($("#customer_type").val() == "SOHO") {
                $('#i_ind_copm-error').show();
            }
            $("#i_ind_copm_hidden").val('2');
            $('#company_name').removeClass("mage-error");
            $('#company_name-error').hide();
            $('#vat_number').removeClass("mage-error");
            $('#vat_number-error').hide();
            $("#tx_profile_dropdown").removeClass("mage-error");
            $("#tx_profile_dropdown-error").hide();
            $("input:checkbox[name='custom_attributes[i_ind_copm]']").prop('checked', false);
        }
        $("input:text[name='custom_attributes[i_ind_copm]']").keyup();
    });
    $("#tx_profile_dropdown").on('change', function () {
        $("input:text[name='custom_attributes[tx_drop_down]']").val($("#tx_profile_dropdown option:selected").text());
        $("input:text[name='custom_attributes[tx_drop_down]']").keyup();
    });
    $("#legal_status").on('change', function () {
        $("input:text[name='custom_attributes[legal_status]']").val($("#legal_status option:selected").val());
        $("input:text[name='custom_attributes[legal_status]']").keyup();
    });
    $("input:text[name='company_name']").on('change', function () {
        $("input:text[name='custom_attributes[company_name]']").val($("input:text[name='company_name']").val());
        $("input:text[name='custom_attributes[company_name]']").keyup();
    });
    $("input:text[name='vat_number']").on('change', function () {
        $("input:text[name='custom_attributes[vat_number]']").val($("input:text[name='vat_number']").val());
        $("input:text[name='custom_attributes[vat_number]']").keyup();
    });
    $("input:radio[name='gender']").on('change', function () {
        $("input:text[name='prefix']").val(this.value);
        $("input:text[name='prefix']").keyup();
    });
	//Start National Id implementation
    /*$("input:radio[name='registered']").on('change', function () {
        if (this.value == '1')
        {
            $("input:checkbox[name='custom_attributes[registered]']").prop('checked', true);
            $('#passport_div').addClass('disable_div');
            $('#error_div').addClass('disable_div');
			$('#error_div .mage-error').hide();
            $('#residence_div').removeClass('disable_div');
            $('#passport_number').removeClass('mage-error');
            $('#passport_number-error').hide();
            $('.bundle-hide').removeClass('disable_div');
        } else if (this.value == '0') {
            $("#residence_number").val('');
            $("#residence_number").removeClass("mage-error");
            $('#id_number-error').hide();
            $('#residence_number-error').hide();
            var bundle_error = $("#bundle-error").val();
            if (bundle_error == 1) {
                $('#residence_div').addClass('disable_div');
                $('#passport_div').addClass('disable_div');
                $('#label_passport_div').addClass('disable_div');
                $('#passport_number').addClass('disable_div');
                $('#error_div').removeClass('disable_div');
				$('#error_div .mage-error').show();
				
                $('.bundle-hide').addClass('disable_div');
            } else {
                $('#passport_div').removeClass('disable_div');
                $('#label_passport_div').removeClass('disable_div');
                $('#passport_number').removeClass('disable_div');
                $('#error_div').addClass('disable_div');
				$('#error_div .mage-error').hide();
                $('#id_number').addClass('disable_div');
                $('#residence_div').addClass('disable_div');
                $('.bundle-hide').removeClass('disable_div');
                $("input:checkbox[name='custom_attributes[registered]']").prop('checked', false);
                $("input:text[name='custom_attributes[registered]']").keyup();
            }
        } else
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
    $("input[name='residence_number']").on('change', function () {
        $("input[name='custom_attributes[residence_number]']").val($("input[name='residence_number']").val());
        $("input[name='custom_attributes[residence_number]']").keyup();
    });
    $("input:text[name='passport_number']").on('change', function () {
        $("input:text[name='custom_attributes[passport_number]']").val($("input:text[name='passport_number']").val());
        $("input:text[name='custom_attributes[passport_number]']").keyup();
    });*/
	$("input[name='national_id_number']").on('change', function () {
        $("input:text[name='custom_attributes[national_id]']").val($("input[name='national_id_number']").val());
        $("input:text[name='custom_attributes[national_id]']").keyup();
    });
	//End National Id implementation
    $("input[name='id_number']").on('change', function () {
        $("input:text[name='custom_attributes[passport_number]']").val($("input[name='id_number']").val());
        $("input:text[name='custom_attributes[passport_number]']").keyup();
    });

    $("input:text[name='s_name']").on('change', function () {
        if ($('input[name=c_delivery_address]:radio:checked').val() == 1) {
            $("#billing-new-address-form input:text[name='lastname']").val($("input:text[name='s_name']").val());
            $("#billing-new-address-form input[name='lastname']").keyup();
        } else {
            $("#shipping-new-address-form input:text[name='lastname']").val($("input:text[name='s_name']").val());
            $("#shipping-new-address-form input[name='lastname']").keyup();
        }

    });

    $("input:text[name='s_firstname']").on('change', function () {
        if ($('input[name=c_delivery_address]:radio:checked').val() == 1) {
            $("#billing-new-address-form input:text[name='firstname']").val($("input:text[name='s_firstname']").val());
            $("#billing-new-address-form input[name='firstname']").keyup();
        } else {
            $("#shipping-new-address-form input:text[name='firstname']").val($("input:text[name='s_firstname']").val());
            $("#shipping-new-address-form input[name='firstname']").keyup();
        }

    });

    $("input[name='s_postcode_city']").on('change', function () {
        var postalCity = $("input[name='s_postcode_city']").val();
        if ($('input[name=c_delivery_address]:radio:checked').val() == 1) {
            $("#billing-new-address-form input:text[name='postcode']").val(postalCity);
            $("#billing-new-address-form input[name='postcode']").keyup();
        } else {
            $("#shipping-new-address-form input:text[name='postcode']").val(postalCity);
            $("#shipping-new-address-form input[name='postcode']").keyup();
        }

    });
    $("input[name='s_city']").on('change', function () {
        if ($('input[name=c_delivery_address]:radio:checked').val() == 1) {
            $("#billing-new-address-form input:text[name='city']").val($("input:text[name='s_city']").val());
            $("#billing-new-address-form input:text[name='region']").val($("input:text[name='s_city']").val());
            $("input[name='city']").keyup();
            $("input[name='region']").keyup();
        } else {
            $("#shipping-new-address-form input:text[name='city']").val($("input:text[name='s_city']").val());
            $("#shipping-new-address-form input:text[name='region']").val($("input:text[name='s_city']").val());
            $("input[name='city']").keyup();
            $("input[name='region']").keyup();
        }

    });
    $("input:text[name='s_box']").on('change', function () {
        if ($('input[name=c_delivery_address]:radio:checked').val() == 1) {
            $("#billing-new-address-form input:text[name='street[1]']").val($("input:text[name='s_box']").val());
            $("#billing-new-address-form input[name='street[1]']").keyup();
        } else {
            $("#shipping-new-address-form input:text[name='street[1]']").val($("input:text[name='s_box']").val());
            $("#shipping-new-address-form input[name='street[1]']").keyup();
        }

    });
    $("input:text[name='s_street']").on('change', function () {
        if ($('input[name=c_delivery_address]:radio:checked').val() == 1) {
            $("#billing-new-address-form input:text[name='street[0]']").val($("input:text[name='s_street']").val());
            $("#billing-new-address-form input[name='street[0]']").keyup();
        } else {
            $("#shipping-new-address-form input:text[name='street[0]']").val($("input:text[name='s_street']").val());
            $("#shipping-new-address-form input[name='street[0]']").keyup();
        }


    });
    $("input:text[name='s_number']").on('change', function () {
        if ($('input[name=c_delivery_address]:radio:checked').val() == 1) {
            $("#billing-new-address-form input:text[name='custom_attributes[street_number]']").val($("input:text[name='s_number']").val());
            $("#billing-new-address-form input:text[name='custom_attributes[street_number]']").keyup();
        } else {
            $("#shipping-new-address-form input:text[name='custom_attributes[street_number]']").val($("input:text[name='s_number']").val());
            $("#shipping-new-address-form input:text[name='custom_attributes[street_number]']").keyup();
        }

    });
    $("input[name='cust_telephone']").on('change', function () {
        $("input:text[name='telephone']").val($("input[name='cust_telephone']").val());
        $("input[name='telephone']").keyup();
    });
    $("input:text[name='cust_birthplace']").on('change', function () {
        $("input:text[name='custom_attributes[birth_place]']").val($("input:text[name='cust_birthplace']").val());
        $("input:text[name='custom_attributes[birth_place]']").keyup();
    });
    $("input[name='b_postcode_city']").on('change', function () {
        var postalCity = $("input[name='b_postcode_city']").val();
        $("input:text[name='postcode']").val(postalCity);
        $("input[name='postcode']").keyup();
    });
    $("input[name='b_city']").on('change', function () {
        $("input:text[name='city']").val($("input:text[name='b_city']").val());
        $("input:text[name='region']").val($("input:text[name='b_city']").val());
        $("input[name='city']").keyup();
        $("input[name='region']").keyup();
    });
    $("input:text[name='b_box']").on('change', function () {
        $("input:text[name='street[1]']").val($("input:text[name='b_box']").val());
        $("input[name='street[1]']").keyup();
    });
    $("input:text[name='b_street']").on('change', function () {
        $("input:text[name='street[0]']").val($("input:text[name='b_street']").val());
        $("input[name='street[0]']").keyup();
    });
    $("input:text[name='b_number']").on('change', function () {
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
    $("input[name='c_dob']").on('change', function () {
        var c_dob_val = $(this).val();
        var c_dob_s = c_dob_val.split('/');
        var day = c_dob_s[0];
        var month = c_dob_s[1];
        var year = c_dob_s[2];
        var f_cdob = month + "/" + day + "/" + year;
        $("input:text[name='custom_attributes[dob]']").val(f_cdob);
        $("input:text[name='custom_attributes[dob]']").change();
    });

    $(document).on("click", "input:radio[id='bank_transfer_type']", function (e) {
        $("#domiciliation_textbox_content").show();
        $(".domiciliation-message").show();
        $("#v_bank_transfer_type").val($(this).val());
    });

    $(document).on("click", "input:radio[id='edit-nationality-virement']", function (e) {
        $("#domiciliation_textbox_content").hide();
        $(".domiciliation-message").hide();
        if ($(this).val() == "Virement") {
            $("#v_bank_transfer_type").val($(this).val());
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
    function AutoCompleteSaveForm(form) {
        var iframe = document.createElement('iframe');
        iframe.name = 'save_autocomplete';
        iframe.style.cssText = 'display:none';
        document.body.appendChild(iframe);
        var oldTarget = form.target;
        var oldAction = form.action;
        form.target = 'save_autocomplete';
        form.action = '/checkout/index/chromeautocomplete';
        form.submit();
        setTimeout(function () {
            form.target = oldTarget;
            form.action = oldAction;
        });
    }
    $(document).on("change", "input:radio[name='transfer_type']", function (e) {
        $("input:text[name='v_bank_transfer_type']").val(this.value);
    });
    var $teneuroboxes = $('input[type=radio].is_teneuro:checked');
    $teneuroboxes.each(function () {
        var itemId = $(this).attr('data-itemid');
        $("#iew_telephone_" + itemId).keypress(function () {
            if (this.value.length > 10) {
                return false;
            }
        });
    });
    $("#cust_telephone").keypress(function () {
        /*if (this.value.length > 10) {
         return false;
         }*/
    });

    $(document).on("click", "#place_order_btn", function (e) {
		if ($("input[name='transfer_type']:checked" ).val() == 'Domiciliation' ) {
			if (!$("#first-accountnumber").val()) {
				  $("#first-accountnumber-error").show();
				  $("#first-accountnumber-error-data").hide();
				  $("#first-accountnumber").addClass('mage-error');
				  $("html, body").animate({ scrollTop: 0 }, 600);
				  $('.step-mage-error').show();
			  } else if ($("#first-accountnumber").val() == "BE") {
				  $("#first-accountnumber-error").hide();
				  $("#first-accountnumber-error-data").show();
				  $("#first-accountnumber").addClass('mage-error');
				  $("html, body").animate({ scrollTop: 0 }, 600);
				  $('.step-mage-error').show();
			  }
		}
        var payment_id = $('.payment-method-title input.radio:checked').val();
        $("#error-message-payment-bottom").hide();
        $('.step-mage-error').hide();
        if (payment_id != "undefined" || payment_id != "")
        {
			var cart_url = $("#cart_url").val();
        	$.ajax({
            		url: $("#quoteexpire_url").val(),
					type: "POST",
            		showLoader: true,
            		async: false,
            		success: function (result) {
						console.log(result);
                		if (result == 0) {
                    		window.location.href = cart_url;
                    		return false;
                		}
            		}	
        	});			
            $(".payment-method._active .payment-method-content .action.primary.checkout").click();
            if (typeof (payment_id) == "undefined") {
                $("html, body").animate({scrollTop: 0}, 600);
                $('.step-mage-error').show();
                $("#error-message-payment-bottom").show();
            }
        } else {
            alert("Please select any one of the payment method");
        }
    });
    $(document).on("click", ".ogone_payment", function (e) {
        $("#error-message-payment-bottom").hide();
    });

    function date_validation(value)
    {
        var c_dob_v = value;
        var c_dob_s = c_dob_v.split('/');
        var day = c_dob_s[0];
        var month = c_dob_s[1];
        var year = c_dob_s[2];
        var age = 18;

        var mydate = new Date();
        mydate.setFullYear(year, month - 1, day);

        var currdate = new Date();
        currdate.setFullYear(currdate.getFullYear() - age);

        return (currdate - mydate < 0 ? false : true);
    }

    /////////////////////Ajax Screen to Load Step3/////////////////////////////////////////////////////////////
    $(document).on("click", '#step2', function (e) {
        $("html, body").animate({scrollTop: 0}, 600);
        if (!$("input:text[name='firstname']").val() && $("input:text[name='first_name']").val()) {
            $("input:text[name='firstname']").val($("input:text[name='first_name']").val());
            $("input[name='firstname']").keyup();
        }

        if (!$("input:text[name='lastname']").val() && $("input:text[name='last_name']").val()) {
            $("input:text[name='lastname']").val($("input:text[name='last_name']").val());
            $("input[name='lastname']").keyup();
        }

        if (!$("input:text[name='telephone']").val() && $("input[name='cust_telephone']").val()) {
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
        var val_c_delivery_address = $("input[name='c_delivery_address']:checked").val();
        var val_last_name = $('#last_name').val();
        var val_b_postcode_city = $('#b_postcode_city').val();
        var val_b_street = $('#b_street').val();
        var val_b_number = $('#b_number').val();
        var val_b_box = $('#b_box').val();
        var id_number = true;
		var national_id_number = true;
        if (val_c_delivery_address == 1)
        {
            $("#c_delivery_address_bpost-error").hide();
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
            //$("#s_method_freeshipping_freeshipping").trigger('click');
        } else if (val_c_delivery_address == 3) {
            //$("#s_method_bpost_bpost").trigger('click');
            var customerPostalLocation = $('#customerPostalLocation').val();
            var customerFirstName = $('#customerFirstName').val();
            var customerLastName = $('#customerLastName').val();
            var customerStreet = $('#customerStreet').val();
            var customerStreetNumber = $('#customerStreetNumber').val();
            var customerPostalCode = $('#customerPostalCode').val();
            var customerCity = $('#customerCity').val();
            var deliveryMethod = $('#deliveryMethod').val();
            $('#isdeliveryMethod').val("1");
            $('#s_number').removeAttr("data-validate");
            $('#s_firstname').val(customerFirstName);
            $('#s_name').val(customerLastName);
            $('#s_postcode_city').val(customerPostalCode);
            $('#s_street').val(customerStreet);
            $('#s_edit_city').val(customerCity);
            $('#s_number').val(customerStreetNumber);
            $('#s_box').val('');
            $("#s_attention_n").val('');
            $("input:text[name='custom_attributes[bpost_postal_location]']").val(customerPostalLocation);
            $("input:text[name='street[0]']").val(customerStreet + " " + customerStreetNumber);
            $("input:text[name='street[1]']").val('');
            $("input[name='postcode']").val(customerPostalCode);
            $("input:text[name='region']").val(customerCity);
            $("input:text[name='city']").val(customerCity);
            $("input:text[name='firstname']").val($("input:text[name='first_name']").val());
            $("input:text[name='lastname']").val($("input:text[name='last_name']").val());
            $("input:text[name='custom_attributes[bpost_method]']").val(deliveryMethod);
        } else if (val_c_delivery_address == 2) {
            //$("#s_method_freeshipping_freeshipping").trigger('click');
            $("#c_delivery_address_bpost-error").hide();
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
        //var nationalityCheckreg = $("input[name='registered']:checked").val();
        var tx_profile_dropdown = $("#tx_profile_dropdown").val();
        var virtualproductonepage = $("#virtualproductonepage").val();
        var prepaid_check = $("#prepaid_check").val();
        var number_screen = $("#number_screen").val();
        $(checkout_form).validation();
        var first_name = $.validator.validateElement($("#first_name"));
        var last_name = $.validator.validateElement($("#last_name"));
        var email = $.validator.validateElement($("#email"));
        var b_postcode_city = $.validator.validateElement($("#b_postcode_city"));
        var b_street = $.validator.validateElement($("#b_street"));
        var b_city = $.validator.validateElement($("#b_city"));
        var b_number = $.validator.validateElement($("#b_number"));
        var val_ex_invoice = $("input[name='ex_invoice']:checked").val();
        var val_cu_ex_invoice_bill_in_name = $("input[name='cu_ex_invoice_bill_in_name']:checked").val();
        var val_discount_f = $("input[name='discount_f']:checked").val();
        var val_cu_discount_f_bill_in_name = $("input[name='cu_discount_f_bill_in_name']:checked").val();
        var c_date_v = $("#c_dob").val();
        var cu_ex_invoice_cust_number_validation = true;
        var v_bank_transfer_type = $('#v_bank_transfer_type').val();
        if (v_bank_transfer_type == "")
        {
            $("input[name='transfer_type'][value='Virement']").prop('checked', 'checked');
        } else if (v_bank_transfer_type == "Domiciliation")
        {
            $("input[name='transfer_type'][value='Domiciliation']").prop('checked', 'checked');
            $("#domiciliation_textbox_content").show();
            $(".domiciliation-message").show();
        } else
        {
            $("input[name='transfer_type'][value='Virement']").prop('checked', 'checked');
        }
        if (val_ex_invoice == 'yes')
        {
            var cu_ex_invoice_cust_number = $.validator.validateElement($("#cu_ex_invoice_cust_number"));
            if (val_cu_ex_invoice_bill_in_name == '1')
            {
                var cu_ex_invoice_cust_surname = true;
                var cu_ex_invoice_cust_firstname = true;
                var cu_ex_invoice_cust_dob = true;
            } else
            {
                var cu_ex_invoice_cust_surname = $.validator.validateElement($("#cu_ex_invoice_cust_surname"));
                var cu_ex_invoice_cust_firstname = $.validator.validateElement($("#cu_ex_invoice_cust_firstname"));
                var cu_ex_invoice_cust_dob = $.validator.validateElement($("#cu_ex_invoice_cust_dob"));
            }
            if (cu_ex_invoice_cust_number == true) {
                if ($('#cu_ex_invoice_cust_number_hidden').val() == "1") {
                    $("#cu_ex_invoice_cust_number").addClass("mage-error");
                    $('#cust_number_validation_error').show();
                    cu_ex_invoice_cust_number_validation = false;
                } else {
                    $('#cust_number_validation_error').hide();
                    $("#cu_ex_invoice_cust_number").removeClass("mage-error")
                }
            }
        } else
        {
            var cu_ex_invoice_cust_surname = true;
            var cu_ex_invoice_cust_firstname = true;
            var cu_ex_invoice_cust_dob = true;
            var cu_ex_invoice_cust_number = true;
        }
        if (val_discount_f == 'yes')
        {
            var cu_discount_f_cust_number = $.validator.validateElement($("#cu_discount_f_cust_number"));
            if (val_cu_discount_f_bill_in_name == '1')
            {
                var cu_discount_f_cust_surname = true;
                var cu_discount_f_cust_firstname = true;
                var cu_discount_f_cust_dob = true;
            } else
            {
                var cu_discount_f_cust_surname = $.validator.validateElement($("#cu_discount_f_cust_surname"));
                var cu_discount_f_cust_firstname = $.validator.validateElement($("#cu_discount_f_cust_firstname"));
                var cu_discount_f_cust_dob = $.validator.validateElement($("#cu_discount_f_cust_dob"));
            }
        } else
        {
            var cu_discount_f_cust_surname = true;
            var cu_discount_f_cust_firstname = true;
            var cu_discount_f_cust_dob = true;
            var cu_discount_f_cust_number = true;
        }

        if (virtualproductonepage == 1 || prepaid_check == 1)
        {
            var s_firstname = true;
            var s_name = true;
            var s_postcode_city = true;
            var s_street = true;
            var s_city = true;
            var s_number = true;
            var s_street_validation = 0;
            var s_zipcode_validation = 0;
        } else
        {
            if (val_c_delivery_address == 2) {
                $("#c_delivery_address_bpost-error").hide();
                var s_firstname = $.validator.validateElement($("#s_firstname"));
                var s_name = $.validator.validateElement($("#s_name"));
                var s_postcode_city = $.validator.validateElement($("#s_postcode_city"));
                var s_street = $.validator.validateElement($("#s_street"));
                var s_number = $.validator.validateElement($("#s_number"));
                var s_city = $.validator.validateElement($("#s_edit_city"));
                var s_street_validation = $("#s_street_validation").val();
                var s_zipcode_validation = $("#s_zipcode_validation").val();

            } else {
                var s_firstname = true;
                var s_name = true;
                var s_postcode_city = true;
                var s_street = true;
                var s_city = true;
                var s_number = true;
                var s_street_validation = 0;
                var s_zipcode_validation = 0;
            }
        }


        var b_zipcode_validation = $("#b_zipcode_validation").val();
        var b_street_validation = $("#b_street_validation").val();
        if (number_screen == 1)
        {
            if (nationalityCheck == "belgian")
            {
               /*Custom Validation for Idcard Number and Residence Number*/
			   var idcardlength = $("#id_number").val();
			   var idcardlengthC = idcardlength.substr(0, 1);
			   if(idcardlength.length == 0)
				{
					var id_number = $.validator.validateElement($("#id_number"));
					$("#residence_error").hide();
					$("#id_number_new_error").hide();
				}
				else
				{
           
					 if((idcardlengthC.toUpperCase() == 'B')) {
	                   var value = idcardlength.replace(/-/g, "");
						if (value.length < 10) {
						   $("#residence_error").attr("style", "display:block");
						   $('#id_number').addClass('mage-error');
						   $("#id_number_new_error").attr("style", "display:none");
						   $("#id_number-error").attr("style", "display:none");
						}
						else
						{
							$("#id_number-error").attr("style", "display:none");
							$("#residence_error").attr("style", "display:none");
							$('#id_number').removeClass('mage-error');
							$("#id_number_new_error").attr("style", "display:none");
						}
					}
					else
					{
					/** Code check for Id card Number */
					var value = idcardlength.replace(/-/g, "");
						if ((value.length) == 12) {
							var check_digit = value.substr(-2);
							var number = value.substring(0, 10);
							var result = number % 97;
									if(result == parseInt(check_digit) || result == 0  )
									{
										$("#id_number_new_error").attr("style", "display:none");
										$("#residence_error").attr("style", "display:none");
										$('#id_number').removeClass('mage-error');
										$("#id_number-error").attr("style", "display:none");
							 
									}
									else
									{ 
										$("#id_number_new_error").show();
										$('#id_number').addClass('mage-error');
										$("#residence_error").attr("style", "display:none");
										$("#id_number-error").attr("style", "display:none");
									}
						}
						else
						{
							$("#id_number_new_error").attr("style", "display:block");
							$('#id_number').addClass('mage-error');
							$("#residence_error").attr("style", "display:none");
							$("#id_number-error").attr("style", "display:none");
						}
					}
                  }
   			   
                var national_id_number = $.validator.validateElement($("#national_id_number"));
                var id_number = $.validator.validateElement($("#id_number"));
            } else {
					var id_number = true;
                    var national_id_number = true;
            }

        }
        if (number_screen == "" && prepaid_check == 0)
        {
            var c_dob = true;

        } else
        {

            if (number_screen == "" && prepaid_check == 1)
            {
                var c_dob = $.validator.validateElement($("#c_dob"));
            } else if (number_screen == 1)
            {
                var c_dob = $.validator.validateElement($("#c_dob"));
            } else
            {
                var c_dob = $.validator.validateElement($("#c_dob"));
            }



        }

        if (prepaid_check == 1)
        {
            var cust_birthplace = $.validator.validateElement($("#cust_birthplace"));
            if ($("#only_prepaid_custnumber_val").val() == "1") {
                var cust_telephone = true;
                $("input:text[name='telephone']").val('9999999999');
                $("input[name='telephone']").keyup();
            } else {
                var cust_telephone = $.validator.validateElement($("#cust_telephone"));
            }
        } else
        {
            var cust_birthplace = true;
            var cust_telephone = $.validator.validateElement($("#cust_telephone"));
        }
        var vat_number_chk = $("#vat_number").val();
        if ($("#i_ind_copm").is(':checked'))
        {
            var tx_profile_dropdown_val = $.validator.validateElement($("#tx_profile_dropdown"));
            if (tx_profile_dropdown == "")
            {
                // alert('hello');
                var legal_status_val = true;
                var vat_number = true;
                var company_name = true;

            } else if (tx_profile_dropdown == 1 || tx_profile_dropdown == 4)
            {
                var legal_status_val = true;
                var vat_number = $.validator.validateElement($("#vat_number"));
                var company_name = true;

            } else if (tx_profile_dropdown == 2)
            {
                var legal_status_val = true;
                var vat_number = true;
                var company_name = true;

            } else if (tx_profile_dropdown == 3)
            {

                var legal_status_val = $.validator.validateElement($("#legal_status"));
                var vat_number = $.validator.validateElement($("#vat_number"));
                var company_name = $.validator.validateElement($("#company_name"));

            } else
            {
                var legal_status_val = true;
                var vat_number = true;
                var company_name = true;
            }
            if ($('#vat_number_validation_error').html()) {
                var vat_number = false;
            }

        } else
        {
            if (tx_profile_dropdown == 3) {
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
        $teneuroboxes.each(function () {
            if ($(this).attr('value') == 'yes')
            {
                var itemId = $(this).attr('data-itemid');
                var iew_phone = $.validator.validateElement($("#iew_telephone_" + itemId));
                if (!iew_phone) {
                    iew_validation_status = false;
                }
                if($("#iew_telephone_" + itemId+"_hidden").val() == 1) {
                    $("#iew_telephone_"+itemId+'_error').show();
                    $("#iew_telephone_"+itemId).addClass("mage-error");
                    iew_validation_status = false;
                }
            }
        });
        var $contractboxes = $('input[type=radio].iew_contract:checked');
        $contractboxes.each(function () {
            if ($(this).attr('value') == 0)
            {
                var itemId = $(this).attr('data-itemid');
                if ($('#is_teneuro_' + itemId).is(":checked"))
                {
                    var iew_fname = $.validator.validateElement($("#iew_first_name_" + itemId));
                    var iew_lname = $.validator.validateElement($("#iew_last_name_" + itemId));
                    var iew_dob = $.validator.validateElement($("#iew_dob_" + itemId));
                    if (!iew_fname || !iew_lname || !iew_dob)
                    {
                        iew_validation_status = false;
                    }
                }
            }
        });

        if ($("#terms_condition").is(':checked'))
        {
            $("#terms_condition-error").hide();
            $("#terms_condition").removeClass('mage-error');
            var vald_terms_condition = true;
        } else
        {
            $("#terms_condition-error").show();
            $("#terms_condition").addClass('mage-error');
            var vald_terms_condition = false;
        }
        /* Validator Starts*/
        /**** Billing Address Validation on proceedding to next step*/
        $("input:text[id='b_postcode_city']").keyup();
        if ($("#b_zipcode_validation").val() == 1) {
            $("#b_zip_validation").text($.mage.__("Zipcode doesn't exist"));
        } else {
            $("#b_zip_validation").text("");
        }

        if ($("#b_zipcode_city_validation").val() == 1) {
            $("#b_city_validation").text($.mage.__("City does not exist"));
        } else {
            $("#b_city_validation").text("");
        }
        if ($("#b_street_validation").val() == 1) {
            $("#street_validation").text($.mage.__("Street doesn't exist"));
        } else {
            $("#street_validation").text("");
        }
        /**** Shipping Address Validation on proceedding to next step*/
        if (val_c_delivery_address == 2) {
            $("#c_delivery_address_bpost-error").hide();
            $("input:text[id='s_postcode_city']").keyup();
            if ($("#s_zipcode_validation").val() == 1) {
                $("#s_zip_validation").text($.mage.__("Zipcode doesn't exist"));
            } else {
                $("#s_zip_validation").text("");
            }

            if ($("#s_zipcode_city_validation").val() == 1) {
                $("#s_city_validation").text($.mage.__("City does not exist"));
            } else {
                $("#s_city_validation").text("");
            }
            if ($("#s_street_validation").val() == 1) {
                $("#street_validation_s").text($.mage.__("Street doesn't exist"));
            } else {
                $("#street_validation_s").text("");
            }
        }
        /******/
        var bpost_selected = true;
        if (val_c_delivery_address == 3) {
            var isBpost = $("#customerPostalLocation").val();
            if (isBpost != "") {
                $("#c_delivery_address_bpost-error").hide();
                var bpost_selected = true;
            } else {
                $("#c_delivery_address_bpost-error").show();
                var bpost_selected = false;
            }
        }
        var vatNumberIndCom = true;
        if ($("#customer_type").val() == "SOHO") {
            if ($("#i_ind_copm").is(':checked')) {
                $('#i_ind_copm-error').hide();
            } else {
                vatNumberIndCom = false;
                $('#i_ind_copm-error').show();
            }
        }
        var b_city_validation = $("#b_city_validation").text();
        var b_zip_validation = $("#b_zip_validation").text();
        var street_validation = $("#street_validation").text();


        if (cu_ex_invoice_cust_number_validation == false || b_city_validation.trim() != "" || b_zip_validation.trim() != "" || street_validation.trim() != "" || vald_terms_condition == false || b_zipcode_validation == 1 || s_zipcode_validation == 1 || b_street_validation == 1 || s_street_validation == 1 || id_number == false || national_id_number == false || cust_telephone == false || cust_birthplace == false || cu_discount_f_cust_number == false || cu_discount_f_cust_surname == false || cu_discount_f_cust_firstname == false || cu_discount_f_cust_dob == false || cu_ex_invoice_cust_firstname == false || cu_ex_invoice_cust_dob == false || cu_ex_invoice_cust_surname == false || cu_ex_invoice_cust_number == false || legal_status_val == false || tx_profile_dropdown_val == false || company_name == false || vat_number == false || first_name == false || last_name == false || email == false || c_dob == false || b_street == false || b_number == false || b_city == false || s_firstname == false || s_name == false || s_postcode_city == false || s_street == false || s_city == false || s_number == false || iew_validation_status == false || bpost_selected == false || vatNumberIndCom == false)
        {
            $("html, body").animate({scrollTop: 0}, 600);
            $('.step-mage-error').show();
            return false;
        } else
        {
            $('#chk_step_stat').val('final');
            $('.step-mage-error').hide();
        }

        var fName = $("#first_name").val();
        var lName = $("#last_name").val();
        var cdob = $("#c_dob").val();
        var vatnumber = $("#vat_number").val();
        var scoringTypeCheck = $("#scoring_type").val();
        if (scoringTypeCheck == '1') {
            var scoringType = "Subsidized";
        } else if(scoringTypeCheck == '2'){
            scoringType = "Classic";
        }
        var hsCount = $("#scoring_handset_count").val();
        var misidn = $("#design_te_existing_number").val();
        var idCardNumber = $("#id_number").val();
        var idType = 'eID';
        var nationality = $("input[name='nationality']:checked").val();
        //Start National Id implementation
		var nationalId = $("#national_id_number").val();
		var checkDocValidation = 'Yes';
		if (idCardNumber.indexOf('B') != -1) {
			idType = 'eID foreign';
		}
        /*if (nationality == "other") {
            var registeredbelgium = $("input[name='registered']:checked").val();
            if (registeredbelgium == "1") {
                var idType = 'eID foreign';
                var idCardNumber = $("#residence_number").val();
            }
            else {
                var idType = 'ID foreign';
                var idCardNumber = $("#passport_number").val();
            }			
        }*/
		//End National Id implementation
        var email = $("#email").val();
        var streetname = $("#b_street").val();
        var streeetnumber = $("#b_number").val();
        var zip = $("#b_postcode_city").val();
        var city = $("#b_city").val();
        var nbrsimcard = '1';
        var scoringResponse = "";
        var postpaid = $("#design_sim_number").val();
        var refusal_page = $("#scoring_redirect").val();
        var scoring_url = $("#scoring_url").val();
        if (idCardNumber.length !== 0 && fName.length !== 0 && lName.length !== 0 && cdob.length !== 0 && scoringTypeCheck.length !== 0)
        {
		    fullscreenLoader.startLoader();
            //ajax calls to scoring controller then to scoring server                  
             var val_c_delivery_address = $("input[name='c_delivery_address']:checked").val();
			 if (val_c_delivery_address == 1)
			 {
				$("#s_method_freeshipping_freeshipping").trigger('click');
			 } else if (val_c_delivery_address == 3) {
					$("#s_method_bpost_bpost").trigger('click');
			 } else {
					$("#s_method_freeshipping_freeshipping").trigger('click');
             }

            /* 5484 defect fix */
			var grandTotalValue = $("#grandtotalfinalval").val();
			if (grandTotalValue == "0") {
			   $("#thridsteppaymentshow").val(1);
			}
			setInterval(function () {
				var thridsteppaymentshow = $("#thridsteppaymentshow").val();
				var thridsteppaymentshowtimingId = $("#thridsteppaymentshowtimingId").val();
				var ogonePaymentStatus = true;
				if (grandTotalValue > 0) {
					var ogonePaymentStatus = $('input[name="payment[method]"]').hasClass('ogone_payment');
				}
				if (thridsteppaymentshow== "1" && thridsteppaymentshowtimingId == "1" && ogonePaymentStatus==true) {
					$("#thridsteppaymentshowtimingId").val(2);
					var cart_url = $("#cart_url").val();
					$.ajax({
					url: $("#quoteexpire_url").val(),
					type: "POST",
					showLoader: true,
					async: false,
					success: function (result) {
						if (result == 0) {
							window.location.href = cart_url;
							return false;
						}
					}
					});
					$.ajax({
						url: scoring_url,
						type: "POST",
						showLoader: true,
						data: "id_card_numer=" + idCardNumber + "&fname=" + fName + "&lname=" + lName + "&dob=" + cdob + "&scoring_type=" + scoringType + "&hs_count=" + hsCount + "&misidn=" + misidn + "&email=" + email + "&street_name=" + streetname + "&street_number=" + streeetnumber + "&zip=" + zip + "&city=" + city + "&nbr_sim_cards=" + nbrsimcard + "&id_type=" + idType + "&vatnumber=" + vatnumber + "&checkDocValidation=" + checkDocValidation + "&national_id=" + nationalId,
						success: function (result) {
							scoringResponse = result;
							$("#scoringResponse").val(scoringResponse);
							if (scoringResponse == 'FALSE') {
								window.location.href = refusal_page;
							} else if (!scoringResponse) {
								window.location.href = refusal_page;
							} else {
								return step2Ajaxcall();
							}
						},
						complete: function () {
						}
					});
				}
			}, 200);
			//return step2Ajaxcall();
        } else
        {
		     fullscreenLoader.startLoader();
			 var val_c_delivery_address = $("input[name='c_delivery_address']:checked").val();
			 if (val_c_delivery_address == 1)
			 {
                $("#c_delivery_address_bpost-error").hide();
				$("#s_method_freeshipping_freeshipping").trigger('click');
			 } else if (val_c_delivery_address == 3) {
					$("#s_method_bpost_bpost").trigger('click');
			 } else {
					$("#s_method_freeshipping_freeshipping").trigger('click');
             }
            var grandTotalValue = $("#grandtotalfinalval").val();
			if (grandTotalValue == "0") {
				$("#thridsteppaymentshow").val(1);
				var ogonePaymentStatus = true;
			}
			setInterval(function () {
				if (grandTotalValue > 0) {
					var ogonePaymentStatus = $('input[name="payment[method]"]').hasClass('ogone_payment');
				}
				var thridsteppaymentshow = $("#thridsteppaymentshow").val();
				var thridsteppaymentshowtimingId = $("#thridsteppaymentshowtimingId").val();
				if (thridsteppaymentshow== "1" && thridsteppaymentshowtimingId == "1" && ogonePaymentStatus == true) {
				var cart_url = $("#cart_url").val();
				$.ajax({
				url: $("#quoteexpire_url").val(),
				type: "POST",
				showLoader: true,
				async: false,
				success: function (result) {
					if (result == 0) {
						window.location.href = cart_url;
						return false;
					}
				}
				});
				$("#thridsteppaymentshowtimingId").val(2);
					return step2Ajaxcall();
				}
			}, 200);
        }

    });
    function step2Ajaxcall()
    {
        var check_delivery = $("input[name='c_delivery_address']:checked").val();
		var zipcode = $("#b_postcode_city").val();
		var cityValue = $("#b_city").val();
		var street = $("#b_street").val();
		var returnflag = 0;
		var sreturnflag = 0;
		var dataurl = "zipcode=" + zipcode.trim() + "&city=" + cityValue.trim() + "&street=" + street.trim();
		var shippingzipcodeval;
		//if shipping address is different from billing address
		if (check_delivery == 2)
		{
			var s_zipcode = $("#s_postcode_city").val();
			var s_cityValue = $("#s_edit_city").val();
			var s_street = $("#s_street").val();
			dataurl = dataurl + "&szipcode=" + s_zipcode.trim() + "&scity=" + s_cityValue.trim() + "&sstreet=" + s_street.trim();
			if(s_zipcode.length <= 4 && s_zipcode.length >= 2)
			shippingzipcodeval = true;
			else
			shippingzipcodeval = false;
		}
		else
			shippingzipcodeval = true;
		if (zipcode.length <= 4 && zipcode.length >= 2 && shippingzipcodeval)
        {
            $.ajax({
                    url: $("#road65url").val(),
                    type: "POST",
					data: dataurl
            }).done(function (result) {  
			var data_array = $.parseJSON(result);
			if(!data_array[0])
			{
				$("#b_zip_validation").text($.mage.__("Zipcode doesn't exist"));
				$("#b_zipcode_validation").val('1');
				$("#b_postcode_city-error").hide();
				returnflag = returnflag + 1;
				
			}
			if(!data_array[1])
			{
				$("#b_city_validation").text($.mage.__("City does not exist"));
				$("#b_zipcode_city_validation").val("1");
				returnflag = returnflag + 1;
			}
			if(!data_array[2])
			{
				$("#street_validation").text($.mage.__("Street doesn't exist"));
				$("#b_street-error").hide();
				$("#b_street_validation").val('1');
				returnflag = returnflag + 1;
			}
			if(check_delivery == 2)
			{
				if(!data_array[3])
				{
					$("#s_zip_validation").text($.mage.__("Zipcode doesn't exist"));
					$("#s_zipcode_validation").val('1');
					$("#s_postcode_city-error").hide();
					sreturnflag = sreturnflag + 1;
				}
				if(!data_array[4])
				{
					$("#s_city_validation").text($.mage.__("City does not exist"));
					$("#s_zipcode_city_validation").val("1");
					sreturnflag = sreturnflag + 1;
				}
				if(!data_array[5])
				{
					$("#street_validation_s").text($.mage.__("Street doesn't exist"));
					$("#s_street-error").hide();
					$("#s_street_validation").val('1');
					sreturnflag = sreturnflag + 1;
				}
			}
			if(returnflag > 0 && returnflag !=0)
			{
				fullscreenLoader.stopLoader();
				$('.step-mage-error').show();
				$("#thridsteppaymentshow").val(0);
				$("#thridsteppaymentshowtimingId").val(1);
				return false;
			}
			if(check_delivery == 2)
			{
				if(sreturnflag > 0 && sreturnflag !=0)
				{
					fullscreenLoader.stopLoader();
					$('.step-mage-error').show();
					$("#thridsteppaymentshow").val(0);
				    $("#thridsteppaymentshowtimingId").val(1);
					return false;
				}
			}
			var shippingvalidation;
			if(check_delivery == 2)
			{
				if(data_array[3] && data_array[4] && data_array[5])
				shippingvalidation = true;
				else
				shippingvalidation = false;
			}
			else
			shippingvalidation = true;	
			if(data_array[0] && data_array[1] && data_array[2] && shippingvalidation)
			{
			    var shippingInformation = $('form#checkout_form').serializeArray();
        var ajaxStep = "step2";
        var session_url = $("#session_url").val();
        var checkstepstat = "final";
        var val_c_delivery_address = $("input[name='c_delivery_address']:checked").val();
        /** Hide coupon message in total section if no rule applied**/
        if (window.checkoutConfig.quoteData.applied_rule_ids == null || window.checkoutConfig.quoteData.applied_rule_ids == "") {
            $('.coupon-message').addClass('disable_div');
        } else {
            $('.coupon-message').removeClass('disable_div');
        }
        shippingInformation = JSON.stringify(shippingInformation);
        $.ajax({
            url: session_url,
            data: {saveData: shippingInformation, aStep: ajaxStep, checkstepstat: checkstepstat},
            type: 'POST',
            dataType: 'json',
            showLoader: true,
            context: $('#step2')
        }).done(function (data) {
            //  $('#session_step').val("final");
         /*    if (val_c_delivery_address == 1)
            {
                $("#s_method_freeshipping_freeshipping").trigger('click');
            } else if (val_c_delivery_address == 3) {
                $("#s_method_bpost_bpost").trigger('click');
            } else {
                $("#s_method_freeshipping_freeshipping").trigger('click');
            } */
            $('#step2_information').addClass("disable_div");
            $("#step2_tab").removeClass("active");
            $("#step2_tab").addClass("done");
            $("#step3_tab").removeClass("last");
            $("#step3_tab").addClass("last active");
            if ($("#step3_tab").hasClass("active")) {
                $('.newpayment-cart').show();
                $('.removepayment-cart').hide();
            }
            window.location.hash = "paymentdetails";

            var tealium_status = $('input[name="tealium_status"]').val();
            if (tealium_status > 0) {
              var tealium_number_type = get_number_type_value();
              var current_operator = $('#tealium_current_operator').val();
              var num_type = [];
              for (var n in tealium_number_type) {
                num_type.push(tealium_number_type[n]);
              }
              // Payment Detail Tab.
              var tealium_data = $('input[name="tealium_values"]').val();
              var d = $.parseJSON(tealium_data);
              d.page_name = "checkout payment details";
              d.checkout_step = "payment_details";
              d.number_type = num_type;
              d.current_operator = current_operator.toLowerCase();
              utag.view(d);
            }

            $('#step').val("final");
            $('#step3_payment').removeClass("disable_div");
            $(".iwd-opc-shipping-method").hide();
            $(".shippingaddressbottom").addClass("disable_div");
            $('.cartitemvalueonepage').html($('.onepagetopcart').html());
            $('.cartcouponform').html($('.cart_coupon_from').html());
            $('#subscription-cart-total-iwd').html($('.subscription-cart-total-iwd').html());
            $('#subsidy-cart-total-iwd').html($('.subsidy-cart-total-iwd').html());
            if ($('.discount-cart-total-iwd').length) {
                $('.discount-cart-coupon').html($('.discount-cart-total-iwd').html());
            } else {
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
            if (window.checkoutConfig.quoteData.applied_rule_ids == null || window.checkoutConfig.quoteData.applied_rule_ids == "") {
                $('.coupon-message').addClass('disable_div');
            } else {
                $('.coupon-message').removeClass('disable_div');
            }
            $('.discount-cart-coupon-code').html(window.checkoutConfig.quoteData.coupon_description);
            $('#iwd-grand-total-item').html($('.iwd-grand-total-item').html());
            $('#iwd-grand-total-item').addClass('iwd-grand-total-item');
            if ($(".shipping-method-title-onepage")[0]) {
                $('.shipping-method-rightside').html($('.shipping-method-title-onepage').html());
            } else {
                //$(".total-shipping-section").hide();
            }
            $('#iwd-grand-total-item').addClass('iwd-grand-total-item');
            $("span.iwd-grand-total-item").addClass('orange');

            if ($('.virtualproductonepage').val() == 1) {
                $("#payment .action-update").trigger('click');
                $(".creditcard-label").hide();
                $(".netbanking-label").hide();
            }
            if ($('.virtualproductonepage').val() == 1 && $('.virtualproductonepage_one').val() == 1) {
                //$(".total-shipping-section").hide();
            }
            $("#payment").show();
            $(".cartitemvalueonepage").show();
            $(".cartitemvalueonepageside").show();
            $(".cartcouponform").show();
            if (window.checkoutConfig.quoteData.applied_rule_ids == null || window.checkoutConfig.quoteData.applied_rule_ids == "") {
                $('.coupon-message').addClass('disable_div');
            } else {
                $('.coupon-message').removeClass('disable_div');
            }
            if ($('#chk_step_stat').val() == "final") {
                $('.newpayment-cart').show();
                $('.removepayment-cart').hide();
            } else {
                $('.newpayment-cart').hide();
                $('.removepayment-cart').show();
            }
            if (window.checkoutConfig.quoteData.coupon_code)
            {
                //$(".iwd-coupon-code-item").show();
                $('.coupon_summary').show();
            }
            $(".iwd-discount-options").show();
            /*Chrome Autocomplete */
            var myForm = document.getElementById('checkout_form');
            var x = AutoCompleteSaveForm(myForm);
        });
        $('.step-mage-error').hide();
        if ($("#edit-nationality-virement").is(':checked')) {
            $(".domiciliation-message").hide();
        }
        var grandTotal = window.checkoutConfig.quoteData.grand_total;
        if (grandTotal < 1) {
            $('#checkout-payment-method-load').hide();
        }
        $("html, body").animate({scrollTop: 0}, 600);
		fullscreenLoader.stopLoader();
			}
			});
		}
    }
/////////////////////////////////////////////////////////////
    $('input:radio[name="c_delivery_address"]').change(function () {
        if (this.checked && this.value == '1') {
            $("input[name='billing-address-same-as-shipping']").prop('checked', true);
            var last_name = $('#last_name').val();
            var b_postcode_city = $('#b_postcode_city').val();
            var b_street = $('#b_street').val();
            var b_number = $('#b_number').val();
            var b_box = $('#b_box').val();
            $('#s_name').val(last_name);
            $('#s_postcode_city').val(b_postcode_city);
            $('#s_street').val(b_street);
            $('#s_number').val(b_number);
            $('#s_box').val(b_box);
            $("#div_shipping_address").removeClass("disable_div");
            $("#div_shipping_address").addClass("disable_div");
            $("#bpost").addClass("disable_div");
            $("#c_delivery_address_bpost-error").hide();
            //$("#s_method_freeshipping_freeshipping").trigger('click');
        } else if (this.checked && this.value == '3') {
            //$("#s_method_bpost_bpost").trigger('click');
            $("#div_shipping_address").removeClass("disable_div");
            $("#div_shipping_address").addClass("disable_div");
            $("#bpost").removeClass("disable_div");
        } else {
            //$("#s_method_freeshipping_freeshipping").trigger('click');
            $("#bpost").addClass("disable_div");
            $("#c_delivery_address_bpost-error").hide();
            $("#div_shipping_address").removeClass("disable_div");
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
    $('.a_subscription_type').click(function () {
        var subscriptionType = $(this).attr('data-dir');
        $('#subscription_type').val(subscriptionType);
        $('a#subscription_div_a').text(subscriptionType);

    });
    /////////////////////////////////////////////////////////////////////////		
    //Simcard Change
    $('.design_sim_number').on('click', function () {
        //$("#step1_continue_div").hide();
        var id = this.id;
        var designsimid = id.split("_");
        var id = designsimid[1] + "_" + designsimid[2];
        var totalpostpaidValue = $("#totalvirtualproduct").val();
        var res;

        if (totalpostpaidValue.indexOf(',') != -1) {
            var res = totalpostpaidValue.split(",");
        } else {
            res = [totalpostpaidValue];
        }
        var tealium_status = $('input[name="tealium_status"]').val();
        if (tealium_status > 0) {
          var tealium_number_type = get_number_type_value();
          var current_operator = $('#tealium_current_operator').val();
          var num_type = [];
          for (var n in tealium_number_type) {
            num_type.push(tealium_number_type[n]);
          }
          var tealium_data = $('input[name="tealium_values"]').val();
          var d = $.parseJSON(tealium_data);
          d.page_name = "checkout number";
          d.checkout_step = "number";
          d.number_type = num_type;
          d.current_operator = current_operator.toLowerCase();
          utag.view(d);
        }
        if (this.checked && this.value == '2') {
            $("#designteexistingnumber_" + id + "-error").html('');
            $("#design_te_existing_number_final_validation_" + id).val('0');
        }
        if (this.checked && this.value == '1') {
            $("#designteexistingnumber_" + id).show();
            $("#designteexistingnumber_" + id).removeClass("form-control required valid mage-error");
            $("#designteexistingnumber_" + id).addClass("form-control");
            // validation missing
            $("#designteexistingnumber_" + id).removeAttr("data-validate");
            $("#designteexistingnumber_" + id).attr("data-validate", "{'custom-required':true,'validate-orangenumber':true,'validate-orangenumber-total':true,'restriction-postpaid-number':true}");
            if ($("#design_te_existing_number_final_validation_" + id).val() == 0) {
                showDivButtons(3);
            }
        } else
        {
            $("#designteexistingnumber_" + id).removeAttr("data-validate");
            $('#design_te_existing_number-error').hide();
            $("#designteexistingnumber_" + id).hide();
            $("#designteexistingnumber_" + id).val('');
            $("#design_te_existing_number_final_validation_" + id).val('0');
            $("#network_customer_number_" + id).val('');
            $("#simcard_number_" + id).val('');
            $("#holders_name_" + id).val('');
            $("input:text[name='custom_attributes[holders_name]']").val('');
            $("input:text[name='custom_attributes[holders_name]']").keyup();
            $("#holder_name_" + id).val('');
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
                if ($("input[name='design_sim_number-" + res[i] + "']:checked").val() == '1') {

                    if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 1) {
                        showFirstValue = 1;
                    }
                    if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 2) {
                        showSecondValue = 4;
                        if (iewItemsCheck.indexOf(res[i]) <= 0) {
                            iewChecking == '2';
                        }
                    }
                    if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 3) {
                        showThirdValue = 2;
                    }
                    if ($("#design_te_existing_number_final_validation_" + res[i]).val() == 0) {
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
                        if ($("#continue-delete-button").hasClass("continue-delete-step1-existing")) { //continue-delete-step1
                            $("#continue-delete-button").removeClass('continue-delete-step1-existing');
                        }
                        if (!$("#continue-delete-button").hasClass("continue-delete-step1")) {
                            $("#continue-delete-button").addClass('continue-delete-step1');
                        }
                        $("#customer-zone-button").hide();
                        $("#customer-zone-des").hide();
                        $("#continue-delete-button-top").show();
                        $("#continue-delete-button").show();
                    } else {
                        if ($("#continue-delete-button").hasClass("continue-delete-step1")) {
                            $("#continue-delete-button").removeClass('continue-delete-step1');
                        }
                        if (!$("#continue-delete-button").hasClass("continue-delete-step1-existing")) {
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
            if ($('input.iew_sim:radio:checked').length > 0 && showValue != 3) {
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

    $(".design_te_existing_number").keyup(function () {
        var str = this.value;
        var replaced = str.replace(/[\. ,:_/]+/g, "");
        var id = this.id;
        var designsimid = id.split("_");
        var id = designsimid[1] + "_" + designsimid[2];
        var existingNumberfinal = $("#design_te_existing_number_final_validation_" + id).val();
        $("#design_te_existing_number_final_validation_" + id).val('0');
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
                $("#design_te_existing_number_final_validation_" + id).val(existingNumberfinal);
                return false;
            }
            existing = this.value;
            $("#design_te_existing_number_final_validation_" + id).val('0');
            number_validation(this.value, id);

        }
    });
    $(".design_te_existing_number").blur(function () {
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
    $('#step2_tab').click(function () {
        var stepValue = $('#step').val();
        $('.newpayment-cart').hide();
        $('.removepayment-cart').show();
		$("#thridsteppaymentshow").val(0);
		$("#thridsteppaymentshowtimingId").val(1);
        final_ajax_call();
        if (stepValue == 'final')
        {
            $('#chk_step_stat').val('step3');
            $('#session_step').val('step3');
            $('#step3_payment').addClass("disable_div");
            $("#step1_tab").removeClass("first active");
            $("#step1_tab").addClass("first done");
            $("#step2_tab").removeClass("done");
            $("#step2_tab").addClass("active");
            $("#step3_tab").removeClass("last active");
            $("#step3_tab").addClass("last");
            $('#step').val("step3");
            $('#step2_information').removeClass("disable_div");
            $("#step1_exiting_num").show();
            $("#transfer_number").addClass("disable_div");
            $('.cartcouponform').hide();
            $(".iwd-opc-shipping-method").show();
            var nationalityCheck = $("input[name='nationality']:checked").val();
            //var nationalityCheckreg = $("input[name='registered']:checked").val();
            if (nationalityCheck == "belgian" || nationalityCheck == '') {
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

        if ($('#step2_tab').hasClass("active")) {
            window.location.hash = "details";
            var tealium_status = $('input[name="tealium_status"]').val();
            if (tealium_status > 0) {
              var tealium_number_type = get_number_type_value();
              var current_operator = $('#tealium_current_operator').val();
              var num_type = [];
              for (var n in tealium_number_type) {
                num_type.push(tealium_number_type[n]);
              }
              // Personal Details Tab.
              var tealium_data = $('input[name="tealium_values"]').val();
              var d = $.parseJSON(tealium_data);
              d.page_name = "checkout personal details";
              d.checkout_step = "personal_details";
              d.number_type = num_type;
              d.current_operator = current_operator.toLowerCase();
              utag.view(d);
            }
        }

    });

    //Tab Hide
    $('#step1_tab').click(function () {
        $('.newpayment-cart').hide();
        $('.removepayment-cart').show();
		$("#thridsteppaymentshow").val(0);
		$("#thridsteppaymentshowtimingId").val(1);
        final_ajax_call();
        var stepValue = $('#step').val();
        if (stepValue == 'final' || stepValue == 'step3')
        {
            $('#session_step').val("step2");
            $('#step2_information').addClass("disable_div");
            $('#step3_payment').addClass("disable_div");
            $("#step1_tab").removeClass("first done");
            $("#step1_tab").addClass("first active");
            $("#step2_tab").removeClass("done");
            $("#step2_tab").removeClass("active");
            $("#step3_tab").removeClass("last active");
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

        if ($('#step1_tab').hasClass("active")) {
            window.location.hash = "number";
            var tealium_status = $('input[name="tealium_status"]').val();
            if (tealium_status > 0) {
              var tealium_number_type = get_number_type_value();
              var current_operator = $('#tealium_current_operator').val();
              var num_type = [];
              for (var n in tealium_number_type) {
                num_type.push(tealium_number_type[n]);
              }
              // Number Tab.
              var tealium_data = $('input[name="tealium_values"]').val();
              var d = $.parseJSON(tealium_data);
              d.page_name = "checkout number";
              d.checkout_step = "number";
              d.number_type = num_type;
              d.current_operator = current_operator.toLowerCase();
              utag.view(d);
            }
        }

    });

    // Profile Dropdown ONCHANGE Event
    // ====================
    $("#tx_profile_dropdown").change(function () {
        var profileStatus = $('#tx_profile_dropdown').val();
        $('#vat_number-error').hide();
        $('#vat_number_validation_error').html('');
        $('#vat_number').removeClass('mage-error');
        if (profileStatus == 1)
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
            $("#company_div").removeClass("row margin-sm-b-s disable_div");
            $("#legal_status_div").removeClass("row margin-sm-b-s disable_div");
            $("#vat_number_div").removeClass("row margin-sm-b-s disable_div");
            $("#company_div").addClass("row margin-sm-b-s disable_div");
            $("#legal_status_div").addClass("row margin-sm-b-s disable_div");
            $("#vat_number_div").addClass("row margin-sm-b-s");
            $("#vat_number").removeClass("form-control required mage-error");
            $("#vat_number").addClass("form-control");
            $('#vat_number').removeAttr("data-validate");
            $("#vat_number").attr("data-validate", "{'custom-required':true, 'validate-new-alphanum':true,'vat-number-min-max-lenth-validation':true}");
            $('#company_name').removeAttr("data-validate");
            $("#legal_status").removeAttr("data-validate");
        } else if (profileStatus == 2)
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
            $("#company_div").removeClass("row margin-sm-b-s disable_div");
            $("#legal_status_div").removeClass("row margin-sm-b-s disable_div");
            $("#vat_number_div").removeClass("row margin-sm-b-s disable_div");
            $("#company_div").addClass("row margin-sm-b-s disable_div");
            $("#legal_status_div").addClass("row margin-sm-b-s disable_div");
            $("#vat_number_div").addClass("row margin-sm-b-s disable_div");
            $('#vat_number_validation_error').html('');
            $('#vat_number').removeAttr("data-validate");
            $('#company_name').removeAttr("data-validate");
            $("#legal_status").removeAttr("data-validate");


        } else if (profileStatus == 3)
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
            $("#vat_number_div").removeClass("row margin-sm-b-s disable_div");
            $("#company_div").removeClass("row margin-sm-b-s disable_div");
            $("#legal_status_div").removeClass("row margin-sm-b-s disable_div");
            $("#vat_number_div").addClass("row margin-sm-b-s");
            $("#company_div").addClass("row margin-sm-b-s");
            $("#legal_status_div").addClass("row margin-sm-b-s");
            $('#vat_number').removeAttr("data-validate");
            $("#vat_number").attr("data-validate", "{'custom-required':true, 'validate-new-alphanum':true,'vat-number-min-max-lenth-validation':true}");
            $('#company_name').removeAttr("data-validate");
            $("#company_name").attr("data-validate", "{'custom-required':true}");
            //$('#company_name').removeAttr( "data-validate" );
            $("#legal_status").removeAttr("data-validate");
            $("#legal_status").attr("data-validate", "{'custom-required':true}");



        } else if (profileStatus == 4)
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
            $("#company_div").removeClass("row margin-sm-b-s disable_div");
            $("#legal_status_div").removeClass("row margin-sm-b-s disable_div");
            $("#vat_number_div").removeClass("row margin-sm-b-s disable_div");
            $("#vat_number_div").addClass("row margin-sm-b-s");
            $("#company_div").addClass("row margin-sm-b-s disable_div");
            $("#legal_status_div").addClass("row margin-sm-b-s disable_div");
            $('#vat_number').removeAttr("data-validate");
            $("#vat_number").attr("data-validate", "{'custom-required':true, 'validate-new-alphanum':true,'vat-number-min-max-lenth-validation':true}");
            $('#company_name').removeAttr("data-validate");
            $("#legal_status").removeAttr("data-validate");
        } else
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
            $("#company_div").removeClass("row margin-sm-b-s disable_div");
            $("#legal_status_div").removeClass("row margin-sm-b-s disable_div");
            $("#vat_number_div").removeClass("row margin-sm-b-s disable_div");
            $("#company_div").addClass("row margin-sm-b-s disable_div");
            $("#legal_status_div").addClass("row margin-sm-b-s disable_div");
            $("#vat_number_div").addClass("row margin-sm-b-s disable_div");
            $('#vat_number_validation_error').html('');
            $('#vat_number').removeAttr("data-validate");
            $('#company_name').removeAttr("data-validate");
            $("#legal_status").removeAttr("data-validate");
        }
    });

    //Start National Id implementation
	$('input:radio[name="nationality"]').change(function () {
		if (this.checked && this.value == 'belgian') {
			$('.bundle-hide').removeClass('disable_div');
			$('#id_number_div').removeClass('disable_div');
			$("#residence_error").hide();
			$("#id_number_new_error").hide();
			$('#national_id_number_div').removeClass('disable_div');
			$("#id_number").attr("data-validate", "{'custom-required':true, 'id-number':true}");
			$("#national_id_number").attr("data-validate", "{'custom-required':true, 'national-id-number':true}");
			$('#error_div').addClass('disable_div');
            $('#error_div .mage-error').removeAttr("style");
		} else {
			$('.bundle-hide').addClass('disable_div');
			$('#id_number_div').addClass('disable_div');
			$('#national_id_number_div').addClass('disable_div');
			$('#id_number-error').hide();
			$('#national_id_number-error').hide();
			$('#id_number').removeClass('mage-error');
			$('#national_id_number').removeClass('mage-error');
			$('#id_number').removeAttr("data-validate");
			$('#national_id_number').removeAttr("data-validate");
			$("#id_number").val('');
			$("#national_id_number").val('');
			$('#error_div').removeClass('disable_div');
            $('#error_div .mage-error').removeAttr("style");
		}
	});
	//End National Id implementation


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
            var iewClassNameCheck = $("input[name='design_sim_number-" + res[i] + "']").attr('class');
            if (~iewClassNameCheck.indexOf("iew_sim")) {
                continue;
            }
            var validateDesignsimnumber = $("input[name='design_sim_number-" + res[i] + "']:checked").val();
            if (validateDesignsimnumber == '1') {
                var validate = true;
                if ($("input[name='design_te_existing_number-" + res[i] + "']").hasClass('mage-error')) {
                    $.validator.validateElement($("input[name='design_te_existing_number-" + res[i] + "']"));
                }
            }
        }
    }

    var finalvalueLoad = 0;
    var addresscheckload = 0;
    var paymentcheckload = 0;
    var loaderon = 0;
    var deliveryaddressValue = 1;
    setInterval(function () {
        var postcode = $("input:text[name='postcode']").val();
        if (paymentcheckload == 0 && typeof postcode != 'undefined') {

            var session_step = $("#session_step").val();


            if (session_step == 'final')
            {
                if (loaderon == 0)
                {
                    fullscreenLoader.startLoader();
                    loaderon = 1;
                }
                var pre_onepage_trigger = $("#pre_onepage_trigger").val();
                if (pre_onepage_trigger == 1)
                {
                    onepage_trigger();
                }
                var prepaid_check = $("#prepaid_check").val();
                var oFirstName = $("input:text[name='first_name']").val();
                var oLastName = $("input:text[name='last_name']").val();
                var postalCity = $("input[name='b_postcode_city']").val();
                if ($("#only_prepaid_custnumber_val").val() == "1") {
                    var otelephone = $("input[name='cust_telephone']").val(9999999999);
                } else
                {
                    var otelephone = $("input[name='cust_telephone']").val();
                }


                if (!$("input:text[name='firstname']").val() && $("input:text[name='first_name']").val()) {
                    $("input:text[name='firstname']").val($("input:text[name='first_name']").val());
                    $("input[name='firstname']").keyup();
                }

                if (!$("input:text[name='lastname']").val() && $("input:text[name='last_name']").val()) {
                    $("input:text[name='lastname']").val($("input:text[name='last_name']").val());
                    $("input[name='lastname']").keyup();
                }
                if (prepaid_check == 1)
                {
                    if ($("#only_prepaid_custnumber_val").val() == "1") {
                        if (!$("input:text[name='telephone']").val()) {
                            $("input:text[name='telephone']").val(9999999999);
                            $("input[name='telephone']").keyup();
                        } else {
                            if (!$("input:text[name='telephone']").val() && otelephone) {
                                $("input:text[name='telephone']").val(otelephone);
                                $("input[name='telephone']").keyup();
                            }
                        }
                    }
                } else
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

          

                $("input:text[name='custom_attributes[dob]']").change();
                //$("#s_method_freeshipping_freeshipping").trigger('click');
                var val_c_delivery_address = $("input[name='c_delivery_address']:checked").val();
                var val_last_name = $('#last_name').val();
                var val_b_postcode_city = $('#b_postcode_city').val();
                var val_b_street = $('#b_street').val();
                var val_b_number = $('#b_number').val();
                var val_b_box = $('#b_box').val();
                var id_number = true;
                if (val_c_delivery_address == 1)
                {
                    $("#c_delivery_address_bpost-error").hide();
                    $('#s_name').val(val_last_name);
                    $('#s_firstname').val($('#first_name').val());
                    $('#s_postcode_city').val(val_b_postcode_city);
                    $('#s_street').val(val_b_street);
                    $('#s_number').val(val_b_number);
                    $('#s_box').val(val_b_box);
                    $('#s_city').val($("input:text[name='b_city']").val());
                    $("input:text[name='custom_attributes[bpost_postal_location]']").val('');
                    $("input:text[name='custom_attributes[bpost_method]']").val('');

                } else if (val_c_delivery_address == 3) {
                    var customerPostalLocation = $('#customerPostalLocation').val();
                    var customerFirstName = $('#customerFirstName').val();
                    var customerLastName = $('#customerLastName').val();
                    var customerStreet = $('#customerStreet').val();
                    var customerStreetNumber = $('#customerStreetNumber').val();
                    var customerPostalCode = $('#customerPostalCode').val();
                    var customerCity = $('#customerCity').val();
                    var deliveryMethod = $('#deliveryMethod').val();
                    $('#s_number').removeAttr("data-validate");
                    $('#s_firstname').val(customerFirstName);
                    $('#s_name').val(customerLastName);
                    $('#s_postcode_city').val(customerPostalCode);
                    $('#s_street').val(customerStreet);
                    $('#s_edit_city').val(customerCity);
                    $('#s_number').val(customerStreetNumber);
                    $('#s_box').val('');
                    $("input:text[name='custom_attributes[bpost_postal_location]']").val(customerPostalLocation);
                    $("input:text[name='street[0]']").val(customerStreet + " " + customerStreetNumber);
                    $("input:text[name='street[1]']").val('');
                    $("input[name='postcode']").val(customerPostalCode);
                    $("input:text[name='region']").val(customerCity);
                    $("input:text[name='city']").val(customerCity);
                    $("input:text[name='firstname']").val($("input:text[name='first_name']").val());
                    $("input:text[name='lastname']").val($("input:text[name='last_name']").val());
                    $("input:text[name='custom_attributes[bpost_method]']").val(deliveryMethod);
                } else if (val_c_delivery_address == 2) {
                    $("#c_delivery_address_bpost-error").hide();
                    $("input:text[name='custom_attributes[bpost_postal_location]']").val("");
                    $("input:text[name='custom_attributes[bpost_method]']").val('');
                    $("input:text[name='firstname']").val($("input:text[name='first_name']").val());
                    $("input:text[name='lastname']").val($("input:text[name='last_name']").val());
                    $("input:text[name='city']").val($("input:text[name='s_city']").val());
                    $("input:text[name='region']").val($("input:text[name='s_city']").val());
                    $("input[name='postcode']").val($("input:text[name='s_postcode_city']").val());
                }
                var v_bank_transfer_type = $('#v_bank_transfer_type').val();
                if (v_bank_transfer_type == "")
                {
                    $("input[name='transfer_type'][value='Virement']").prop('checked', 'checked');
                } else if (v_bank_transfer_type == "Domiciliation")
                {
                    $("input[name='transfer_type'][value='Domiciliation']").prop('checked', 'checked');
                    $("input[name='account_number']").val($("input:text[name='v_account_number']").val());
                    $("#domiciliation_textbox_content").show();
                    $(".domiciliation-message").show();
                } else
                {
                    $("input[name='transfer_type'][value='Virement']").prop('checked', 'checked');
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
                if (virtualhideDeliveryValue == "1" && $('.virtualproductonepage').val() == 1) {
                    $(".total-shipping-section").hide();
                }
                $('.newpayment-cart').show();
                $('.removepayment-cart').hide();
                if ($("#edit-nationality-virement").is(':checked')) {
                    $(".domiciliation-message").hide();
                }
                if ($("#bank_transfer_type").is(':checked')) {
					  $('#first-accountnumber').val($('#v_account_number').val());
                    $(".domiciliation-message").show();
                }
                var grandTotal = window.checkoutConfig.quoteData.grand_total;
                if (grandTotal < 1) {
                    $('#checkout-payment-method-load').hide();
                }
                $("#step1_number").removeClass("disable_div");
                $('#step1_number').addClass("disable_div");
                $("#step2_information").removeClass("disable_div");
                $('#step2_information').addClass("disable_div");
                $("#step1_tab").removeClass("first active");
                $("#step1_tab").addClass("first done");
                $("#step2_tab").removeClass("active");
                $("#step2_tab").addClass("done");
                $("#step3_tab").removeClass("last");
                $("#step3_tab").addClass("last active");
                window.location.hash = "paymentdetails";
                var tealium_status = $('input[name="tealium_status"]').val();
                if (tealium_status > 0) {
                  var tealium_number_type = get_number_type_value();
                  var current_operator = $('#tealium_current_operator').val();
                  var num_type = [];
                  for (var n in tealium_number_type) {
                    num_type.push(tealium_number_type[n]);
                  }
                  // Payment Detail Tab.
                  var tealium_data = $('input[name="tealium_values"]').val();
                  var d = $.parseJSON(tealium_data);
                  d.page_name = "checkout payment details";
                  d.checkout_step = "payment_details";
                  d.number_type = num_type;
                  d.current_operator = current_operator.toLowerCase();
                  utag.view(d);
                }
                $('#step').val("final");
                $('#step3_payment').removeClass("disable_div");
                $(".iwd-opc-shipping-method").hide();
                $(".shippingaddressbottom").addClass("disable_div");
                $('.cartitemvalueonepage').html($('.onepagetopcart').html());
                $('#subscription-cart-total-iwd').html($('.subscription-cart-total-iwd').html());
                $('#subsidy-cart-total-iwd').html($('.subsidy-cart-total-iwd').html());
                $('.cartcouponform').html($('.cart_coupon_from').html());
                $('.cartcouponform').show();
                if (window.checkoutConfig.quoteData.applied_rule_ids == null || window.checkoutConfig.quoteData.applied_rule_ids == "") {
                    $('.coupon-message').addClass('disable_div');
                } else {
                    $('.coupon-message').removeClass('disable_div');
                }
                $('#iwd-grand-total-item').html($('.iwd-grand-total-item').html());
                $('#iwd-grand-total-item').addClass('iwd-grand-total-item');
                if ($(".shipping-method-title-onepage")[0]) {
                    $('.shipping-method-rightside').html($('.shipping-method-title-onepage').html());
                } else {
                    //$(".total-shipping-section").hide();
                }
                if ($('.virtualproductonepage').val() == 1) {
                    $("#payment .action-update").trigger('click');
                    $(".creditcard-label").hide();
                    $(".netbanking-label").hide();
                }
                if ($('.virtualproductonepage').val() == 1 && $('.virtualproductonepage_one').val() == 1) {
                    //$(".total-shipping-section").hide();
                }
                laststepshow();

                var blockcvalueonepage = $(".cartitemvalueonepage").css('display');
                var blockConepageside = $(".cartitemvalueonepageside").css('display');

                if (blockcvalueonepage == "none" || blockConepageside == "none")
                {
                    thirdStep();
                } else
                {
                    thirdStep();
                }
                var classstep2_information = $('#step2_information').attr('class');
                var classstep1_number = $('#step1_number').attr('class');
                if (blockcvalueonepage == "block" && blockConepageside == "block" && $("#payment").css('display') == "block")
                {
                    var grandtotalfinalval = $("#grandtotalfinalval").val();
                    if (grandtotalfinalval > 0)
                    {
                        var shippingFinalClick = $("#s_method_freeshipping_freeshipping").val();
                        var bpostFinalClick = $("#s_method_freeshipping_freeshipping").val();
                        if (deliveryaddressValue == 1 && typeof shippingFinalClick != 'undefined') {
                            if (val_c_delivery_address == 1) {
                                $("#c_delivery_address_bpost-error").hide();
                                $("#s_method_freeshipping_freeshipping").trigger('click');
                                deliveryaddressValue = 2
                            } else if (val_c_delivery_address == 3) {
                                $("#s_method_bpost_bpost").trigger('click');
                                deliveryaddressValue = 2
                            } else {
                                $("#s_method_freeshipping_freeshipping").trigger('click');
                                deliveryaddressValue = 2
                            }
                        }

                        var payment_radio = $('input[name="payment[method]"]').val();
                        if (typeof payment_radio != 'undefined') {
                            paymentcheckload = 1;
                            $("#session_step").val('step2');
                            fullscreenLoader.stopLoader();
                            $("html, body").animate({scrollTop: 0}, 600);
                        }

                    } else {
                        var tvirtualproductonepage = $("#virtualProductSimple").val();

                        if (tvirtualproductonepage == 0)
                        {

                            var typeshipping = $("input[type=radio][name=shipping_method]:checked").val();
                            ;
                            if (typeof typeshipping != 'undefined')
                            {
                                $("#s_method_freeshipping_freeshipping").trigger('click');
                                paymentcheckload = 1;
                                $("#session_step").val('step2');
                                fullscreenLoader.stopLoader();
                                $("html, body").animate({scrollTop: 0}, 600);
                            }

                        } else
                        {
                            paymentcheckload = 1;
                            $("#session_step").val('step2');
                            loadcheckouttrigger();
                            fullscreenLoader.stopLoader();
                            $("html, body").animate({scrollTop: 0}, 600);
                        }

                    }

                }
            }
        }

    }, 200);
    function thirdStep()
    {

        $("#step1_number").removeClass("disable_div");
        $('#step1_number').addClass("disable_div");
        $("#step2_information").removeClass("disable_div");
        $('#step2_information').addClass("disable_div");
        $("#step1_tab").removeClass("first active");
        $("#step1_tab").addClass("first done");
        $("#step2_tab").removeClass("active");
        $("#step2_tab").addClass("done");
        $("#step3_tab").removeClass("last");
        $("#step3_tab").addClass("last active");
        window.location.hash = "paymentdetails";
        $('#step').val("final");
        $('#step3_payment').removeClass("disable_div");
        $(".iwd-opc-shipping-method").hide();
        $(".shippingaddressbottom").addClass("disable_div");
        $('.cartitemvalueonepage').html($('.onepagetopcart').html());
        $('#subscription-cart-total-iwd').html($('.subscription-cart-total-iwd').html());
        $('#subsidy-cart-total-iwd').html($('.subsidy-cart-total-iwd').html());
        $('.cartcouponform').html($('.cart_coupon_from').html());
        $('.cartcouponform').show();
        if (window.checkoutConfig.quoteData.applied_rule_ids == null || window.checkoutConfig.quoteData.applied_rule_ids == "") {
            $('.coupon-message').addClass('disable_div');
        } else {
            $('.coupon-message').removeClass('disable_div');
        }
        $('#iwd-grand-total-item').html($('.iwd-grand-total-item').html());
        $('#iwd-grand-total-item').addClass('iwd-grand-total-item');
        if ($(".shipping-method-title-onepage")[0]) {
            $('.shipping-method-rightside').html($('.shipping-method-title-onepage').html());
        } else {
            //$(".total-shipping-section").hide();
        }
        if ($('.virtualproductonepage').val() == 1) {
            $("#payment .action-update").trigger('click');
            $(".creditcard-label").hide();
            $(".netbanking-label").hide();
        }
        if ($('.virtualproductonepage').val() == 1 && $('.virtualproductonepage_one').val() == 1) {
            //$(".total-shipping-section").hide();
        }

        laststepshow();

    }
    function onepage_trigger()
    {
        var checkstepstat = 'final';
        var shippingInformation = $('form#checkout_form').serializeArray();
        var ajaxStep = "step2";
        var session_url = $("#session_url").val();
        var val_c_delivery_address = $("input[name='c_delivery_address']:checked").val();
        /** Hide coupon message in total section if no rule applied**/
        if (window.checkoutConfig.quoteData.applied_rule_ids == null || window.checkoutConfig.quoteData.applied_rule_ids == "") {
            $('.coupon-message').addClass('disable_div');
        } else {
            $('.coupon-message').removeClass('disable_div');
        }
        shippingInformation = JSON.stringify(shippingInformation);
		/* removed duplicate call in session #5694*/
		var sessionShippingmethodend = $('#sessionShippingmethodend').val();
		if (sessionShippingmethodend == "1") {
		    $('#sessionShippingmethodend').val('2');
			$.ajax({
				url: session_url,
				data: {saveData: shippingInformation, aStep: ajaxStep, checkstepstat: checkstepstat},
				type: 'POST',
				dataType: 'json',
				showLoader: true,
				context: $('#step2')
			}).done(function (data) {
				//$('#pre_onepage_trigger').val("2");
			});
		}
        $('.step-mage-error').hide();
        if ($("#edit-nationality-virement").is(':checked')) {
            $(".domiciliation-message").hide();
        }
        var grandTotal = window.checkoutConfig.quoteData.grand_total;
        if (grandTotal < 1) {
            $('#checkout-payment-method-load').hide();
        }
		var typeshippingMethodValueChecking = $("input[type=radio][name=shipping_method]").val();
		if (typeof typeshippingMethodValueChecking != 'undefined') {
		    $('#pre_onepage_trigger').val("2"); 
			if (val_c_delivery_address == 1 && typeshippingMethodValueChecking != "freeshipping_freeshipping") {
				$("#s_method_freeshipping_freeshipping").trigger('click');
			} else if (val_c_delivery_address == 3) {
				$("#s_method_bpost_bpost").trigger('click');
			} else {
			    if (typeshippingMethodValueChecking != "freeshipping_freeshipping") {
					$("#s_method_freeshipping_freeshipping").trigger('click');
				}
			}
		}
       /* end duplicate call */
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
        var val_c_delivery_address = $("input[name='c_delivery_address']:checked").val();
        if (val_c_delivery_address == 1)
        {
            $("#s_method_freeshipping_freeshipping").trigger('click');
        } else if (val_c_delivery_address == 3)
        {
            $("#s_method_bpost_bpost").trigger('click');
        } else {
            $("#s_method_freeshipping_freeshipping").trigger('click');
        }


    }
    /*
     #####################New function added for validation#####################
     */
    function Calculate1(Luhn) {
        var sum = 0;
        for (i = 0; i < Luhn.length; i++) {
            sum += parseInt(Luhn.substring(i, i + 1));
        }
        var delta = new Array(0, 1, 2, 3, 4, -4, -3, -2, -1, 0);
        for (i = Luhn.length - 1; i >= 0; i -= 2) {
            var deltaIndex = parseInt(Luhn.substring(i, i + 1));
            var deltaValue = delta[deltaIndex];
            sum += deltaValue;
        }
        var mod10 = sum % 10;
        mod10 = 10 - mod10;
        if (mod10 == 10) {
            mod10 = 0;
        }
        return mod10;
    }
    function simcard_proxi_main(values) {
        var ok = false;
        var value = values.toString();
        var length = value.length;

        if (length == '13') {
            var A = [];
            var som = 0;
            var check = 100;

            for (var i = 0; i < 12; i++) {
                if (i % 2 != 0) {
                    A[i] = value.substr(i, 1) * 2;
                    if (A[i] > 9)
                        A[i] = A[i] - 9;
                } else {
                    A[i] = value.substr(i, 1);
                }
                som = som + parseFloat(A[i]);
            }

            check = check - (som + 4);
            while (check >= 10) {
                check = check - 10;
            }

            if (check == value.substr(length - 1, 1))
                ok = true;
        }

        return ok;

    }
    function simcard_base_main(value) {
        var ok = false;
        var result;
        var res = 0;
        var serieNummer;
        var length = value.length;
        var totalsom = 0;
        var pos = 0;
        var temp = 0;
        var fois2 = 0;
        var ln_marque = 0;
        var ln_nomarque = 0;
        var lastIndex = length - 1;
        var validat_digit = value.substr(0, 6)
        if (length == 13 || length == 19) {
            if (length == 19) {
                serieNummer = value.substr(6, 12); // du 7? caract?re jusqu'a l'avant dernier
            } else {
                serieNummer = value.substr(0, 12); // allowing 13 digit number as well
            }
            res = Calculate1(serieNummer)
        }
        if (length == 13) {
            result = serieNummer + res;
        }
        if (length == 19) {
            result = validat_digit + serieNummer + res;
        }
        if (result == value) {
            ok = true;
        }
        return ok;
    }
    function simcard_others_main(value) {
        var ok = false;
        var value1 = false;
        var value2 = false;
        var value3 = false;

        value1 = simcar_telnet_main(value);
        value2 = simcard_base_main(value);
        value3 = simcard_proxi_main(value);
        if (value1 == true || value2 == true || value3 == true) {
            ok = true;
        }

        return ok;

    }
    function simcar_telnet_main(value) {
        var length = value.length;
        var ok = false;

        if (length != 19)
            ok = false;
        else
            ok = true;
        if ((((parseInt)(value / 10000000000000)) == 894101) || (((parseInt)(value / 10000000000000)) == 893207))
            ok = true;
        else
            ok = false;

        return ok;
    }
    jQuery('#nationality_other').on('click', function () {
        jQuery('#registered-yes').attr('checked', true);

    });
    $(".form-radio,.form-control,.form-checkbox,select").live('change', function () {
        var currentStep = jQuery('.item-step > .active').attr('id');
        if (typeof $(this).attr('name') != "undefined") {
            if ($(this).attr('name') == 'c_dob') {
                var shippingInformation = $('form#checkout_form').serializeArray();
                shippingInformation = JSON.stringify(shippingInformation);

                $.ajax({
                    url: $("#session_url").val(),
                    data: {saveData: shippingInformation, currentStep: currentStep, blurSession: true},
                    type: 'POST',
                    dataType: 'json'
                }).done(function (data) {
                    //dosomething
                });
            }
        }
    });

    $('.iew_contract').on("click", function () {
        var tvall = $(this).val();
        if (tvall == 1) {
            var itemId = $(this).attr('data-itemid');
            $('#iew_first_name_' + itemId + '-error').hide();
            $("#iew_first_name_" + itemId).removeClass('mage-error');
            $("#iew_last_name_" + itemId + "-error").hide();
            $("#iew_last_name_" + itemId).removeClass('mage-error');
            $("#iew_dob_" + itemId + "-error").hide();
            $("#iew_dob_" + itemId).removeClass('mage-error');
        }
    });

    $('.is_teneuro').on("click", function () {
        var tval = $(this).val();
        if (tval == 'yes') {
            var itemId = $(this).attr('data-itemid');
            $("#iew_contract_yes_" + itemId).trigger("click");
        }
        if (tval == 'no') {

            var itemId = $(this).attr('data-itemid');
            $("#iew_telephone_" + itemId + "-error").hide();
            $("#iew_telephone_" + itemId).removeClass('mage-error');
            $('#iew_first_name_' + itemId + '-error').hide();
            $("#iew_first_name_" + itemId).removeClass('mage-error');
            $("#iew_last_name_" + itemId + "-error").hide();
            $("#iew_last_name_" + itemId).removeClass('mage-error');
            $("#iew_dob_" + itemId + "-error").hide();
            $("#iew_dob_" + itemId).removeClass('mage-error');
        }
    });

    $('#ex_invoice_yes').on("click", function () {
        var invoice = $(this).val();
        if (invoice == 'yes') {
            $("#cu_ex_invoice_bill_in_name_yes").trigger("click");
        }
    });

    $(document).ready(function () {
        $(".current_operator [value='1']").attr('checked', 'checked');
        if ($('#step').val() != "step2")
        {
            $('#back_but').click(function () {
                parent.history.back();
                return false;


            });
        }
        $('.maxlengthcontact').unbind('keyup change input paste').bind('keyup change input paste', function (e) {
            var $this = $(this);
            var val = $this.val();
            var valLength = val.length;
            var maxCount = $this.attr('maxlength');
            if (valLength > maxCount) {
                $this.val($this.val().substring(0, maxCount));

            }
        });
        var timeout;
        var finishTimeout = false;

        $('.orange').on("mouseover", function () {
            var el = $(this);
            timeout = setTimeout(function () {
                finishTimeout = true;
                el.tooltip("open");
                finishTimeout = false;
            }, 1000);
        });
        $('.orange').mouseout(function () {
            clearTimeout(timeout);
        });
        var shippingInformationD = "";
        $(".form-control,.form-checkbox,select").on('blur', function () {
            var currentStep = jQuery('.item-step > .active').attr('id');
            if (typeof $(this).attr('name') != "undefined") {
                if ($(this).attr('name') != 'region_id' && $(this).attr('name') != 'country_id' && $(this).attr('name') != 'transfer_type' && $(this).attr('name') != 'account_number') {
                    var shippingInformation = $('form#checkout_form').serializeArray();

                    shippingInformation = JSON.stringify(shippingInformation);
                    if ((JSON.stringify(shippingInformationD) != shippingInformation))
                    {
                        if ($(this).val() != "")
                        {
                            shippingInformationD = $('form#checkout_form').serializeArray();
                            $.ajax({
                                url: $("#session_url").val(),
                                data: {saveData: shippingInformation, currentStep: currentStep, blurSession: true},
                                type: 'POST',
                                dataType: 'json'
                            }).done(function (data) {
                                //dosomething
                            });
                        }
                    }
                }
            }
        });
		/* defect 5901 - blur to click event */
		$(".form-radio").on('click', function () {
			var currentStep = jQuery('.item-step > .active').attr('id');
			if (typeof $(this).attr('name') != "undefined") {
				 var shippingInformation = $('form#checkout_form').serializeArray();
				 shippingInformation = JSON.stringify(shippingInformation);
				$.ajax({
					url: $("#session_url").val(),
					data: {saveData: shippingInformation, currentStep: currentStep, blurSession: true},
					type: 'POST',
					dataType: 'json'
				}).done(function (data) {
					//dosomething
				});					
			}
		});
		/* end defect 5901 */
    });
	/* defect 5901 */
	$(".removepayment-cart, .newpayment-cart").on('click', function () {
        var currentStep = jQuery('.item-step > .active').attr('id');
        var shippingInformation = $('form#checkout_form').serializeArray();
        shippingInformation = JSON.stringify(shippingInformation);
        $.ajax({
        url: $("#session_url").val(),
        data: {saveData: shippingInformation, currentStep: currentStep, blurSession: true},
        type: 'POST',
        showLoader: true,
        dataType: 'json'
        }).done(function (data) {
            window.location.href = $('#formatCartUrl').val();
            return false;
        });					
	});
	
	/* end defect 5901 */

    $(".current_operator").on('click', function () {
        $('.cuoperator .mage-error').empty();
        $('.cusimcard .mage-error').empty()
        $('.sim_custom_number').removeClass("mage-error");
        $('.simcard_number').removeClass("mage-error");
    });

    $(window).on("load", function () {
        if ($('#step').val() == "step2" && $('#step2_tab').hasClass("active")) {
            $("#step2_tab").trigger("click");
            window.location.hash = "details";
            jQuery("html, body").animate({scrollTop: 0}, "slow");
            return false;
        } else if ($('#step').val() == "final" && $('#step3_tab').hasClass("active")) {
            $("#step3_tab").trigger("click");
            window.location.hash = "paymentdetails";
            jQuery("html, body").animate({scrollTop: 0}, "slow");
            return false;
        } else if ($('#step').val() == "step1" && $('#step1_tab').hasClass("active")) {
            $("#step1_tab").trigger("click");
            window.location.hash = "number";
            jQuery("html, body").animate({scrollTop: 0}, "slow");
            return false;
        } else {
            if ($("#number_tab").hasClass("disable_div") || $('#step2_tab').hasClass("active")) {
                window.location.hash = "details";
                jQuery("html, body").animate({scrollTop: 0}, "slow");
                return false;
            } else {
                window.location.hash = "number";
                jQuery("html, body").animate({scrollTop: 0}, "slow");
                return false;
            }
        }

    });


    $(window).on('popstate', function (event) {
        var backValue = window.location.hash.substr(1);
        var finalvalue = $('#session_step').val();

        /* Defect 11067 - A warning message is NOT displayed where u can choose the closest orange shop */
        if ($('input:radio[name="nationality"]:checked').val() == 'other') {
            $('#error_div .mage-error').removeAttr("style");
        }
        /* Defect 11067 */

        if (backValue) {
            if (backValue == "number" && $('#step1_tab').hasClass("done") && finalvalue != "final") {
                $("#step1_tab").trigger("click");
                jQuery("html, body").animate({scrollTop: 0}, "slow");
                return false;
            } else if (backValue == "details" && $('#step2_tab').hasClass("done")) {
                $("#step2_tab").trigger("click");
                jQuery("html, body").animate({scrollTop: 0}, "slow");
                return false;
            } else if (backValue == "payment" && $('#step3_tab').hasClass("done")) {
                $("#step3_tab").trigger("click");
                jQuery("html, body").animate({scrollTop: 0}, "slow");
                return false;
            }
        } else {
            Window.history.back();
            return false;
        }
    });
    var city = [];
    // Limiting max 10 characters
    var bNumber = $('#b_number');
    bNumber.on('keyup', function (e) {
        var max = 10;
        if (bNumber.val().length >= max) {
            bNumber.val(bNumber.val().substr(0, max));
        }
    });

    var bBox = $('#b_box');
    bBox.on('keyup', function (e) {
        var max = 10;
        if (bBox.val().length >= max) {
            bBox.val(bBox.val().substr(0, max));
        }
    });
    $(document).ready(function () {
        if ($('.smartphone_propack').length > 0) {
            var smartpack = $('.smartphone_propack').val();
            var reductionpack = $('.reduction_propack').val();
            var surfpack = $('.surf_propack').val();
            if (smartpack != '') {
                var smartpack = smartpack.split(',');
                for (i = 0; i < smartpack.length; i++) {
                    $("input[data-propack='Smartphone ProPack']:not(.eagle_premium).eagle_" + smartpack[i]).trigger('click');
                    var n = $("input:checked.premium_pack.eagle_" + smartpack[i]).length;
                    $("input[type=checkbox]:not(:checked):not(.eagle_premium).premium_pack.eagle_" + smartpack[i]).prop('disabled', (n === 2) ? true : false);
                }
            }
            if (reductionpack != '') {
                var reductionpack = reductionpack.split(',');
                for (i = 0; i < reductionpack.length; i++) {
                    $("input[data-propack='Reduction ProPack']:not(.eagle_premium).eagle_" + reductionpack[i]).trigger('click');
                    var n = $("input:checked.premium_pack.eagle_" + reductionpack[i]).length;
                    $("input[type=checkbox]:not(:checked):not(.eagle_premium).premium_pack.eagle_" + reductionpack[i]).prop('disabled', (n === 2) ? true : false);
                }
            }
            if (surfpack != '') {
                var surfpack = surfpack.split(',');
                for (i = 0; i < surfpack.length; i++) {
                    $("input[data-propack='Surf ProPack']:not(.eagle_premium).eagle_" + surfpack[i]).trigger('click');
                    var n = $("input:checked.premium_pack.eagle_" + surfpack[i]).length;
                    $("input[type=checkbox]:not(:checked):not(.eagle_premium).premium_pack.eagle_" + surfpack[i]).prop('disabled', (n === 2) ? true : false);
                }
            }
        }
        $(".eagle_premium").on('change', function () {
            this.checked = !this.checked ? !false : true;
        });
        $('input[type=checkbox]:not(.eagle_premium).premium_pack').click(function () {
            var eagleClass = $(this).attr('class');
            var itemId = $(this).attr('data-itemid');
            var n = $("input:checked.premium_pack.eagle_" + itemId).length;
            $("input[type=checkbox]:not(:checked).premium_pack.eagle_" + itemId).prop('disabled', (n === 2) ? true : false);
        });
        $('input[type=checkbox]:not(.eagle_premium).propack_item').change(function () {
            var itemId = $(this).attr('data-itemid');
            var proPack = $(this).attr('data-propack');
            if ($(this).prop('checked')) {
                updatePropackItems(itemId, proPack);
            } else {
                if (proPack == 'Smartphone ProPack') {
                    $('.smartphone_propack').val($('.smartphone_propack').val().replace(itemId, ""));
                }
                if (proPack == 'Reduction ProPack') {
                    $('.reduction_propack').val($('.reduction_propack').val().replace(itemId, ""));
                }
                if (proPack == 'Surf ProPack') {
                    $('.surf_propack').val($('.surf_propack').val().replace(itemId, ""));
                }
            }
        });
        $('input[type=radio]:not(.eagle_premium).propack_item').click(function () {
            var itemId = $(this).attr('data-itemid');
            var proPack = $(this).attr('data-propack');
            var propackItems = '';
            if (proPack == 'Smartphone ProPack') {
                $('.smartphone_propack').val($('.smartphone_propack').val() + ',' + itemId);
                var reductionPropack = $('.reduction_propack').val();
                var surfPropack = $('.surf_propack').val();
                $('.reduction_propack').val(reductionPropack.replace(itemId, ""));
                $('.surf_propack').val(surfPropack.replace(itemId, ""));
            }
            if (proPack == 'Reduction ProPack') {
                $('.reduction_propack').val($('.reduction_propack').val() + ',' + itemId);
                var smartPropack = $('.smartphone_propack').val();
                var surfPropack = $('.surf_propack').val();
                $('.smartphone_propack').val(smartPropack.replace(itemId, ""));
                $('.surf_propack').val(surfPropack.replace(itemId, ""));
            }
            if (proPack == 'Surf ProPack') {
                $('.surf_propack').val($('.surf_propack').val() + ',' + itemId);
                var reductionPropack = $('.reduction_propack').val();
                var smartPropack = $('.smartphone_propack').val();
                $('.reduction_propack').val(reductionPropack.replace(itemId, ""));
                $('.smartphone_propack').val(smartPropack.replace(itemId, ""));
            }
        });
        function updatePropackItems(itemId, propack)
        {
            var propackItems = '';
            if (propack == 'Smartphone ProPack') {
                var smartphonePropackItems = $('.smartphone_propack').val();
                propackItems = smartphonePropackItems + ',' + itemId;
                $('.smartphone_propack').val(propackItems);
            }
            if (propack == 'Reduction ProPack') {
                var reductionPropackItems = $('.reduction_propack').val();
                propackItems = reductionPropackItems + ',' + itemId;
                $('.reduction_propack').val(propackItems);
            }
            if (propack == 'Surf ProPack') {
                var surfPropackItems = $('.surf_propack').val();
                propackItems = surfPropackItems + ',' + itemId;
                $('.surf_propack').val(propackItems);
            }
        }
        /** IEW **/
        $('input[type=checkbox].is_teneuro').click(function () {
            var itemId = $(this).attr('data-itemid');
            if ($(this).prop("checked") == true) {
                $(".iew_cont_" + itemId).show();
            } else {
                $(".iew_cont_" + itemId).hide();
            }
            if ($('#iew_contract_yes_' + itemId).is(':checked')) {
                $(".iew_contract_form_" + itemId).hide();
            }
        });
        $('input[type=radio].is_teneuro').click(function () {
            var itemId = $(this).attr('data-itemid');
            var iew_status = $(this).attr('value');
            if (iew_status == 'yes') {
                $(".iew_cont_" + itemId).show();
            } else {
                $(".iew_cont_" + itemId).hide();
            }
            if ($('#iew_contract_yes_' + itemId).is(':checked')) {
                $(".iew_contract_form_" + itemId).hide();
            }
        });
        $('input[type=radio].iew_contract').click(function () {
            var itemId = $(this).attr('data-itemid');
            var contract_status = $(this).attr('value');
            if (contract_status == 1) {
                $(".iew_contract_form_" + itemId).hide();
            } else {
                $(".iew_contract_form_" + itemId).show();
            }
        });
        /** Coupon Functionality **/
        $('body').on("keyup", ".orange-promocode", function (e) {
            $('#discount-code-fake').val($(this).val());
        });
        $('body').on("click", ".orange-promocode-submit", function (e) {
            $("#promocode-submit").click();
            return false;
        });
    });

    $('#i_ind_copm').change(function () {
        if ($(this).prop('checked')) {
            var drop = $('#tx_profile_dropdown').val();
            if (drop == "") {
                $('#tx_profile_dropdown').trigger('change');
                $("#checkbox_based_dropdown").css("display", "block");
                $("#checkbox_based_dropdown1").css("display", "block");
            } else {
                $("#checkbox_based_dropdown").css("display", "block");
                $("#checkbox_based_dropdown1").css("display", "block");
            }
        } else {
            $("#tx_profile_dropdown").val('');
            $("#legal_status").val('');
            $("#company_name").val('');
            $("#vat_number").val('');
            $("#checkbox_based_dropdown").css("display", "none");
            $("#checkbox_based_dropdown1").css("display", "none");
        }
    });

    $(document).ready(function () {
        var city = [];
        var zipcodeS = [];
        var streetWeb = [];
        var streetWebTwo = [];
        var cstreetWebTwo = [];
        var zipcodeWeb = [];
        var zipcodeWebTwo = [];
        var cityName = [];
        var cityName2 = [];
        if ($("#ex_invoice_no").is(":checked")) {
            $("#div_ex_invoice_cust_details").addClass("disable_div");
        }

        var zipcode = $("#b_postcode_city").val();
        /*Start of BZipcode Validation  */
        var delay = (function () {
            var timer = 0;
            return function (callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })();

        $("#b_postcode_city").keyup(function () {
            delay(function () {

                var zipcodeM = $('#b_postcode_city');
                var max = 4;
                if (zipcodeM.val().length >= max) {
                    zipcodeM.val(zipcodeM.val().substr(0, max));
                }
                var zipcode = $("#b_postcode_city").val();
                if (zipcode.length <= 3) {
                    $("#b_city").val('');
                    $("#b_street").val('');
                }
                if (zipcode.length == 3)
                {
                    $("#b_postcode_city").autocomplete({
                        source: function (req, responseFn) {
                            var re = $.ui.autocomplete.escapeRegex(req.term);
                            var matcher = new RegExp("^" + re, "i");
                            var a = $.grep(uniquezipcode(zipcodeWeb), function (item, index) {
                                return matcher.test(item);
                            });
                            responseFn(a);
                        },
                        minLength: 3,
                        select: function (event, ui) {
                            $("#b_zip_validation").text(" ");
                            $("#b_zipcode_city_validation").val("");
                        }
                    }).keyup(function (e) {

                    });
                }
                if (zipcode.length == 0) {
                    $("#b_zip_validation").text(" ");
                    $("#b_zipcode_city_validation").val("");
                }
                if (zipcode.length <= 4 && zipcode.length >= 2)
                {
                    $.ajax({
                        url: $("#road65url").val(),
                        type: "POST",
                        data: "zipcode=" + zipcode
                    }).done(function (result) {
                        cityName = [];
                        cityName = cityName.slice();
                        var data = $.parseJSON(result);
                        var obj = $.parseJSON(result);
                        for (var prop in obj) {
                            city.push(prop);
                            $.each(obj[prop], function (key, val) {
                                zipcodeS.push(val[0]);
                                cityName.push(val[1]);
                            });
                        }
                        zipcodeWeb = obj;
                        zipcodeWeb = $.unique(zipcodeS);
                        var uniqueZip = zipcodeWeb.filter(function (item, i, ar) {
                            return ar.indexOf(item) === i;
                        });
                        var uniqueCity = $.unique(cityName);

                        var currentCity = $("#b_city").val().toUpperCase();
                        var zipcode_foucs = $("#b_postcode_city").is(":focus");
                        if (zipcode.length == 3 && zipcode_foucs == true)
                        {
                            $('#b_postcode_city').autocomplete("search");
                            $("#b_postcode_city").autocomplete({
                                source: function (req, responseFn) {
                                    var re = $.ui.autocomplete.escapeRegex(req.term);
                                    var matcher = new RegExp("^" + re, "i");
                                    var a = $.grep(uniquezipcode(zipcodeWeb), function (item, index) {
                                        return matcher.test(item);
                                    });
                                    responseFn(a);
                                },
                                minLength: 3,
                                select: function (event, ui) {
                                    $("#b_zip_validation").text(" ");
                                    $("#b_zipcode_city_validation").val("");
                                }
                            }).keyup(function (e) {
                                if (e.which === 13) {

                                }
                            });
                        }

                    });
                }
            }, 100);
        });
       
        $("#b_city").keyup(function () {
            var cityValue = $("#b_city").val();
            var uniqueCity = cityName.filter(function (item, i, ar) {
                return ar.indexOf(item) === i;
            });

            $("#b_city").autocomplete({
                source: $.unique(uniqueCity),
                minLength: 3,
                select: function (event, ui) {
                    $("#b_city_validation").text(" ");
                }

            });
            if (cityValue.length == 0) {
                $("#b_city_validation").text(" ");
            }
            $("input:text[name='city']").val(cityValue);
            $("input:text[name='region']").val(cityValue);
        });
        /*Billing Street */
        function cityValidation()
        {
            var zipcode = $("#b_postcode_city").val();
            if (zipcode.length <= 4 && zipcode.length >= 2)
            {
                $.ajax({
                    url: $("#road65url").val(),
                    type: "POST",
                    data: "zipcode=" + zipcode
                }).done(function (result) {
                    cityName = [];
                    cityName = cityName.slice();
                    var data = $.parseJSON(result);
                    var obj = $.parseJSON(result);
                    for (var prop in obj) {
                        city.push(prop);
                        $.each(obj[prop], function (key, val) {
                            zipcodeS.push(val[0]);
                            cityName.push(val[1]);
                        });
                    }
                    zipcodeWeb = obj;
                    zipcodeWeb = $.unique(zipcodeS);
                    var uniqueZip = zipcodeWeb.filter(function (item, i, ar) {
                        return ar.indexOf(item) === i;
                    });
                    var uniqueCity = $.unique(cityName);

                    var currentCity = $("#b_city").val().toUpperCase();
                    var cityValue = $("#b_city").val();
                    var uniqueCity = cityName.filter(function (item, i, ar) {
                        return ar.indexOf(item) === i;
                    });
                    if (cityValue.length > 0) {
                        if (cityValue.length >= 2) {
                            if (cityName.indexOf(cityValue.toUpperCase()) == -1) {
                                $("#b_city_validation").text($.mage.__("City does not exist"));
                                $("#b_zipcode_city_validation").val("1");
                            } else {
                                $("#b_city_validation").text(" ");
                                $("#b_zipcode_city_validation").val("");
                            }
                        } else {
                            $("#b_city_validation").text($.mage.__("City does not exist"));
                            $("#b_zipcode_city_validation").val("1");
                        }
                    } else
                    {
                        $("#b_city_validation").text(" ");
                        $("#b_zipcode_city_validation").val("");
                    }
                    $("input:text[name='city']").val(cityValue);
                    $("input:text[name='region']").val(cityValue);
                });
            }
        }
        $("#b_city").change(function () {
            cityValidation();
        });
        $("#b_city").on("blur", function () {
            cityValidation();
        });
        /* ENd FOR ROAD 65 Billing City */
        /*Road 65 for Billing Street */
        $("#b_street").keyup(function (e) {
            delay(function () {

                if (e.keyCode != 16) {
                    var streetName = [];
                    var zipcode = $("#b_postcode_city").val();
                    var street = $("#b_street").val();
                    $("#b_street").autocomplete({
                        source: function (req, responseFn) {
                            var re = $.ui.autocomplete.escapeRegex(req.term);
                            var matcher = new RegExp("^" + re, "i");
                            var a = $.grep($.unique(streetWeb), function (item, index) {
                                return matcher.test(item);
                            });
                            responseFn($.unique(streetWeb));
                        },
                        minLength: 4,
                        select: function (event, ui) {
                            $("#street_validation").text(" ");
                            $("#b_street_validation").val('');
                        }
                    });
                    if (zipcode.length != 0) {
                        if (street.length >= 1)
                        {

                            streetName.splice(0, streetName.length);
                            var data = new Array();
                            $.ajax({
                                url: $("#road65url").val(),
                                type: "POST",
                                data: "zipcode=" + zipcode + "&street=" + street
                            }).done(function (result) {
                                var data = $.parseJSON(result);
                                var obj = $.parseJSON(result);
                                var streetName = [];
                                for (var prop in obj) {
                                    city.push(prop);
                                    $.each(obj[prop], function (key, val) {
                                        var tmp_street = val[1].toUpperCase();
                                        streetName.push(tmp_street);
                                    });
                                }
                                streetWeb = streetName;
                                var temp = $.unique((streetName));
                                var street_foucs = $("#b_street").is(":focus");
                                if (street.length >= 2 && street_foucs == true)
                                {

                                    $('#b_street').autocomplete("search");
                                    $("#b_street").autocomplete({
                                        source: function (req, responseFn) {
                                            var re = $.ui.autocomplete.escapeRegex(req.term);
                                            var matcher = new RegExp("^" + re, "i");
                                            var a = $.grep($.unique(streetWeb), function (item, index) {
                                                return matcher.test(item);
                                            });
                                            responseFn($.unique(streetWeb));
                                        },
                                        minLength: 3,
                                        select: function (event, ui) {
                                            $("#street_validation").text(" ");
                                            $("#b_street_validation").val('');
                                        }
                                    });

                                }



                            });
                        }
                    }
                }
            }, 100);
        });
        /**End of Btreet Ajax*/
        /* Change */
        function bstreetvalid()
        {

            var street = $("#b_street").val();
            if (street.length != 0)
            {
                var streetName = [];
                var zipcode = $("#b_postcode_city").val();
                if (zipcode.length != 0) {
                    if (street.length >= 1)
                    {
                        if ($("#b_street").hasClass("ui-autocomplete-input")) {
                            $('#b_street').autocomplete('close');
                        }
                        streetName.splice(0, streetName.length);
                        var data = new Array();
                        $.ajax({
                            url: $("#road65url").val(),
                            type: "POST",
                            data: "zipcode=" + zipcode + "&street=" + street
                        }).done(function (result) {
                            var data = $.parseJSON(result);
                            var obj = $.parseJSON(result);
                            for (var prop in obj) {
                                city.push(prop);
                                $.each(obj[prop], function (key, val) {
                                    var tmp_street = val[1].toUpperCase();
                                    streetName.push(tmp_street);
                                });
                            }
                            streetWeb = streetName;
                            var temp = $.unique((streetName));
                            var street1 = $("#b_street").val().toUpperCase();
                            if (street1.length != 0)
                            {
                                if ($.inArray(street1, streetWeb) == -1)
                                {
                                    $("#street_validation").text($.mage.__("Street doesn't exist"));
                                    $("#b_street-error").hide();
                                    $("#b_street_validation").val('1');
                                } else
                                {
                                    $("#street_validation").text("");
                                    $("#b_street_validation").val('');
                                }
                            } else
                            {
                                $("#street_validation").text("");
                                $("#b_street_validation").val('');
                            }


                        });
                    }
                }

            } else
            {
                $("#street_validation").text(" ");
                $("#b_street_validation").val('');
            }

        }
        $("#b_street").on("change", function () {
            delay(function () {
                bstreetvalid();
            }, 100);
        });
        $("#b_street").on("blur", function () {
            delay(function () {
                bstreetvalid();
            }, 100);
        });
        function zipcodevalid()
        {
            $('#b_postcode_city').autocomplete('close');
            var zipcode = $("#b_postcode_city").val();
            getzipCityNames();
            if (zipcode.length != 4 && zipcode.length > 0) {
                $("#b_zip_validation").text($.mage.__("Zipcode doesn't exist"));
                $("#b_zipcode_validation").val('');
                $("#b_postcode_city-error").hide();
            } else
            {
                if (zipcode.length != 0)
                {
                    if ($.inArray(zipcode, zipcodeWeb) == -1)
                    {

                        $("#b_zip_validation").text($.mage.__("Zipcode doesn't exist"));
                        $("#b_zipcode_validation").val('');
                        $("#b_postcode_city-error").hide();
                    } else
                    {
                        $("#b_zip_validation").text("");
                        $("#b_zipcode_validation").val('');
                        $("#b_postcode_city-error").hide();
                    }
                }

            }
        }
        $("#b_postcode_city").on("blur", function () {
            delay(function () {
                getzipCityNames();
            }, 100);
        });

        $("#b_postcode_city").on("change", function () {
            delay(function () {
                getzipCityNames();
            }, 100);
        });

        /*End of BZipcode Validation  */

        /*Road 65 for Billing City */

        function getzipCityNames()
        {
            var zipcode = $("#b_postcode_city").val();
            if (zipcode.length == 0) {
                $("#b_zip_validation").text(" ");
                $("#b_zipcode_city_validation").val("");
            }
            if (zipcode.length <= 4 && zipcode.length >= 2)
            {
                $.ajax({
                    url: $("#road65url").val(),
                    type: "POST",
                    data: "zipcode=" + zipcode
                }).done(function (result) {
                    cityName = [];
                    cityName = cityName.slice();
                    var data = $.parseJSON(result);
                    var obj = $.parseJSON(result);
                    for (var prop in obj) {
                        city.push(prop);
                        $.each(obj[prop], function (key, val) {
                            zipcodeS.push(val[0]);
                            cityName.push(val[1]);
                        });
                    }
                    zipcodeWeb = obj;
                    zipcodeWeb = $.unique(zipcodeS);
                    var uniqueZip = zipcodeWeb.filter(function (item, i, ar) {
                        return ar.indexOf(item) === i;
                    });
                    var uniqueCity = $.unique(cityName);

                    var currentCity = $("#b_city").val().toUpperCase();
                    if ($("#b_postcode_city").hasClass("ui-autocomplete-input")) {
                        $('#b_postcode_city').autocomplete('close');
                    }
                    var zipcode = $("#b_postcode_city").val();
                    if (zipcode.length != 4 && zipcode.length > 0) {
                        $("#b_zip_validation").text($.mage.__("Zipcode doesn't exist"));
                        $("#b_zipcode_validation").val('1');
                        $("#b_postcode_city-error").hide();
                    } else
                    {
                        if (zipcode.length != 0)
                        {
                            if ($.inArray(zipcode, zipcodeWeb) == -1)
                            {

                                $("#b_zip_validation").text($.mage.__("Zipcode doesn't exist"));
                                $("#b_zipcode_validation").val('1');
                                $("#b_postcode_city-error").hide();
                            } else
                            {
                                $("#b_zip_validation").text("");
                                $("#b_zipcode_validation").val('');
                                $("#b_postcode_city-error").hide();
                            }
                        }

                    }

                });
            } else if (zipcode.length == 1)
            {
                $("#b_zip_validation").text($.mage.__("Zipcode doesn't exist"));
                $("#b_zipcode_validation").val('1');
                $("#b_postcode_city-error").hide();
            }

        }
        function uniquezipcode(array) {
            return $.grep(array, function (el, index) {
                return index == $.inArray(el, array);
            });
        }

        /**End of Btreet Ajax*/
        /* Shipping Post code**/
        $("#s_postcode_city").keyup(function () {
            delay(function () {
                var zipcodeM = $('#s_postcode_city');
                var max = 4;
                if (zipcodeM.val().length >= max) {
                    zipcodeM.val(zipcodeM.val().substr(0, max));
                }
                var zipcode = $("#s_postcode_city").val();
                if (zipcode.length <= 3) {
                    $("#s_edit_city").val('');
                    $("#s_street").val('');
                }
                $("#s_postcode_city").autocomplete({
                    source: function (req, responseFn) {
                        var re = $.ui.autocomplete.escapeRegex(req.term);
                        var matcher = new RegExp("^" + re, "i");
                        var a = $.grep(uniquezipcode(zipcodeWebTwo), function (item, index) {
                            return matcher.test(item);
                        });
                        responseFn(a);
                    },
                    minLength: 3,
                    select: function (event, ui) {
                        $("#s_zip_validation").text(" ");
                        $("#s_zipcode_validation").val('');
                    }
                });
                if (zipcode.length == 0) {
                    $("#s_zip_validation").text(" ");
                    $("#s_zipcode_validation").val('');
                }
                if (zipcode.length <= 4 && zipcode.length >= 2)
                {
                    $.ajax({
                        url: $("#road65url").val(),
                        type: "POST",
                        data: "zipcode=" + zipcode
                    }).done(function (result) {
                        cityName2 = [];
                        cityName2 = cityName.slice();
                        var data = $.parseJSON(result);
                        var obj = $.parseJSON(result);
                        for (var prop in obj) {
                            city.push(prop);
                            $.each(obj[prop], function (key, val) {
                                zipcodeS.push(val[0]);
                                cityName2.push(val[1]);
                            });
                        }
                        zipcodeWebTwo = obj;
                        zipcodeWebTwo = $.unique(zipcodeS);
                        var uniqueZipTwo = zipcodeWebTwo.filter(function (item, i, ar) {
                            return ar.indexOf(item) === i;
                        });
                        var uniqueCityTwo = $.unique(cityName2);

                        var currentCity = $("#s_edit_city").val().toUpperCase();
                        var szipcode_foucs = $("#s_postcode_city").is(":focus");
                        if (zipcode.length == 3 && szipcode_foucs == true)
                        {
                            $('#s_postcode_city').autocomplete("search");
                            $("#s_postcode_city").autocomplete({
                                source: function (req, responseFn) {
                                    var re = $.ui.autocomplete.escapeRegex(req.term);
                                    var matcher = new RegExp("^" + re, "i");
                                    var a = $.grep(uniquezipcode(zipcodeWebTwo), function (item, index) {
                                        return matcher.test(item);
                                    });
                                    responseFn(a);
                                },
                                minLength: 3,
                                select: function (event, ui) {
                                    $("#s_zip_validation").text(" ");
                                    $("#s_zipcode_validation").val('');
                                }
                            }).keyup(function (e) {
                                if (e.which === 13) {
                                    $(".ui-menu-item").hide();
                                }
                            });
                        }
                    });
                }
            }, 100);
        });

        $("#s_edit_city").keyup(function () {

            var cityValue = $("#s_edit_city").val();
            var uniqueCity = cityName2.filter(function (item, i, ar) {
                return ar.indexOf(item) === i;
            });

            $("#s_edit_city").autocomplete({
                source: $.unique(uniqueCity),
                minLength: 3,
                select: function (event, ui) {
                    $("#s_city_validation").text(" ");
                    $("#s_zipcode_city_validation").val("");
                }

            });
            if (cityValue.length == 0) {
                $("#s_city_validation").text(" ");
            }
            $("input:text[name='city']").val(cityValue);
            $("input:text[name='region']").val(cityValue);
        });
        function shipcityValidation()
        {
            var zipcode = $("#s_postcode_city").val();
            if (zipcode.length <= 4 && zipcode.length >= 2)
            {
                if ($("#s_postcode_city").hasClass("ui-autocomplete-input")) {
                    $('#s_postcode_city').autocomplete('close');
                }
                $.ajax({
                    url: $("#road65url").val(),
                    type: "POST",
                    data: "zipcode=" + zipcode
                }).done(function (result) {
                    cityName2 = [];
                    cityName2 = cityName2.slice();
                    var data = $.parseJSON(result);
                    var obj = $.parseJSON(result);
                    for (var prop in obj) {
                        city.push(prop);
                        $.each(obj[prop], function (key, val) {
                            zipcodeS.push(val[0]);
                            cityName2.push(val[1]);
                        });
                    }
                    zipcodeWebTwo = obj;
                    zipcodeWebTwo = $.unique(zipcodeS);
                    var uniqueZipTwo = zipcodeWebTwo.filter(function (item, i, ar) {
                        return ar.indexOf(item) === i;
                    });
                    var uniqueCityTwo = $.unique(cityName2);

                    var currentCity = $("#s_edit_city").val().toUpperCase();
                    var zipcode = $("#s_postcode_city").val();
                    var cityValue = $("#s_edit_city").val();
                    if (cityValue.length > 0) {
                        var uniqueCity = cityName2.filter(function (item, i, ar) {
                            return ar.indexOf(item) === i;
                        });
                        if (cityValue.length >= 1) {
                            if (cityName2.indexOf(cityValue.toUpperCase()) == -1) {
                                $("#s_city_validation").text($.mage.__("City does not exist"));
                                $("#s_zipcode_city_validation").val("1");
                            } else {
                                $("#s_city_validation").text(" ");
                                $("#s_zipcode_city_validation").val("");
                            }
                        } else {
                            $("#s_city_validation").text(" ");
                            $("#s_zipcode_city_validation").val("");
                        }
                    } else
                    {
                        $("#s_city_validation").text(" ");
                        $("#s_zipcode_city_validation").val("");
                    }
                    $("input:text[name='city']").val(cityValue);
                    $("input:text[name='region']").val(cityValue);

                });
            }


        }
        $("#s_edit_city").change(function () {
            delay(function () {
                shipcityValidation();
            }, 100);
        });
        $("#s_edit_city").on("blur", function () {
            delay(function () {
                shipcityValidation();
            }, 100);
        });
        function scityvalidation()
        {
            var zipcode = $("#s_postcode_city").val();
            getshipzipCityNames();
            if (zipcode.length != 4 && zipcode.length > 0) {
                $("#s_city_validation").text($.mage.__("City does not exist"));
                $("#s_zipcode_validation").val('1');
                $("#s_postcode_city-error").hide();

            } else
            {
                if (zipcode.length != 0)
                {
                    if ($.inArray(zipcode, zipcodeWebTwo) == -1)
                    {
                        $("#s_city_validation").text($.mage.__("City does not exist"));
                        $("#b_zipcode_validation").val('');
                        $("#b_postcode_city-error").hide();
                    }
                }

            }
        }
        $("#s_postcode_city").on("blur", function () {
            delay(function () {
                getshipzipCityNames();
            }, 100);
        });

        $("#s_postcode_city").on("change", function () {
            delay(function () {
                getshipzipCityNames();
            }, 100);
        });
        function getshipzipCityNames()
        {

            var zipcode = $("#s_postcode_city").val();
            if (zipcode.length <= 4 && zipcode.length >= 2)
            {
                if ($("#s_postcode_city").hasClass("ui-autocomplete-input")) {
                    $('#s_postcode_city').autocomplete('close');
                }
                $.ajax({
                    url: $("#road65url").val(),
                    type: "POST",
                    data: "zipcode=" + zipcode
                }).done(function (result) {
                    cityName2 = [];
                    cityName2 = cityName2.slice();
                    var data = $.parseJSON(result);
                    var obj = $.parseJSON(result);
                    for (var prop in obj) {
                        city.push(prop);
                        $.each(obj[prop], function (key, val) {
                            zipcodeS.push(val[0]);
                            cityName2.push(val[1]);
                        });
                    }
                    zipcodeWebTwo = obj;
                    zipcodeWebTwo = $.unique(zipcodeS);
                    var uniqueZipTwo = zipcodeWebTwo.filter(function (item, i, ar) {
                        return ar.indexOf(item) === i;
                    });
                    var uniqueCityTwo = $.unique(cityName2);

                    var currentCity = $("#s_edit_city").val().toUpperCase();
                    var zipcode = $("#s_postcode_city").val();

                    if (zipcode.length != 4 && zipcode.length > 0) {
                        $("#s_zip_validation").text($.mage.__("City does not exist"));
                        $("#s_zipcode_validation").val('1');
                        $("#s_postcode_city-error").hide();

                    } else
                    {
                        if (zipcode.length != 0)
                        {

                            if ($.inArray(zipcode, zipcodeWebTwo) == -1)
                            {

                                $("#s_zip_validation").text($.mage.__("City does not exist"));
                                $("#s_zipcode_validation").val('1');
                                $("#s_postcode_city-error").hide();
                            } else
                            {
                                $("#s_zip_validation").text("");
                                $("#s_zipcode_validation").val('');
                                $("#s_postcode_city-error").hide();
                            }
                        } else
                        {
                            $("#s_zip_validation").text("");
                            $("#s_zipcode_validation").val('');
                            $("#s_postcode_city-error").hide();

                        }

                    }

                });
            } else if (zipcode.length == 1)
            {
                $("#s_zip_validation").text($.mage.__("City does not exist"));
                $("#s_zipcode_validation").val('1');
                $("#s_postcode_city-error").hide();
            }

        }
        /**End of Shipping Postcode Code **/
        $("#s_street").keyup(function (e) {
            delay(function () {
                if (e.keyCode != 16) {
                    var streetName = [];
                    var zipcode = $("#s_postcode_city").val();
                    var street = $("#s_street").val();
                    $("#s_street").autocomplete({
                        source: function (req, responseFn) {
                            var re = $.ui.autocomplete.escapeRegex(req.term);
                            var matcher = new RegExp("^" + re, "i");
                            var a = $.grep($.unique(streetWebTwo), function (item, index) {
                                return matcher.test(item);
                            });
                            responseFn($.unique(streetWebTwo));
                        },
                        minLength: 4,
                        select: function (event, ui) {
                            $("#street_validation_s").text(" ");
                            $("#s_street_validation").val('');
                        }
                    });
                    if (zipcode.length != 0) {
                        if (street.length >= 1)
                        {

                            streetName.splice(0, streetName.length);
                            var data = new Array();
                            $.ajax({
                                url: $("#road65url").val(),
                                type: "POST",
                                data: "zipcode=" + zipcode + "&street=" + street
                            }).done(function (result) {
                                var data = $.parseJSON(result);
                                var obj = $.parseJSON(result);
                                var streetName = [];
                                for (var prop in obj) {
                                    city.push(prop);
                                    $.each(obj[prop], function (key, val) {
                                        var tmp_street = val[1].toUpperCase();
                                        streetName.push(tmp_street);
                                    });
                                }
                                streetWebTwo = streetName;
                                var temp = $.unique((streetName));

                                var street_foucs = $("#s_street").is(":focus");
                                if (street.length >= 2 && street_foucs == true)
                                {
                                    $('#s_street').autocomplete("search");
                                    $("#s_street").autocomplete({
                                        source: function (req, responseFn) {
                                            var re = $.ui.autocomplete.escapeRegex(req.term);
                                            var matcher = new RegExp("^" + re, "i");
                                            var a = $.grep($.unique(streetWebTwo), function (item, index) {
                                                return matcher.test(item);
                                            });
                                            responseFn($.unique(streetWebTwo));
                                        },
                                        minLength: 3,
                                        select: function (event, ui) {
                                            $("#street_validation_s").text(" ");
                                            $("#s_street_validation").val('');
                                        }
                                    });

                                }

                            });
                        }
                    }
                }
            }, 100);
        });
        /**End of Btreet Ajax*/
        /* Change */
        function sstreetvalidation()
        {

            var s_street = $("#s_street").val();
            if (s_street.length != 0)
            {
                var streetName = [];
                var zipcode = $("#s_postcode_city").val();
                if (zipcode.length != 0) {
                    if (s_street.length >= 1)
                    {
                        if ($("#s_street").hasClass("ui-autocomplete-input")) {
                            $('#s_street').autocomplete('close');
                        }

                        streetName.splice(0, streetName.length);
                        var data = new Array();
                        $.ajax({
                            url: $("#road65url").val(),
                            type: "POST",
                            data: "zipcode=" + zipcode + "&street=" + s_street
                        }).done(function (result) {
                            var data = $.parseJSON(result);
                            var obj = $.parseJSON(result);
                            for (var prop in obj) {
                                city.push(prop);
                                $.each(obj[prop], function (key, val) {
                                    var tmp_street = val[1].toUpperCase();
                                    streetName.push(tmp_street);
                                });
                            }
                            streetWebTwo = streetName;
                            var temp = $.unique((streetName));
                            var s_street = $("#s_street").val().toUpperCase();
                            if ($.inArray(s_street, streetWebTwo) == -1)
                            {
                                $("#street_validation_s").text($.mage.__("Street doesn't exist"));
                                $("#s_street_validation").val('1');
                                $("#s_street-error").hide();
                            } else
                            {
                                $("#street_validation_s").text("");
                                $("#s_street_validation").val('');
                            }
                        });
                    }
                }
            } else
            {
                $("#street_validation_s").text(" ");
                $("#s_street_validation").val('');
            }
        }
        $("#s_street").on("change", function () {
            delay(function () {
                sstreetvalidation();
            }, 100);
        });
        $("#s_street").on("blur", function () {
            delay(function () {
                sstreetvalidation();
            }, 100);
        });

    });
    //road65 end
    //Simcard validation starts
    $(".simcard_number").on("keyup", function (e) {
        var start = this.selectionStart,
            end = this.selectionEnd;
            this.value = this.value.replace(/[^0-9.]/g, '');
            this.setSelectionRange(start, end);
        var error = 0;
        var myattrValue = $(this).attr("data-myAttri");
        var value = $(this).val();
        var length1 = value.length;

        var idstring = $(this).attr("id");
        var id = idstring.split('simcard_number_');
        $("#simcard_number-" + id[1] + "-error").hide();
        $("#simcard_number-" + id[1] + "-error").empty();
        if (value != "") {
            if (length1 > 12) {

                $('#simcard_number-' + id[1] + '-error').hide();
                $("#simcard_number_" + id[1]).removeClass('mage-error');

                if (myattrValue == 'Proximus') {
                    var result_prox = simcard_proxi(value);
                    if (!result_prox) {
                        error = 1;
                        $('#simcard_number-' + id[1] + '-error').show();
                        $('#simcard_number-' + id[1] + '-error').empty();
                        $("#simcard_number_" + id[1]).removeClass('form-control validation_check simcard_number');
                        $("#simcard_number_" + id[1]).addClass('form-control simcard_number mage-error');
                        $('#simcard_number-' + id[1] + '-error').html($.mage.__('Current SIM card number and operator not matched.'));
                        return false;
                    } else {
                        $("#simcard_number-" + id[1] + "-error").hide();
                        $("#simcard_number-" + id[1] + "-error").empty();
                    }
                } else if (myattrValue == 'Base') {
                    var result_base = simcard_base(value);
                    if (!result_base) {
                        error = 1;
                        $('#simcard_number-' + id[1] + '-error').show();
                        $('#simcard_number-' + id[1] + '-error').empty();
                        $("#simcard_number_" + id[1]).removeClass('form-control validation_check simcard_number');
                        $("#simcard_number_" + id[1]).addClass('form-control simcard_number mage-error');
                        $('#simcard_number-' + id[1] + '-error').html($.mage.__('Current SIM card number and operator not matched.'));
                        return false;
                    } else {
                        $("#simcard_number-" + id[1] + "-error").hide();
                        $("#simcard_number-" + id[1] + "-error").empty();
                    }
                } else if (myattrValue == 'Other' || myattrValue == 'Autre') {
                    var result_others = simcard_others(value);
                    if (!result_others) {
                        error = 1;
                        $('#simcard_number-' + id[1] + '-error').show();
                        $('#simcard_number-' + id[1] + '-error').empty();
                        $("#simcard_number_" + id[1]).removeClass('form-control validation_check simcard_number');
                        $("#simcard_number_" + id[1]).addClass('form-control simcard_number mage-error');
                        $('#simcard_number-' + id[1] + '-error').html($.mage.__('Current SIM card number and operator not matched.'));
                        return false;
                    } else {
                        $("#simcard_number-" + id[1] + "-error").hide();
                        $("#simcard_number-" + id[1] + "-error").empty();
                    }
                } else if (myattrValue == 'Telenet') {
                    var result_telnet = simcar_telnet(value);
                    if (!result_telnet) {
                        error = 1;
                        $('#simcard_number-' + id[1] + '-error').show();
                        $('#simcard_number-' + id[1] + '-error').empty();
                        $("#simcard_number_" + id[1]).removeClass('form-control validation_check simcard_number');
                        $("#simcard_number_" + id[1]).addClass('form-control simcard_number mage-error');
                        $('#simcard_number-' + id[1] + '-error').html($.mage.__('Current SIM card number and operator not matched.'));
                        return false;
                    } else {
                        $("#simcard_number-" + id[1] + "-error").hide();
                        $("#simcard_number-" + id[1] + "-error").empty();
                    }
                }
                $("#simcard_number-" + id[1] + "-error").hide();
                $("#simcard_number-" + id[1] + "-error").empty();


                if (!$("#simcard_number_" + id[1]).hasClass('mage-error')) {
                    var data = {sim_number: value, providers: myattrValue};
                    $.ajax({
                        url: $("#simcard_url").val(),
                        type: "POST",
                        showLoader: true,
                        data: data,
                        success: function (result) {
                            if (result == 'Current SIM card number and operator not matched.') {
                                $('#simcard_number-' + id[1] + '-error').show();
                                $('#simcard_number-' + id[1] + '-error').empty();
                                $("#simcard_number_" + id[1]).removeClass('form-control validation_check simcard_number');
                                $("#simcard_number_" + id[1]).addClass('form-control simcard_number mage-error');
                                $('#simcard_number-' + id[1] + '-error').html($.mage.__('Current SIM card number and operator not matched.'));
                                error = 1;
                                return false;
                            } else {
                                error = 0;
                                $('#simcard_number-' + id[1] + '-error').hide();
                                $('#simcard_number-' + id[1] + '-error').empty();
                                $("#simcard_number_" + id[1]).addClass('form-control validation_check simcard_number');
                                $("#simcard_number-" + id[1]).removeClass('mage-error');
                            }
                        }
                    });

                }
            }
        }
        $(checkout_form).validation(); 
        var validate = $.validator.validateElement(this);
        if (validate == false) {
            return false;
        }
        if (error == 0) {
            $("#simcard_number-" + id[1] + "-error").hide();
            $("#simcard_number-" + id[1] + "-error").empty();
            if (e.relatedTarget && e.relatedTarget.id != "undefined" && e.relatedTarget.id == "step_1_number") {

                $('#step_1_number').trigger('click');
            }
        }
        e.preventDefault();

    });
    function Calculate(Luhn) {
        var sum = 0;
        for (i = 0; i < Luhn.length; i++) {
            sum += parseInt(Luhn.substring(i, i + 1));
        }
        var delta = new Array(0, 1, 2, 3, 4, -4, -3, -2, -1, 0);
        for (i = Luhn.length - 1; i >= 0; i -= 2) {
            var deltaIndex = parseInt(Luhn.substring(i, i + 1));
            var deltaValue = delta[deltaIndex];
            sum += deltaValue;
        }
        var mod10 = sum % 10;
        mod10 = 10 - mod10;
        if (mod10 == 10) {
            mod10 = 0;
        }
        return mod10;
    }
    function simcard_proxi(values) {
        var ok = false;
        var value = values.toString();
        var length = value.length;

        if (length == '13') {
            var A = [];
            var som = 0;
            var check = 100;

            for (var i = 0; i < 12; i++) {
                if (i % 2 != 0) {
                    A[i] = value.substr(i, 1) * 2;
                    if (A[i] > 9)
                        A[i] = A[i] - 9;
                } else {
                    A[i] = value.substr(i, 1);
                }
                som = som + parseFloat(A[i]);
            }

            check = check - (som + 4);
            while (check >= 10) {
                check = check - 10;
            }

            if (check == value.substr(length - 1, 1))
                ok = true;
        }

        return ok;

    }
    function simcard_base(value) {
        var ok = false;
        var result;
        var res = 0;
        var serieNummer;
        var length = value.length;
        var totalsom = 0;
        var pos = 0;
        var temp = 0;
        var fois2 = 0;
        var ln_marque = 0;
        var ln_nomarque = 0;
        var lastIndex = length - 1;
        var validat_digit = value.substr(0, 6)
        if (length == 13 || length == 19) {
            if (length == 19) {
                serieNummer = value.substr(6, 12); // du 7? caract?re jusqu'a l'avant dernier
            } else {
                serieNummer = value.substr(0, 12); // allowing 13 digit number as well
            }
            res = Calculate(serieNummer)
        }
        if (length == 13) {
            result = serieNummer + res;
        }
        if (length == 19) {
            result = validat_digit + serieNummer + res;
        }
        if (result == value) {
            ok = true;
        }
        return ok;
    }


    function simcard_others(value) {
        var ok = false;
        var value1 = false;
        var value2 = false;
        var value3 = false;

        value1 = simcar_telnet(value);
        value2 = simcard_base(value);
        value3 = simcard_proxi(value);
        if (value1 == true || value2 == true || value3 == true) {
            ok = true;
        }

        return ok;

    }

    function simcar_telnet(value) {
        var length = value.length;
        var ok = false;

        if (length != 19)
            ok = false;
        else
            ok = true;
        if ((((parseInt)(value / 10000000000000)) == 894101) || (((parseInt)(value / 10000000000000)) == 893207))
            ok = true;
        else
            ok = false;

        return ok;
    }

    $(".simcard_type_custom_value").on("change", function () {
        var idstring = $(this).attr("id");
        var id = idstring.split('subscription_type_');
        var simId = "simcard_number_" + id[1];
        $('#simcard_number-' + id[1] + '-error').hide();
        $("#simcard_number_" + id[1]).val('');
        $("#simcard_number_" + id[1]).attr('data-myAttri', '');
        $("#simcard_number_" + id[1]).attr('data-myAttri', $(this).val());
        $('#simcard_number_' + id[1] + '-error').hide();
        $('#simcard_number_' + id[1] + '-error').empty();
        $("#simcard_number_" + id[1]).removeClass('form-control mage-error simcard_number');
        $("#simcard_number_" + id[1]).addClass('form-control validation_check simcard_number');
        
        var tealium_status = $('input[name="tealium_status"]').val();
        if (tealium_status > 0) {
          // Number Tab.
          var current_operator = $(this).val();
          $('#tealium_current_operator').val(current_operator);
          var tealium_data = $('input[name="tealium_values"]').val();
          var d = $.parseJSON(tealium_data);
          d.page_name = "checkout number";
          d.checkout_step = "number";
          d.number_type = ["existing"];
          d.current_operator = current_operator.toLowerCase();
          utag.view(d);
        }
    });

    //Validate Customer Number 
    $("#cu_ex_invoice_cust_number").on("blur", function () {
        $('form#checkout_form').validation();
        var cust_number_blur_validation = $.validator.validateElement($("#cu_ex_invoice_cust_number"));
        $('#cust_number_validation_error').hide();
        if ($(this).val() && cust_number_blur_validation == true) {
            var num = $(this).val();
            cutomernumber_validation(num);
        }
    });
	
	function cutomernumber_validation(numbervalue) {
			var design_te_existing_number_chk = numbervalue;
			
		    var customernumber_url = $("#customernumber_url").val();
					  
	      $.ajax({
           url: customernumber_url,
           data: {design_te_existing_number_chk:design_te_existing_number_chk },
           type: 'get',
           dataType: 'json',
           showLoader: true,
           context: $('#step_1')
           }).done(function(data){
		   if(data == 'yes'){
			    $('#cust_number_validation_error').hide();
				$("#cu_ex_invoice_cust_number").removeClass("mage-error");
				$("#cu_ex_invoice_cust_number_hidden").val('');
			   return true;
		    }else{
			   $('#cust_number_validation_error').show();
			   $("#cu_ex_invoice_cust_number").addClass("mage-error");
			   $("#cu_ex_invoice_cust_number_hidden").val('1');
			   return false;
		   }		   
		   }).fail(function(data){
			 $("#cu_ex_invoice_cust_number").addClass("mage-error");
			 $("#cu_ex_invoice_cust_number_hidden").val('1');
			 $('#cust_number_validation_error').show();
			  return false;
		});
   }

    $(".iew_customer_number").on("blur", function () {
        if($(this).hasClass('form-control')) { 
            $('form#checkout_form').validation();
            var itemId = $(this).attr('data-itemid');
            var cust_number_blur_validation = $.validator.validateElement($("#iew_telephone_"+itemId));
            $("#iew_telephone_"+itemId+'_error').hide();
            if ($(this).val() && cust_number_blur_validation == true) {
                var num = $(this).val();
                iew_cutomernumber_validation(num,itemId);
            }
        }
    });

    function iew_cutomernumber_validation(numbervalue,itemId) {
        var design_te_existing_number_chk = numbervalue;
        var customernumber_url = $("#customernumber_url").val();
        $.ajax({
            url: customernumber_url,
            data: {design_te_existing_number_chk:design_te_existing_number_chk },
            type: 'get',
            dataType: 'json',
            showLoader: true,
            context: $('#step_1')
        }).done(function(data){
            if(data == 'yes'){
                $("#iew_telephone_"+itemId+'_error').hide();
                $("#iew_telephone_"+itemId).removeClass("mage-error");
                $("#iew_telephone_"+itemId+"_hidden").val('');
                $("#iew_telephone_"+itemId+"_hidden").blur(function(){});
                $("#iew_telephone_"+itemId+"_hidden").trigger('blur');
                return true;
            }else{
                $("#iew_telephone_"+itemId+'_error').show();
                $("#iew_telephone_"+itemId).addClass("mage-error");
                $("#iew_telephone_"+itemId+"_hidden").val('1');
                $("#iew_telephone_"+itemId+"_hidden").blur(function(){});
                $("#iew_telephone_"+itemId+"_hidden").trigger('blur');
                return false;
            }           
        }).fail(function(data){
            $("#iew_telephone_"+itemId).addClass("mage-error");
            $("#iew_telephone_"+itemId+"_hidden").val('1');
            $("#iew_telephone_"+itemId+'_error').show();
            $("#iew_telephone_"+itemId+"_hidden").blur(function(){});
            $("#iew_telephone_"+itemId+"_hidden").trigger('blur');
            return false;
        });
   }
    function get_number_type_value() {
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
      return tealium_number_type;
    }
//end Customer Number Validation

    $("#vat_number").on("blur", function () {
        $('form#checkout_form').validation();
        var vat_number_validation = $.validator.validateElement($("#vat_number"));
        if (vat_number_validation == false) {
            $('#vat_number_validation_error').html('');
        }
        if ($(this).val() && vat_number_validation == true) {
            var data = {vat_number: $(this).val()};
            $.ajax({
                url: $("#vatnumber_url").val(),
                type: "POST",
                showLoader: true,
                data: data,
                success: function (result) {
                    if (result == 'Key not valid or not exist') {
                        $('#vat_number_validation_error').html($.mage.__('Wrong format entered'));
                        $('#vat_number').addClass('mage-error');

                    } else {
                        $('#vat_number_validation_error').html('');
                        $('#vat_number').removeClass('mage-error');
                    }
                },
                statusCode: {
                    503: function (xhr) {
                        $('#vat_number_validation_error').html($.mage.__('Wrong format entered'));
                        $('#vat_number').addClass('mage-error');
                    }
                }
            });
        }

    });

});
 
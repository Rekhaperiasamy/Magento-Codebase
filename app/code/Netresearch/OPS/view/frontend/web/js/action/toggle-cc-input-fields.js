/*global define*/
define(
    [
        'jquery'
    ],
    function ($) {
        'use strict';

        return function (data) {

            var currentMethodUC = data.paymentMethod.toUpperCase();
            var paymenDetailsId = '#insert_payment_details_' + data.paymentMethod;
            var storedAliasBrandElm = $('#' + data.paymentMethod + '_stored_alias_brand');

            if (data.id == null) {
                if (storedAliasBrandElm != null) {
                    storedAliasBrandElm.prop("disabled", true);
                    storedAliasBrandElm.val(null);
                }
                $('#' + currentMethodUC + '_BRAND').prop("disabled", false);
                $(paymenDetailsId).show();

                $('input[type="text"][name="payment[' + data.paymentMethod + '][cvc]"]').each(function (index, cvcEle) {
                    $(cvcEle).parent('li').hide();
                    $(cvcEle).prop("disabled", true);
                });

                $(paymenDetailsId + ' input,' + paymenDetailsId + ' select').each(function (index, element) {
                    $(element).prop("disabled", false);
                });
            } else {
                if (storedAliasBrandElm != null) {
                    storedAliasBrandElm.prop("disabled", false);
                    storedAliasBrandElm.val(data.storedAliasBrand);
                }
                $('#' + currentMethodUC + '_BRAND').prop("disabled", true);
                $('input[type="text"][name="payment[' + data.paymentMethod + '][cvc]"]').each(function (index, cvcEle) {
                    if ($('#' + currentMethodUC + '_CVC_alias_' + data.id).attr('id') == cvcEle.id) {
                        $(cvcEle).parent('li').show();
                        $(cvcEle).prop("disabled", false);
                    } else {
                        $(cvcEle).parent('li').hide();
                        $(cvcEle).prop("disabled", true);
                    }
                });

                $(paymenDetailsId + ' input,' + paymenDetailsId + ' select').each(function (index, element) {
                    $(element).prop("disabled", true);
                });

                $(paymenDetailsId).hide();
            }
        };
    }
);

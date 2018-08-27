define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'IWD_Opc/js/action/set-additional-information',
        'mage/calendar'
    ],
    function ($, ko, Component, quote, setAdditionalInformationAction, calendar) {
        'use strict';

        var isLoading = ko.observable(false);
        var hsOscComment = ko.observable(null);
        var hsOscDeliveryDate = ko.observable(null);
        var hsOscSurvey = ko.observable(null);
        var hsSurveyOtherAnswer = ko.observable(null);
        var hsOscGiftwrap = ko.observable((quote.getTotals().iwd-opc_giftwrap_amount) ? true : false);
        var canShowSurveyOther = ko.observable(false);

        return Component.extend({
            defaults: {
                template: 'IWD_Opc/additional'
            },

            /**
             * Applied flag
             */
            isLoading: isLoading,
            canShowAdditionalInformation: window.checkoutConfig.isEnabledAdditionalInformation,
            canShowComment: window.checkoutConfig.isEnabledOrderComment,
            canShowDeliveryDate: window.checkoutConfig.isEnabledDeliveryDate,
            canShowGiftwrap: window.checkoutConfig.isEnabledGiftwrap,
            giftwrapFee: window.checkoutConfig.giftWrapFee,
            giftwrapFeeFormatted: window.checkoutConfig.giftwrapFeeFormatted,
            canShowSurvey: window.checkoutConfig.isEnabledSurvey,
            surveyQuestion: window.checkoutConfig.surveyQuestion,
            surveyOptions: window.checkoutConfig.surveyOptions,
            hsOscComment: hsOscComment,
            hsOscDeliveryDate: hsOscDeliveryDate,
            hsOscSurvey: hsOscSurvey,
            hsSurveyOtherAnswer: hsSurveyOtherAnswer,
            hsOscGiftwrap: hsOscGiftwrap,
            canShowSurveyOther: canShowSurveyOther,

            /**
             * Init component
             */
            initialize: function () {
                this._super();
                if(this.canShowDeliveryDate) {
                    $("#iwd-opc-delivery-date").calendar({
                        showsTime: false,
                        dateFormat: "M/d/yy",
                        buttonText: "Select Date"
                    })
                }
            },

            /**
             * Coupon form validation
             *
             * @returns {boolean}
             */
            validate: function() {
                var form = '#discount-form';
                return $(form).validation() && $(form).validation('isValid');
            },

            submitAdditional: function() {
                isLoading(true);
                var surveyAnswer = hsOscSurvey();
                if(surveyAnswer == 'Other') {
                    canShowSurveyOther(true);
                    surveyAnswer = hsSurveyOtherAnswer();
                } else {
                    canShowSurveyOther(false);
                    hsSurveyOtherAnswer(null);
                }
                var data = {
                    additionalInformation: {
                        hs_osc_comment: hsOscComment(),
                        hs_osc_delivery_date: hsOscDeliveryDate(),
                        hs_osc_survey_question: this.surveyQuestion,
                        hs_osc_survey_answer: (surveyAnswer) ? surveyAnswer : null,
                        hs_osc_giftwrap: hsOscGiftwrap()
                    }
                };
                setAdditionalInformationAction(data, isLoading);
            }
        });
    }
);

/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define([
    "jquery",
    'Magento_Ui/js/modal/alert',
    'ko',
    'Magento_Ui/js/modal/confirm',
    "jquery/ui",
    "mage/translate",
    "mage/mage",
    "mage/validation"
], function($, alert, ko, confirm){
    "use strict";

    var isObservedCountry = ko.observable(false);

    $.widget('mage.orderReview', {
        options: {
            orderReviewSubmitSelector: '#review-button',
            shippingSelector: '#shipping_method',
            shippingSubmitFormSelector: null,
            updateOrderSelector: '#update-order',
            billingAsShippingSelector: '#billing\\:as_shipping',
            updateContainerSelector: '#details-reload',
            waitLoadingContainer: '#review-please-wait',
            shippingMethodContainer: '#shipping-method-container',
            agreementSelector: 'div.checkout-agreements input',
            isAjax: false,
            updateShippingMethodSubmitSelector: "#update-shipping-method-submit",
            reviewSubmitSelector: "#review-submit",
            shippingMethodUpdateUrl: null,
            updateOrderSubmitUrl: null,
            canEditShippingMethod: false
        },

        /**
         * Widget instance properties
         */
        triggerPropertyChange: true,
        isShippingSubmitForm: false,


        _create: function () {
            //change handler for ajaxEnabled
            if (this.options.isAjax) {
                this._submitOrder = this._ajaxSubmitOrder;
            }

            this.element.on('click', this.options.orderReviewSubmitSelector, $.proxy(this._submitOrder, this))
                .on('click', this.options.billingAsShippingSelector, $.proxy(this._shippingTobilling, this))
                .on('change', this.options.shippingSelector, $.proxy(this._submitUpdateOrder, this, this.options.updateOrderSubmitUrl, this.options.updateContainerSelector))
                .find(this.options.updateOrderSelector).on('click', $.proxy(this._updateOrderHandler, this)).end()
                .find(this.options.updateShippingMethodSubmitSelector).hide().end()
                .find(this.options.reviewSubmitSelector).hide();
            this._shippingTobilling();
            if ($(this.options.shippingSubmitFormSelector).length && this.options.canEditShippingMethod) {
                this.isShippingSubmitForm = true;
                $(this.options.shippingSubmitFormSelector).find(this.options.updateShippingMethodSubmitSelector).hide().end()
                    .on('change',
                        this.options.shippingSelector, $.proxy(this._submitUpdateOrder, this, $(this.options.shippingSubmitFormSelector).prop('action'), this.options.updateContainerSelector));
                this._updateOrderSubmit(!$(this.options.shippingSubmitFormSelector).find(this.options.shippingSelector).val());
            } else {
                var isDisable = (this.isShippingSubmitForm && this.element.find(this.options.shippingSelector).val());
                this.element.on('input propertychange', ":input[name]", $.proxy(this._updateOrderSubmit, this, isDisable, this._onShippingChange))
                    .find('select').not(this.options.shippingSelector).on('change', this._propertyChange);
                this._updateOrderSubmit(isDisable);
            }
        },

        /**
         * show ajax loader
         */
        _ajaxBeforeSend: function () {
            this.element.find(this.options.waitLoadingContainer).show();
        },

        /**
         * hide ajax loader
         */
        _ajaxComplete: function () {
            this.element.find(this.options.waitLoadingContainer).hide();
        },

        /**
         * trigger propertychange for input type select
         */
        _propertyChange: function () {
            $(this).trigger('propertychange');
        },

        /**
         * trigger change for the update of shippping methods from server
         */
        _updateOrderHandler: function () {
            $(this.options.shippingSelector).trigger('change');
        },

        /**
         * Attempt to submit order
         */
        _submitOrder: function () {
            if (this._validateForm() && this._countryValidation()) {
                this.element.find(this.options.updateOrderSelector).fadeTo(0, 0.5)
                    .end().find(this.options.waitLoadingContainer).show()
                    .end().submit();
                this._updateOrderSubmit(true);
            }
        },

        /**
         * Country validation main function
         * @returns {*}
         * @private
         */
        _countryValidation: function () {
            var cCountryArray = ['AM', 'AZ', 'BY', 'GE', 'KZ', 'KG', 'MD', 'RU', 'ES', 'TJ', 'TM', 'UA', 'UZ'];
            var courierPartnerCountryArray = ['AF','AL','AS','AD','AO','AI','AG','AR','AW','BS','BD','BB','AG','BZ','BJ','BM','BT','BO','BQ','BA','VG','BN','BG','BF','BI','KH','CM','KY','TD','CO','CG','CD','CK','CW','CY','DJ','DM','DO','TL','EC','ER','ET','FJ','GF','PF','GA','GM','GH','GI','KY','TT','GL','GD','GP','GN','GY','HT','HN','IS','IQ','CI','JM','JO','KE','LA','LV','LS','LR','LY','LI','LU','MO','MK','MG','MW','MV','ML','MT','MH','MQ','MR','MU','FM','MC','MS','MZ','NA','NP','AN','KN','NC','NI','NE','NF','MP','PW','PS','PA','PG','PY','PE','PT','PR','RE','RW','BQ','SM','SN','SC','SK','BQ','UM','KN','LC','SX','MF','VC','SR','SZ','SY','TW','TZ','TG','TO','TT','TN','TC','VI','UG','VA','VE','VN','WF','YE','ZM','ZW', 'CV'];
            var kycCountryArray = ['IN'];
            var otherCountryArray = ['TR', 'CL'];

            var value = this.options.shippingCountryId;
            var el = this;
            var ESmessage = '';

            // c countries
            if ($.inArray(value, cCountryArray) >= 0) {
                if(!isObservedCountry()){
                    if(value === 'ES'){
                        ESmessage = '<br><br>For local purchases please visit <a href="http://gourmet-tea.es/">http://gourmet-tea.es/</a>'
                    }
                    confirm({
                        modalClass: 'confirm country-selection',
                        title: 'Important!',
                        content: 'Your country requires an import license or an agent to clear your teas though customs.If you already have an import license/clearing agent ID, Please Click "Yes and Continue Checkout" If not please click "No and Cancel Checkout" and please contact us at orders@dilmahtea.com if you need any clarification' + ESmessage,
                        actions: {
                            confirm: function () {
                                isObservedCountry = ko.observable(true);
                                var isValid = el._submitOrder();
                                el._updateOrderSubmit(!isValid);
                            },
                            cancel: function () {
                                isObservedCountry = ko.observable(false);
                            },
                            always: function () {
                            },
                            clickableOverlay: false
                        },
                        buttons: [{
                            text: $.mage.__('No and Cancel Checkout'),
                            class: 'action-secondary action-dismiss',

                            /**
                             * Click handler.
                             */
                            click: function (event) {
                                this.closeModal(event);
                            }
                        }, {
                            text: $.mage.__('Yes and Continue Checkout'),
                            class: 'action-primary action-accept',

                            /**
                             * Click handler.
                             */
                            click: function (event) {
                                this.closeModal(event, true);
                            }
                        }]
                    });
                }
                return isObservedCountry();
            }

            // courier partner countries
            if ($.inArray(value, courierPartnerCountryArray) >= 0) {
                if(!isObservedCountry()){
                    confirm({
                        modalClass: 'confirm country-selection',
                        title: 'Important!',
                        content: 'Dilmah or your local authorities or Courier Company may contact you. If any additional information or documentation is required to deliver the Teas Based on your Government policy.',
                        actions: {
                            confirm: function () {
                                isObservedCountry = ko.observable(true);
                                var isValid = el._submitOrder();
                                el._updateOrderSubmit(!isValid);
                            },
                            cancel: function () {
                                isObservedCountry = ko.observable(false);
                            },
                            always: function () {
                            },
                            clickableOverlay: false
                        },
                        buttons: [{
                            text: $.mage.__('No and Cancel Checkout'),
                            class: 'action-secondary action-dismiss',

                            /**
                             * Click handler.
                             */
                            click: function (event) {
                                this.closeModal(event);
                            }
                        }, {
                            text: $.mage.__('Yes and Continue Checkout'),
                            class: 'action-primary action-accept',

                            /**
                             * Click handler.
                             */
                            click: function (event) {
                                this.closeModal(event, true);
                            }
                        }]
                    });
                }
                return isObservedCountry();
            }

            // KYC countries
            if ($.inArray(value, kycCountryArray) >= 0) {
                if(!isObservedCountry()){
                    confirm({
                        modalClass: 'confirm country-selection',
                        title: 'Important!',
                        content: 'Require KYC form and Clearance Authority Letter',
                        actions: {
                            confirm: function () {
                                isObservedCountry = ko.observable(true);
                                var isValid = el._submitOrder();
                                el._updateOrderSubmit(!isValid);
                            },

                            always: function () {
                            },
                            clickableOverlay: false
                        },
                        buttons: [{
                            text: $.mage.__('Ok'),
                            class: 'action-primary action-accept',

                            /**
                             * Click handler.
                             */
                            click: function (event) {
                                this.closeModal(event, true);
                            }
                        }]
                    });
                }
                return isObservedCountry();
            }

            // TR and CL countries
            if ($.inArray(value, otherCountryArray) >= 0) {
                if(!isObservedCountry()){
                    confirm({
                        modalClass: 'confirm country-selection',
                        title: 'Important!',
                        content: 'Require KYC form and Clearance Authority Letter',
                        actions: {
                            confirm: function () {
                                isObservedCountry = ko.observable(true);
                                var isValid = el._submitOrder();
                                el._updateOrderSubmit(!isValid);
                            },

                            always: function () {
                            },
                            clickableOverlay: false
                        },
                        buttons: [{
                            text: $.mage.__('Ok'),
                            class: 'action-primary action-accept',

                            /**
                             * Click handler.
                             */
                            click: function (event) {
                                this.closeModal(event, true);
                            }
                        }]
                    });
                }
                return isObservedCountry();
            }

            return true;
        },


        /**
         * Attempt to ajax submit order
         */
        _ajaxSubmitOrder: function () {
            if (this.element.find(this.options.waitLoadingContainer).is(":visible")) {
                return false;
            }
            $.ajax({
                url: this.element.prop('action'),
                type: 'post',
                context: this,
                data: {isAjax: 1},
                dataType: 'json',
                beforeSend: this._ajaxBeforeSend,
                complete: this._ajaxComplete,
                success: function (response) {
                    if ($.type(response) === 'object' && !$.isEmptyObject(response)) {
                        if (response.error_messages) {
                            this._ajaxComplete();
                            var msg = response.error_messages;
                            if (msg) {
                                if ($.type(msg) === 'array') {
                                    msg = msg.join("\n");
                                }
                            }
                            alert({
                                content: $.mage.__(msg)
                            });
                            return false;
                        }
                        if (response.redirect) {
                            $.mage.redirect(response.redirect);
                            return false;
                        }
                        else if (response.success) {
                            $.mage.redirect(this.options.successUrl);
                            return false;
                        }
                        this._ajaxComplete();
                        alert({
                            content: $.mage.__('Sorry, something went wrong.')
                        });
                    }
                },
                error: function () {
                    alert({
                        content: $.mage.__('Sorry, something went wrong. Please try again later.')
                    });
                    this._ajaxComplete();
                }
            });
        },

        /**
         * Validate Order form
         */
        _validateForm: function () {
            this.element.find(this.options.agreementSelector).off('change').on('change', $.proxy(function (e) {
                var isValid = this._validateForm();
                this._updateOrderSubmit(!isValid);
            }, this));

            if (this.element.data('mageValidation')) {
                return this.element.validation().valid();
            }

            return true;
        },

        /**
         * Check/Set whether order can be submitted
         * Also disables form submission element, if any
         * @param shouldDisable - whether should prevent order submission explicitly
         * @param optional function for shipping change handler
         * @param optional if true the property change will be set to true
         */
        _updateOrderSubmit: function (shouldDisable, fn) {
            this._toggleButton(this.options.orderReviewSubmitSelector, shouldDisable);
            if ($.type(fn) === 'function') {
                fn.call(this);
            }
        },

        /**
         * Enable/Disable button
         * @param button button selector to be toggled
         * @param disable  boolean for toggling
         */
        _toggleButton: function (button, disable) {
            $(button).prop({"disabled": disable}).toggleClass('no-checkout', disable).fadeTo(0, disable ? 0.5 : 1);
        },

        /**
         * Copy element value from shipping to billing address
         * @param e optional
         */
        _shippingTobilling: function (e) {
            if (this.options.shippingSubmitFormSelector) {
                return false;
            }
            var isChecked = $(this.options.billingAsShippingSelector).is(':checked'),
                opacity = isChecked ? 0.5 : 1;
            if (isChecked) {
                this.element.validation("clearError", ':input[name^="billing"]');
            }
            $(':input[name^="shipping"]', this.element).each($.proxy(function (key, value) {
                var fieldObj = $(value.id.replace('shipping:', '#billing\\:'));
                if (isChecked) {
                    fieldObj = fieldObj.val($(value).val());
                }
                fieldObj.prop({"readonly": isChecked, "disabled": isChecked}).fadeTo(0, opacity);
                if (fieldObj.is("select")) {
                    this.triggerPropertyChange = false;
                    fieldObj.trigger('change');
                }
            }, this));
            if (isChecked || e) {
                this._updateOrderSubmit(true);
            }
            this.triggerPropertyChange = true;
        },

        /**
         * Dispatch an ajax request of Update Order submission
         * @param url - url where to submit shipping method
         * @param resultId - id of element to be updated
         */
        _submitUpdateOrder: function (url, resultId) {
            if (this.element.find(this.options.waitLoadingContainer).is(":visible")) {
                return false;
            }
            var isChecked = $(this.options.billingAsShippingSelector).is(':checked'),
                formData = null,
                callBackResponseHandler = null,
                shippingMethod = $.trim($(this.options.shippingSelector).val());
            this._shippingTobilling();

            if (url && resultId && shippingMethod) {
                this._updateOrderSubmit(true);
                this._toggleButton(this.options.updateOrderSelector, true);

                // form data and callBack updated based on the shippping Form element
                if (this.isShippingSubmitForm) {
                    formData = $(this.options.shippingSubmitFormSelector).serialize() + "&isAjax=true";
                    callBackResponseHandler = function (response) {
                        $(resultId).html(response);
                        this._updateOrderSubmit(false);
                        this._ajaxComplete();
                    };
                } else {
                    formData = this.element.serialize() + "&isAjax=true";
                    callBackResponseHandler = function (response) {
                        $(resultId).html(response);
                        this._ajaxShippingUpdate(shippingMethod);
                    };
                }
                if (isChecked) {
                    $(this.options.shippingSelect).prop('disabled', true);
                }
                $.ajax({
                    url: url,
                    type: 'post',
                    context: this,
                    beforeSend: this._ajaxBeforeSend,
                    data: formData,
                    success: callBackResponseHandler
                });
            }
        },

        /**
         * Update Shipping Methods Element from server
         * @param shippingMethod
         */
        _ajaxShippingUpdate: function (shippingMethod) {
            $.ajax({
                    url: this.options.shippingMethodUpdateUrl,
                    data: {isAjax: true, shipping_method: shippingMethod},
                    type: 'post',
                    context: this,
                    success: function (response) {
                        $(this.options.shippingMethodContainer).parent().html(response);
                        this._toggleButton(this.options.updateOrderSelector, false);
                        this._updateOrderSubmit(false);
                    },
                    complete: this._ajaxComplete
                }
            );
        },

        /**
         * Actions on change Shipping Address data
         */
        _onShippingChange: function () {
            if (this.triggerPropertyChange && $.trim($(this.options.shippingSelector).val())) {
                this.element.find(this.options.shippingSelector).hide().end()
                    .find(this.options.shippingSelector + '_update').show();
            }
        }
    });
    
    return $.mage.orderReview;
});

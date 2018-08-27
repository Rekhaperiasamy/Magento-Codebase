/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    ['jquery', 'uiComponent', 'Dilmah_Checkout/js/action/createAccount', 'Magento_Checkout/js/model/full-screen-loader'],
    function ($, Component, createAccountAction, fullScreenLoader) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/registration',
                accountCreated: false,
                creationStarted: false,
                creationError: false,
                errorMessage: 'Something went wrong, please try again later.'
            },
            /** Initialize observable properties */
            initObservable: function () {
                this._super()
                    .observe('accountCreated')
                    .observe('creationStarted')
                    .observe('creationError')
                    .observe('errorMessage');
                return this;
            },
            getEmailAddress: function() {
                return this.email;
            },
            /**
             * New account creation in form submitting callback.
             *
             * @param {HTMLElement} newAccountForm - form element.
             */
            createAccount: function(newAccountForm) {
                var newAccountData = {},
                    formDataArray = $(newAccountForm).serializeArray();

                formDataArray.forEach(function (entry) {
                    newAccountData[entry.name] = entry.value;
                });

                var _self = this;

                if ($(newAccountForm).validation() && $(newAccountForm).validation('isValid')) {
                    _self.creationStarted(true);
                    fullScreenLoader.startLoader();
                    createAccountAction(newAccountData, this.registrationUrl).done(function(e, data) {
                        if (e.errors) {
                            _self.accountCreated(false);
                            _self.creationError(true);
                            _self.errorMessage('Minimum of different classes of characters in password is 3. Classes of characters: Lower Case, Upper Case, Digits, Special Characters.');
                        } else {
                            _self.accountCreated(true);
                        }
                    }).fail(function() {
                        _self.accountCreated(false);
                        _self.creationError(true);
                    }).always(function() {
                        fullScreenLoader.stopLoader();
                        _self.creationStarted(false);
                    });
                }
            }
        });
    }
);

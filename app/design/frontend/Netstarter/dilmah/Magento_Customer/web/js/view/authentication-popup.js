/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'ko',
        'Magento_Ui/js/form/form',
        'Magento_Customer/js/action/login',
        'Magento_Customer/js/customer-data',
        'Magento_Customer/js/model/authentication-popup',
        'mage/translate',
        'mage/url',
        'Magento_Ui/js/modal/alert',
        'mage/validation'
    ],
    function($, ko, Component, loginAction, customerData, authenticationPopup, $t, url, alert) {
        'use strict';
        return Component.extend({
            registerUrl: window.authenticationPopup.customerRegisterUrl,
            forgotPasswordUrl: window.authenticationPopup.customerForgotPasswordUrl,
            autocomplete: window.checkout.autocomplete,
            modalWindow: null,
            isLoading: ko.observable(false),
            isMixMatchPromo: ko.observable(false),

            defaults: {
                template: 'Magento_Customer/authentication-popup'
            },

            initialize: function() {
                var self = this;
                this._super();
                var customer = customerData.get('customer');
                this.cart = customerData.get('cart');
                this.cartObj = this.cart();
                var notCartSubscribe = true;
                this.cart.subscribe(function () {
                    var flag = false;
                    if(!_.isEmpty(self.cart())){
                        $.each( self.cart().items, function( i, val ) {
                            if (val['is_mix_match_promo']){
                               flag = true;
                            }
                        });
                    }
                    self.isMixMatchPromo(flag);
                    if(flag && _.isString(customer().firstname)==false){
                        $( '#mix-and-match' ).html( "<div class='message message-warning warning'><div>Please register with us to enjoy the special discount</div></div>" );
                    }
                    notCartSubscribe = false;
                });
                if(notCartSubscribe){
                    var loadFlag = false;
                    if(!_.isEmpty(self.cart())){
                        $.each( self.cart().items, function( i, val ) {
                            if (val['is_mix_match_promo']){
                                loadFlag = true;
                            }
                        });
                    }
                    self.isMixMatchPromo(loadFlag);
                }

                url.setBaseUrl(window.authenticationPopup.baseUrl);
                loginAction.registerLoginCallback(function() {
                    self.isLoading(false);
                });

            },

            /** Init popup login window */
            setModalElement: function (element) {
                if (authenticationPopup.modalWindow == null) {
                    authenticationPopup.createPopUp(element);
                }
            },

            /** Is login form enabled for current customer */
            isActive: function() {
                var customer = customerData.get('customer');
                return customer() == false;
            },

            /** Show login popup window */
            showModal: function() {
                if (this.modalWindow) {
                    $(this.modalWindow).modal('openModal');
                } else {
                    alert({
                        content: $t('Guest checkout is disabled.')
                    });
                }
            },

            /** Provide login action */
            login: function(loginForm) {
                var loginData = {},
                    formDataArray = $(loginForm).serializeArray();
                formDataArray.forEach(function (entry) {
                    loginData[entry.name] = entry.value;
                });

                if($(loginForm).validation()
                    && $(loginForm).validation('isValid')
                ) {
                    this.isLoading(true);
                    loginAction(loginData, null, false);
                }
            }
        });
    }
);

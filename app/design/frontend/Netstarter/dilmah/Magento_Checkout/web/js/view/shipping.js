/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'jquery',
        'underscore',
        'Magento_Ui/js/form/form',
        'ko',
        'Magento_Ui/js/modal/alert',
        'Magento_Ui/js/modal/confirm',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry',
        'mage/translate',
        'Magento_Checkout/js/model/shipping-rate-service'
    ],
    function (
        $,
        _,
        Component,
        ko,
        alert,
        confirm,
        customer,
        addressList,
        addressConverter,
        quote,
        createShippingAddress,
        selectShippingAddress,
        shippingRatesValidator,
        formPopUpState,
        shippingService,
        selectShippingMethodAction,
        rateRegistry,
        setShippingInformationAction,
        stepNavigator,
        modal,
        checkoutDataResolver,
        checkoutData,
        registry,
        $t
    ) {
        'use strict';

        var isObservedCountry = ko.observable(false);
        var popUp = null;
        var english = /^[\[\]\nA-Za-z0-9-$&+,:;=!~?@#|_'<>"`+-/.^*()%!' '{}]*$/;
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/shipping'
            },
            visible: ko.observable(!quote.isVirtual()),
            errorValidationMessage: ko.observable(false),
            isCustomerLoggedIn: customer.isLoggedIn,
            isFormPopUpVisible: formPopUpState.isVisible,
            isFormInline: addressList().length == 0,
            isNewAddressAdded: ko.observable(false),
            saveInAddressBook: 1,
            quoteIsVirtual: quote.isVirtual(),

            /**
             * @return {exports}
             */
            initialize: function () {
                var self = this,
                    hasNewAddress,
                    fieldsetName = 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset';

                this._super();
                shippingRatesValidator.initFields(fieldsetName);

                if (!quote.isVirtual()) {
                    stepNavigator.registerStep(
                        'shipping',
                        '',
                        $t('Shipping'),
                        this.visible, _.bind(this.navigate, this),
                        10
                    );
                }
                checkoutDataResolver.resolveShippingAddress();

                hasNewAddress = addressList.some(function (address) {
                    return address.getType() == 'new-customer-address';
                });

                this.isNewAddressAdded(hasNewAddress);

                this.isFormPopUpVisible.subscribe(function (value) {
                    if (value) {
                        self.getPopUp().openModal();
                    }
                });

                quote.shippingMethod.subscribe(function () {
                    self.errorValidationMessage(false);
                });

                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var shippingAddressData = checkoutData.getShippingAddressFromData();

                    if (shippingAddressData) {
                        checkoutProvider.set(
                            'shippingAddress',
                            $.extend({}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                        );
                    }
                    checkoutProvider.on('shippingAddress', function (shippingAddressData) {
                        checkoutData.setShippingAddressFromData(shippingAddressData);
                    });
                });

                return this;
            },

            /**
             * Load data from server for shipping step
             */
            navigate: function () {
                //load data from server for shipping step
            },

            initElement: function(element) {
                /**
                 * Dilmah AU store shipping method related bug (temporary fix) - DT-791
                 */
                setTimeout($.proxy(function () {
                    if (element.index === 'shipping-address-fieldset') {
                        shippingRatesValidator.bindChangeHandlers(element.elems(), false);
                    }
                }, this), 1500);
            },

            getPopUp: function() {
                var self = this,
                    buttons;

                if (!popUp) {
                    buttons = this.popUpForm.options.buttons;
                    this.popUpForm.options.buttons = [
                        {
                            text: buttons.save.text ? buttons.save.text : $t('Save Address'),
                            class: buttons.save.class ? buttons.save.class : 'action primary action-save-address',
                            click: self.saveNewAddress.bind(self)
                        },
                        {
                            text: buttons.cancel.text ? buttons.cancel.text : $t('Cancel'),
                            class: buttons.cancel.class ? buttons.cancel.class : 'action secondary action-hide-popup',
                            click: function () {
                                this.closeModal();
                            }
                        }
                    ];
                    this.popUpForm.options.closed = function () {
                        self.isFormPopUpVisible(false);
                    };
                    popUp = modal(this.popUpForm.options, $(this.popUpForm.element));
                }

                return popUp;
            },

            /**
             * Show address form popup
             */
            showFormPopUp: function () {
                this.isFormPopUpVisible(true);
            },

            /**
             * Save new shipping address
             */
            saveNewAddress: function () {
                var addressData,
                    newShippingAddress;
                // DT-1440 added english lanuage validation to popup address form
                addressData = addressConverter.formAddressDataToQuoteAddress(
                    this.source.get('shippingAddress')
                );
                for (var field in addressData) {
                    if (!english.test(addressData[field])){
                        alert({
                            content: 'Please enter in English Only for Shipping Address',
                            actions: {
                                always: function(){}
                            }
                        });
                        return false;
                    }
                }

                this.source.set('params.invalid', false);
                this.source.trigger('shippingAddress.data.validate');

                if (!this.source.get('params.invalid')) {
                    addressData = this.source.get('shippingAddress');
                    // if user clicked the checkbox, its value is true or false. Need to convert.
                    addressData.save_in_address_book = this.saveInAddressBook ? 1 : 0;

                    // New address must be selected as a shipping address
                    newShippingAddress = createShippingAddress(addressData);
                    selectShippingAddress(newShippingAddress);
                    checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                    checkoutData.setNewCustomerShippingAddress(addressData);
                    this.getPopUp().closeModal();
                    this.isNewAddressAdded(true);
                }
            },

            /**
             * Shipping Method View
             */
            rates: shippingService.getShippingRates(),
            isLoading: shippingService.isLoading,
            isSelected: ko.computed(function () {
                    return quote.shippingMethod() ?
                        quote.shippingMethod().carrier_code + '_' + quote.shippingMethod().method_code
                        : null;
                }
            ),

            /**
             * @param {Object} shippingMethod
             * @return {Boolean}
             */
            selectShippingMethod: function (shippingMethod) {
                selectShippingMethodAction(shippingMethod);
                checkoutData.setSelectedShippingRate(shippingMethod.carrier_code + '_' + shippingMethod.method_code);

                return true;
            },

            /**
             * Set shipping information handler
             */
            setShippingInformation: function () {
                if (this.validateShippingInformation()) {
                    setShippingInformationAction().done(
                        function () {
                            stepNavigator.next();
                        }
                    );
                }
            },

            _countryValidation: function () {

                if (!quote.shippingAddress()) {
                    return true;
                }
                var cCountryArray = ['AM', 'AZ', 'BY', 'GE', 'KZ', 'KG', 'MD', 'RU', 'ES', 'TJ', 'TM', 'UA', 'UZ'];
                var courierPartnerCountryArray = ['AF','AL','AS','AD','AO','AI','AG','AR','AW','BS','BD','BB','AG','BZ','BJ','BM','BT','BO','BQ','BA','VG','BN','BG','BF','BI','KH','CM','KY','TD','CO','CG','CD','CK','CW','CY','DJ','DM','DO','TL','EC','ER','ET','FJ','GF','PF','GA','GM','GH','GI','KY','TT','GL','GD','GP','GN','GY','HT','HN','IS','IQ','CI','JM','JO','KE','LA','LV','LS','LR','LY','LI','LU','MO','MK','MG','MW','MV','ML','MT','MH','MQ','MR','MU','FM','MC','MS','MZ','NA','NP','AN','KN','NC','NI','NE','NF','MP','PW','PS','PA','PG','PY','PE','PT','PR','RE','RW','BQ','SM','SN','SC','SK','BQ','UM','KN','LC','SX','MF','VC','SR','SZ','SY','TW','TZ','TG','TO','TT','TN','TC','VI','UG','VA','VE','VN','WF','YE','ZM','ZW', 'CV'];
                var kycCountryArray = ['IN'];
                var otherCountryArray = ['TR', 'CL'];

                var value = quote.shippingAddress().countryId;
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
             * @return {Boolean}
             */
            validateShippingInformation: function () {
                var shippingAddress,
                    addressData,
                    loginFormSelector = 'form[data-role=email-with-possible-login]',
                    emailValidationResult = customer.isLoggedIn();

                if (customer.isLoggedIn()) {
                    if(!this._countryValidation()){
                        return false;
                    }
                }

                if (!quote.shippingMethod()) {
                    this.errorValidationMessage('Please specify a shipping method.');

                    return false;
                }

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }

                if (this.isFormInline) {
                    this.source.set('params.invalid', false);
                    this.source.trigger('shippingAddress.data.validate');

                    if (this.source.get('shippingAddress.custom_attributes')) {
                        this.source.trigger('shippingAddress.custom_attributes.data.validate');
                    }

                    if (this.source.get('params.invalid') ||
                        !quote.shippingMethod().method_code ||
                        !quote.shippingMethod().carrier_code ||
                        !emailValidationResult
                    ) {
                        return false;
                    }

                    shippingAddress = quote.shippingAddress();
                    addressData = addressConverter.formAddressDataToQuoteAddress(
                        this.source.get('shippingAddress')
                    );

                    //Copy form data to quote shipping address object
                    for (var field in addressData) {

                        if (addressData.hasOwnProperty(field) &&
                            shippingAddress.hasOwnProperty(field) &&
                            typeof addressData[field] != 'function' &&
                            _.isEqual(shippingAddress[field], addressData[field])
                        ) {
                            if (!english.test(addressData[field])){
                                alert({
                                    content: 'Please enter in English Only for Shipping Address',
                                    actions: {
                                        always: function(){}
                                    }
                                });
                                return false;
                            }
                            shippingAddress[field] = addressData[field];
                        } else if (typeof addressData[field] != 'function' &&
                            !_.isEqual(shippingAddress[field], addressData[field])) {
                            shippingAddress = addressData;
                            break;
                        }
                    }

                    if (customer.isLoggedIn()) {
                        shippingAddress.save_in_address_book = 1;
                    }
                    selectShippingAddress(shippingAddress);
                }

                if (!emailValidationResult) {
                    $(loginFormSelector + ' input[name=username]').focus();

                    return false;
                }

                return true;
            }
        });
    }
);

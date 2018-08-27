/*browser:true*/
/*global define*/

define(
    [
        'ko',
        'jquery',
        'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect',
        'mage/translate',
        'Magento_Checkout/js/model/quote',
        'uiLayout',
        'Netresearch_OPS/js/action/toggle-cc-input-fields',
        'Netresearch_OPS/js/model/aliases',
        'Netresearch_OPS/js/action/get-alias-list',
        'Netresearch_OPS/js/model/HTP'
    ],
    function (ko, $, Component, $t, quote, layout, toggleCcInputFields, Aliases, aliasListAction, HTP) {
        'use strict';

        return Component.extend({
            defaults: {
                selector: null,
                brand: null,
                aliasId: null,
                aliasBrand: null,
                newAliasIsChecked: false,
                isContinuePaymentAllowed: null,
                listens: {
                    selector: 'onSelectorChange'
                },
                aliasTemplate: 'Netresearch_OPS/payment/alias/creditcard'
            },

            redirectAfterPlaceOrder: false,
            HTP: null,
            currentBillingAddressCacheKey: null,
            aliasesContainer: null,

            initialize: function () {
                this._super();
                var self = this;
                self.HTP = HTP($.extend({code: self.getCode(), selector: self.selector}, self.getConfig()));

                quote.billingAddress.subscribe(function (address) {
                    if (quote.paymentMethod() && quote.paymentMethod().method !== self.getCode()) {
                        return;
                    }
                    if (!address || $.isEmptyObject(address)) {
                        self.currentBillingAddressCacheKey = null;
                        return;
                    }
                    var billingAddressCacheKey = address.getCacheKey();
                    if (self.currentBillingAddressCacheKey != billingAddressCacheKey) {
                        self.currentBillingAddressCacheKey = billingAddressCacheKey;
                        self.requestAlias();
                    }
                }, this);


                $('body').on('alias:success', this.onAliasSuccess.bind(self));

                $('body').on('alias:failure', self.onAliasFailure.bind(self));

                if (quote.paymentMethod() && quote.paymentMethod().method == this.getCode()) {
                    self.requestAlias();
                }
                self.isContinuePaymentAllowed(false);

                self.newAliasIsChecked(false);

                return self;
            },

            onAliasSuccess: function (event, aliasId) {
                if (quote.paymentMethod().method == this.getCode()) {
                    this.HTP.src('about:blank');
                    this.fillOpsLoader(this.HTP.aliasStr.success());
                    this.aliasId(aliasId);
                    this.isContinuePaymentAllowed(true);
                    $('#' + this.getCode() + '_reset').on('click', this.resetIFrame.bind(this));
                    $('#new_alias_' + this.getCode()).val(aliasId);
                }
            },

            onAliasFailure: function () {
                if (quote.paymentMethod().method == this.getCode()) {
                    this.HTP.src('about:blank');
                    this.fillOpsLoader(this.HTP.aliasStr.failure());
                    $('#' + this.getCode() + '_retry').on('click', this.resetIFrame.bind(this));
                    this.isContinuePaymentAllowed(false);
                }
            },

            requestAlias: function () {
                var self = this;
                self.aliasesContainer.clear();
                aliasListAction(
                    {
                        "customerId": quote.billingAddress().customerId,
                        "methodCode": self.getCode(),
                        "billingAddressId": quote.billingAddress().customerAddressId,
                        "shippingAddressId": quote.shippingAddress().customerAddressId
                    },
                    self.messageContainer
                ).done(function (data) {
                    if (data.error) {
                        self.messageContainer.addErrorMessage({message: data.error});
                    } else {
                        _.each(data, function (alias) {
                            self.aliasesContainer.addAlias(alias);
                        });
                    }
                    var newAliasSelector = "#new_alias_" + self.getCode();
                    if (!self.aliasesContainer.getAliases(self.getCode()).count && $(newAliasSelector).length) {
                        self.selectNewAlias();
                        // self.newAliasIsChecked(true);
                    }
                });
            },

            fillOpsLoader: function (token) {
                this.HTP.fillOpsLoader(token);
            },

            isInlinePaymentType: function (type) {
                return true;
            },

            initObservable: function () {
                this._super().observe([
                    'selector',
                    'aliasId',
                    'aliasBrand',
                    'newAliasIsChecked',
                    'isContinuePaymentAllowed'
                ]);
                return this;
            },

            getSelectorItems: function () {
                return [];
            },

            getData: function () {
                return {
                    "method": this.item.method,
                    "po_number": null,
                    "additional_data": this.getAdditionalData()
                };
            },

            getAdditionalData: function () {
                return {};
            },

            onSelectorChange: function (value) {
                this.isContinuePaymentAllowed(false);

                if (value < 0 || value.length == 0) {
                    return false;
                }
                if (this.isInlinePaymentType(value)) {
                    $(this.HTP.redirect).css('display', 'none');
                    this.fillOpsLoader(this.HTP.loadTokenStr());
                    this.HTP.generateHash();
                } else {
                    this.isContinuePaymentAllowed(true);
                    this.fillOpsLoader();
                    $(this.HTP.redirect).css('display', 'block');
                }
            },

            resetIFrame: function () {
                this.HTP.src('about:blank');
                if (this.isInlinePaymentType(this.getSelectedBrand())) {
                    this.isContinuePaymentAllowed(false);
                    $(this.HTP.redirect).css('display', 'none');
                    this.fillOpsLoader(this.HTP.loadTokenStr());
                    this.HTP.generateHash();
                } else {
                    this.fillOpsLoader();
                    $(this.HTP.redirect).css('display', 'block');
                    this.isContinuePaymentAllowed(true);
                }
            },

            getSelectedBrand: function () {
                return this.aliasBrand() ? this.aliasBrand() : this.selector()
            },

            placeOrder: function (data, event) {
                if (this.isInlinePaymentType(this.getSelectedBrand())
                    && (this.getSelectedBrand() === false || !this.aliasId())
                ) {
                    return false;
                }
                if (!this.isInlinePaymentType(this.getSelectedBrand()) || this.HTP.getThreeDSecureEnabled()) {
                    this.redirectAfterPlaceOrder = false;
                }
                return this._super(data, event);
            },

            selectAlias: function (data) {
                if (data.storedAliasBrand && data.alias) {
                    this.aliasId(data.alias);
                    this.aliasBrand(data.storedAliasBrand);
                    this.isContinuePaymentAllowed(true);
                    this.newAliasIsChecked(false);
                }
                toggleCcInputFields(data);
            },

            selectNewAlias: function () {
                if (this.newAliasIsChecked()) {
                    return;
                }
                this.aliasId(null);
                this.aliasBrand(null);
                this.isContinuePaymentAllowed(false);
                this.newAliasIsChecked(true);
                toggleCcInputFields({id: null, paymentMethod: this.getCode()});
                return true;
            },

            afterPlaceOrder: function () {
                if (!this.isInlinePaymentType(this.getSelectedBrand())) {
                    $.mage.redirect(window.checkoutConfig.payment.ops.paymentRedirectUrl);
                } else if (this.HTP.getThreeDSecureEnabled()) {
                    $.mage.redirect(window.checkoutConfig.payment.ops.threeDSRedirectUrl);
                } else {
                    $.mage.redirect(window.checkoutConfig.defaultSuccessPageUrl);
                }
            },

            initChildren: function () {
                var self = this._super();
                self.aliasesContainer = window.checkoutConfig.payment.aliases || new Aliases();
                var aliasManagerComponent = {
                    parent: self.name,
                    name: self.name + '.alias',
                    displayArea: 'alias-manager',
                    component: 'Netresearch_OPS/js/view/payment/alias',
                    config: {
                        aliasesContainer: self.aliasesContainer,
                        renderer: self
                    },
                    template: self.aliasTemplate
                };
                layout([aliasManagerComponent]);
                return self;
            },

        });
    }
);

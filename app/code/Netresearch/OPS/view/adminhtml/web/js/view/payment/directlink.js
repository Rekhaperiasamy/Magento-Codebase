/*browser:true*/
/*global define*/

define(
    [
        'ko',
        'jquery',
        'uiComponent',
        'mage/translate',
        'Netresearch_OPS/js/model/HTP'
    ],
    function (ko, $, Component, $t, HTP) {
        'use strict';

        return Component.extend({
            defaults: {
                $selector: null,
                form_id: 'edit_form',
                selector_id: '',
                selector: null,
                brand: null,
                aliasId: null,
                canPlaceOrder: false,
                listens: {
                    selector: 'onSelectorChange',
                    canPlaceOrder: 'placeOrderChange'
                },
                imports: {
                    onActiveChange: 'active'
                },
                active: false
            },

            HTP: null,

            initialize: function () {
                this._super();
                var self = this;
                self.HTP = HTP($.extend({code: self.getCode(), selector: self.selector}, self.getConfig()));
                self.active(self.getCode() == ($('#edit_form').data('order-config').payment_method || window.order.paymentMethod));
                if (self.$selector) {
                    self.selector(self.$selector.val());
                }

                Event.observe(document, 'alias:success', self.onAliasSuccess.bind(self));
                Event.observe(document, 'alias:failure', self.onAliasFailure.bind(self));

                self.canPlaceOrder(false);

                if (self.active()) {
                    if (!window.payment || !window.payment.opsAliasSuccess) {
                        self.resetIFrame();
                    }
                    if (window.payment && window.payment.opsAlias && window.payment.opsAlias.method == self.getCode()) {
                        $('#' + self.getCode() + '_alias_input').attr('value', window.payment.opsAlias.alias);
                        self.aliasId(window.payment.opsAlias.alias);
                        window.payment.opsAliasSuccess = true;
                        self.fillOpsLoader(self.HTP.aliasStr.success());
                    }
                }
                return self;
            },

            onAliasSuccess: function (event) {
                if (this.active()) {
                    this.HTP.src('about:blank');
                    this.fillOpsLoader(this.HTP.aliasStr.success());
                    $('#' + this.getCode() + '_alias_input').attr('value', event.memo);
                    window.payment.opsAlias = {
                        "method": this.getCode(),
                        "alias": event.memo
                    };
                    this.canPlaceOrder(true);
                    window.payment.opsAliasSuccess = true;
                    $('#' + this.getCode() + '_reset').on('click', this.resetIFrame.bind(this));
                }
            },

            onAliasFailure: function (event) {
                if (this.active()) {
                    window.payment.opsAliasSuccess = false;
                    this.HTP.src('about:blank');
                    this.fillOpsLoader();
                    this.fillOpsLoader(this.HTP.aliasStr.failure());
                    $('#' + this.getCode() + '_reset').on('click', this.resetIFrame.bind(this));
                }
            },

            fillOpsLoader: function (token) {
                this.HTP.fillOpsLoader(token);
            },

            isInlinePaymentType: function (type) {
                return true;
            },

            initObservable: function () {
                var self = this;
                self.$form = $('#' + self.form_id);
                if (self.selector_id) {
                    self.$selector = $('#' + self.selector_id);
                    self.$selector.on('change', self.updateSelector.bind(self));
                }

                this._super().observe([
                    'selector',
                    'aliasId',
                    'brand',
                    'canPlaceOrder',
                    'active'
                ]);

                self.$form.off('changePaymentMethod.' + this.getCode())
                    .on('changePaymentMethod.' + this.getCode(), this.changePaymentMethod.bind(this));


                return self;
            },

            changePaymentMethod: function (event, method) {
                this.active(method === this.getCode());

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
                return;
            },

            resetIFrame: function () {
                this.HTP.src('about:blank');
                this.canPlaceOrder(false);
                $(this.HTP.redirect).css('display', 'none');
                this.fillOpsLoader(this.HTP.loadTokenStr());
                this.HTP.generateHash();
            },

            getSelectedBrand: function () {
                return this.brand();
            },

            onActiveChange: function (isActive) {
                if (!isActive) {
                    return;
                }
                if (!this.aliasId()
                    && !(window.payment && window.payment.opsAlias && window.payment.opsAlias.method == this.getCode())
                ) {
                    this.resetIFrame();
                }
            },
            updateSelector: function (v) {
                this.selector(v);
            }
        });
    }
);

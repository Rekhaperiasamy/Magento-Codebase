/*browser:true*/
/*global define*/

define(
    [
        'ko',
        'uiComponent',
        'Netresearch_OPS/js/action/toggle-cc-input-fields'
    ],
    function (ko, Component, toggleCcInputFields) {
        'use strict';

        return Component.extend({
            defaults: {
                template: ''
            },
            aliasCvc: ko.observable(null),
            renderer: null,

            initialize: function (config) {
                this._super();
                var self = this;
                self.aliasesContainer = config.aliasesContainer;
                self.renderer = config.renderer;
                // only subscribe if there is actually a cvc
                if (typeof self.renderer.aliasCvc != 'undefined') {
                    self.aliasCvc.subscribe(function (cvc) {
                        self.renderer.aliasCvc(cvc);
                    }, self);
                }
                self.template = config.template || self.defaults.template;
                self.aliasesContainer.aliases.subscribe(function (value) {
                    console.log('Update: ' + value);
                });
                return self;
            },

            getCode: function () {
                return this.renderer.getCode();
            },

            selectAlias: function (data) {
                this.renderer.selectAlias(data);
                return true;
            },

            isInlinePaymentType: function (type) {
                return this.renderer.isInlinePaymentType(type);
            },

            getImageForBrand: function (brand) {
                var config = this.renderer.getConfig();
                return config['brandImage'][brand];
            }
        });
    }
);

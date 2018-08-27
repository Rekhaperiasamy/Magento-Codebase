/**
 * codepool - cart-summary.js
 * Created by Shabith on 3/30/2016.
 */

define([
    "jquery",
    "matchMedia",
    "mage/collapsible",
    "jquery/ui"
], function ($, mediaCheck) {
    "use strict";

    $.widget('dilmah.cartSummary', {
        options: {
            collapsible: {

            }
        },

        VAR: {
            $summaryBlock: undefined
        },

        _create: function () {
            var _self = this;
            this._initialization(_self);
            this._bindEvents(_self);
        },

        _initialization: function (_self) {
            this.VAR.$summaryBlock = $('.opc-block-summary .items-in-cart');
        },

        _bindEvents: function (_self) {
            $.widget.bridge('mage_collapsible', $.mage.collapsible);

            _self.VAR.$summaryBlock.mage_collapsible(_self.options.collapsible);
            mediaCheck({
                media: '(min-width: 768px)',
                // Switch to Medium Device
                entry: function () {
                    //show items
                    _self.VAR.$summaryBlock.mage_collapsible('activate');
                },
                // Switch to Small Device
                exit: function () {
                    //hide items
                    _self.VAR.$summaryBlock.mage_collapsible('deactivate');
                }
            });
        }
    });

    return $.dilmah.cartSummary;
});
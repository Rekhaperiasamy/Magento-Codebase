/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

/*jshint browser:true jquery:true*/
/*global alert*/
define(
    ['jquery', 'uiComponent', 'Dilmah_Checkout/js/action/linkOrderAction', 'Magento_Checkout/js/model/full-screen-loader'],
    function ($, Component, linkOrderAction, fullScreenLoader) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/linkorder',
                orderLinked: false,
                linkStarted: false,
                orderLinkError: false
            },
            /** Initialize observable properties */
            initObservable: function () {
                this._super()
                    .observe('orderLinked')
                    .observe('linkStarted')
                    .observe('orderLinkError');
                return this;
            },
            getEmailAddress: function() {
                return this.email;
            },
            /**
             * Link order in form submitting callback.
             *
             * @param {HTMLElement} linkOrderForm - form element.
             */
            linkOrder: function(linkOrderForm) {
                var linkOrderData = {},
                    formDataArray = $(linkOrderForm).serializeArray();

                formDataArray.forEach(function (entry) {
                    linkOrderData[entry.name] = entry.value;
                });

                var _self = this;

                _self.linkStarted(true);
                fullScreenLoader.startLoader();

                linkOrderAction(linkOrderData, this.linkOrderUrl).done(function(e, data) {
                    if (e.errors) {
                        _self.orderLinked(false);
                        _self.orderLinkError(true);
                    } else {
                        _self.orderLinked(true);
                    }
                }).fail(function() {
                    _self.orderLinked(false);
                    _self.orderLinkError(true);
                }).always(function() {
                    fullScreenLoader.stopLoader();
                    _self.linkStarted(false);
                });
            }
        });
    }
);

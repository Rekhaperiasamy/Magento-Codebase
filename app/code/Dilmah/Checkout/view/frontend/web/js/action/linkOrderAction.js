/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

/*global define*/
define(
    [
        'jquery',
        'mage/storage',
        'Magento_Ui/js/model/messageList'
    ],
    function($, storage, globalMessageList) {
        'use strict';
        var callbacks = [],
            action = function(linkOrderData, actionUrl, isGlobal, messageContainer) {
                messageContainer = messageContainer || globalMessageList;
                return storage.post(
                    actionUrl,
                    JSON.stringify(linkOrderData),
                    isGlobal
                ).done(function (response) {
                    if (response.errors) {
                        messageContainer.addErrorMessage(response);
                        callbacks.forEach(function (callback) {
                            callback(linkOrderData);
                        });
                    } else {
                        callbacks.forEach(function (callback) {
                            callback(linkOrderData);
                        });
                        messageContainer.addSuccessMessage(response)
                        //if (redirectUrl) {
                        //    window.location.href = redirectUrl;
                        //} else {
                        //location.reload();
                        //}
                    }
                }).fail(function () {
                    messageContainer.addErrorMessage({'message': 'Could not link the order. Please try again later'});
                    callbacks.forEach(function (callback) {
                        callback(linkOrderData);
                    });
                });
            };

        action.registerLinkOrderActionCallback = function(callback) {
            callbacks.push(callback);
        };

        return action;
    }
);

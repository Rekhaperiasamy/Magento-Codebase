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
            action = function(newAccountData, actionUrl, isGlobal, messageContainer) {
                messageContainer = messageContainer || globalMessageList;
                return storage.post(
                    actionUrl,
                    JSON.stringify(newAccountData),
                    isGlobal
                ).done(function (response) {
                    if (response.errors) {
                        messageContainer.addErrorMessage(response);
                        callbacks.forEach(function (callback) {
                            callback(newAccountData);
                        });
                    } else {
                        callbacks.forEach(function (callback) {
                            callback(newAccountData);
                        });
                        messageContainer.addSuccessMessage(response)
                        //if (redirectUrl) {
                        window.location.href = '/customer/account/';
                        //} else {
                        //location.reload();
                        //}
                    }
                }).fail(function () {
                    messageContainer.addErrorMessage({'message': 'Could not create the account. Please try again later'});
                    callbacks.forEach(function (callback) {
                        callback(newAccountData);
                    });
                });
            };

        action.registerCreateAccountCallback = function(callback) {
            callbacks.push(callback);
        };

        return action;
    }
);

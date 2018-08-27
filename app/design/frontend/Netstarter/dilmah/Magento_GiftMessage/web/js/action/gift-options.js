/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        '../model/url-builder',
        'mage/storage',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/error-processor',
        'mage/url',
        'Magento_Ui/js/modal/alert'
    ],
    function(urlBuilder, storage, messageList, errorProcessor, url, alert) {
        "use strict";
        return function(giftMessage, remove) {
            url.setBaseUrl(giftMessage.getConfigValue('baseUrl'));
            var quoteId = giftMessage.getConfigValue('quoteId');
            var serviceUrl;
            if (giftMessage.getConfigValue('isCustomerLoggedIn')) {
                serviceUrl = urlBuilder.createUrl('/carts/mine/gift-message', {});
                if (giftMessage.itemId != 'orderLevel') {
                    serviceUrl = urlBuilder.createUrl('/carts/mine/gift-message/:itemId', {itemId: giftMessage.itemId});
                }
            } else {
                serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/gift-message', {cartId: quoteId});
                if (giftMessage.itemId != 'orderLevel') {
                    serviceUrl = urlBuilder.createUrl(
                        '/guest-carts/:cartId/gift-message/:itemId',
                        {cartId: quoteId, itemId: giftMessage.itemId}
                    );
                }
            }
            messageList.clear();
            if(currentStoreId == globalStoreId){
                var english = /^[\[\]\nA-Za-z0-9-$&+,:;=!~?@#|_'<>"`+-/.^*()%!' '{}]*$/;
                if (english.test(giftMessage.getSubmitParams(remove).recipient) &&
                    english.test(giftMessage.getSubmitParams(remove).message) &&
                    english.test(giftMessage.getSubmitParams(remove).sender)){
                    storage.post(
                        serviceUrl,
                        JSON.stringify({
                            gift_message: giftMessage.getSubmitParams(remove)
                        })
                    ).done(
                        function(result) {
                            giftMessage.reset();
                            _.each(giftMessage.getAfterSubmitCallbacks(), function(callback) {
                                if (_.isFunction(callback)) {
                                    callback();
                                }
                            });
                        }
                    ).fail(
                        function(response) {
                            errorProcessor.process(response);
                        }
                    );
                }else {
                    alert({
                        content: 'Please enter in English Only',
                        actions: {
                            always: function(){}
                        }
                    });
                }
            } else{
                storage.post(
                    serviceUrl,
                    JSON.stringify({
                        gift_message: giftMessage.getSubmitParams(remove)
                    })
                ).done(
                    function(result) {
                        giftMessage.reset();
                        _.each(giftMessage.getAfterSubmitCallbacks(), function(callback) {
                            if (_.isFunction(callback)) {
                                callback();
                            }
                        });
                    }
                ).fail(
                    function(response) {
                        errorProcessor.process(response);
                    }
                );
            }
        };
    }
);

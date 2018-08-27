/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (placeOrderAction) {
        /** Override default place order action and add agreement_ids to request */
        return wrapper.wrap(placeOrderAction, function(originalAction, paymentData, redirectOnSuccess, messageContainer) {
            // adding order comments
            var orderCommentConfig = window.checkoutConfig.show_hide_custom_block;
            if(orderCommentConfig) // true
            {
                var order_comments=jQuery('[name="comment-code"]').val();
                if(typeof(paymentData.additional_data) === 'undefined'
                || paymentData.additional_data === null
                ){
                    paymentData.additional_data = {comments:order_comments};
                }else{
                    paymentData.additional_data.comments = order_comments;
                }
            }
            return originalAction(paymentData, redirectOnSuccess, messageContainer);
        });
    };
});
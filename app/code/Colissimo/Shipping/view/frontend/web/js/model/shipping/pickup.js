/*global define*/
define([
    'jquery',
    'Magento_Checkout/js/model/url-builder',
    'mage/storage'
], function($, urlBuilder, storage) {
    'use strict';

    return {
        getUrlForRetrievePickupAddress: function(pickupId, networkCode) {
            return urlBuilder.createUrl(
                '/colissimo/:pickupId/:networkCode',
                {'pickupId':pickupId, 'networkCode':networkCode}
            );
        },

        getUrlForSavePickup: function(quoteId, pickupId, networkCode) {
            return urlBuilder.createUrl(
                '/colissimo/:cartId/:pickupId/:networkCode',
                {'cartId':quoteId, 'pickupId':pickupId, 'networkCode':networkCode}
            );
        },

        getUrlForCurrentPickup: function(quoteId) {
            return urlBuilder.createUrl(
                '/colissimo/:cartId',
                {'cartId':quoteId}
            );
        },

        getUrlForResetPickup: function(quoteId) {
            return urlBuilder.createUrl(
                '/colissimo/:cartId',
                {'cartId':quoteId}
            );
        },

        getPickup: function(pickupId, networkCode) {
            return storage.get(this.getUrlForRetrievePickupAddress(pickupId, networkCode), false);
        },

        currentPickup: function(quoteId) {
            return storage.get(this.getUrlForCurrentPickup(quoteId), false);
        },

        savePickup: function(quoteId, pickupId, networkCode) {
            return storage.put(this.getUrlForSavePickup(quoteId, pickupId, networkCode), false);
        },

        resetPickup: function(quoteId) {
            return storage.delete(this.getUrlForResetPickup(quoteId), false);
        }
    }
});
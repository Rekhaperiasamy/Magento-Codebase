/**
 * Created by root on 10/06/2016.
 */
define([
    'uiComponent',
    'Magenest_AbandonedCartReminder/js/model/notification',
    'Magenest_AbandonedCartReminder/js/model/notificationList'
], function (Component, notification, notificationList) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magenest_AbandonedCartReminder/notification'
        },
        dataScope: 'magenest',
        formId : 'what-the-hell',
        theNotificationList : notificationList({refreshUrl: 'http://127.0.0.1'}),
        initialize: function () {
            this._super();
            this.theNotificationList.refresh();


        }

    });
});

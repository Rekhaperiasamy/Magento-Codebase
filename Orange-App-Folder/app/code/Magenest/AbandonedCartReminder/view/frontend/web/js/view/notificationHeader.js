/**
 * Created by root on 13/06/2016.
 *
 * test test
 */
define([
    "jquery",
    "uiClass",
    'Magento_Ui/js/lib/spinner',
    "Magenest_AbandonedCartReminder/js/view/notificationHeader",
    "underscore"
], function ($, Class,loader, notificationHeader, _) {
    "use strict";
    return Class.extend({
        defaults: {
            closeUrl:'abandonedcartreminder/notify/close',
            counterElement:'span[data-role="toggle-notification"]',
            ulElement: 'ul[data-role="notification-container"]',
            closeElement:'button[data-action="close-notification"]'
        },
        /**
         * Constructor
         */
        initialize: function (config) {

            this.initConfig(config);
            this.bindAction();
            return this;
        },

        bindAction: function() {
            var self = this;

            jQuery(self.ulElement).hide();

            jQuery(self.counterElement).click(function() {
                jQuery(self.ulElement).toggle();
            }) ;

            jQuery(self.closeElement).click(function() {
                //hide the element and send ajax request to  change status is_read to 1

                var liE = jQuery(this).parent('li');
                liE.hide();

                var notifyId = jQuery(this).data('notifyid');
                //send ajax request to the
                jQuery.ajax({
                    url: self.closeUrl,
                    data: {id:notifyId}
                });


            });

        }

    });
});

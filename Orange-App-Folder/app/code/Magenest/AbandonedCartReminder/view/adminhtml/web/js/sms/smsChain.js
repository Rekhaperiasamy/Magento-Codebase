/**
 * Created by root on 14/06/2016.
 */
define([
    "jquery",
    "uiClass",
    'Magento_Ui/js/lib/spinner',
    "Magenest_AbandonedCartReminder/js/sms/smsChain",
    "underscore"
], function ($, Class,loader, smsChain, _) {
    "use strict";
    return Class.extend({
        defaults: {
            mainElement:'table[data-role="sms-table"]',
            bodyElement:'tbody[data-role="sms-tbody"]',
            addBtn: 'div[data-role="sms-add"]',
            deleteBtn: 'span[data-role="sms-del"]',
            templateSelector: 'script[data-role="sms-row-template"]',
            maxId: 1000,
            deleteUrl: 'abandonedcartreminder/rule/deletesms'

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


            $(self.addBtn).on('click',function() {
                //data-role="sms-row-template"
                var template = $(self.templateSelector).html();

                 self.maxId++;

                var newRow = _.template(template)({id:self.maxId});

                $(self.bodyElement).append(newRow);

            });

            //when user click on delete button, hide the element and change value of is_deleted to 1
            $(self.deleteBtn).on('click' ,function() {
                var trLi = $(this).closest('tr');
                var deleteInput = $(this).prev('input[data-role="delete-input"]');

                if (deleteInput.length > 0)  deleteInput.val(1);
                var idSms = $(this).data('idsms');

                if (trLi.length > 0) {

                    trLi.hide();
                    //send ajax request
                    $.ajax({
                        url :self.deleteUrl,
                        data : { id :idSms}
                    });
                }

            });

        }

    });
});

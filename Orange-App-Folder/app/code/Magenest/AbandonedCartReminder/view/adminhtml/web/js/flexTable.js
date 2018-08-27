///var/www/html/woomodule/woocommerceticket/wp-content/plugins/woocommerce-event-ticket/js/pdfchain.js

define([
    "jquery",
    "uiClass",
    'Magento_Ui/js/lib/spinner',
    "Magento_Ui/js/modal/modal",
    "Magenest_AbandonedCartReminder/js/flexTable",
    "underscore"
], function ($, Class,loader,modal, Flex, _) {
    "use strict";
    return Class.extend({
        defaults: {
            /**
             * Initialized solutions
             */
            table:'',
            url : '',
            getEmailTemplateUrl : '',
            activeSelectorEmail:''
        },
        /**
         * Constructor
         */
        initialize: function (config) {

            var self = this;

            this.initConfig(config);

            this.bindAction();
            this._addButton(config);

            return this;
        },

        updateEmailTemplate: function() {
            var self = this;

            var existingOption;

            var activeSelector;
            var newOptions = new Array();

            var existInOption = false;

            
            $.ajax({
                url : self.getEmailTemplateUrl,
                async: false

            }).done(function (data) {
                var existingOption = new Array();

                jQuery('select[data-role="followup-email-template"]').each(function() {
                        activeSelector = this;

                        $(this).find('option').each(function() {
                            existingOption.push(this.value);
                        });
                    });


                if (data.length > 0) {

                        for (var j = 0; j < existingOption.length ; j++ ) {
                            for (var i = 0; i < data.length; i++) {
                            if (existingOption[j] != data[i]['id']) {
                                existInOption = true;

                                newOptions.push(data[i]);


                            }
                        }

                    }
                }





     /*               //outdate
                if (data.length > 0) {
                    for (var i = 0; i < data.length; i++)  {
                        jQuery('select[data-role="followup-email-template"]').each(function() {
                            activeSelector = this;
                            var existingOption = new Array();

                            var newOptions = new Array();

                            $(this).find('option').each(function(){
                                existingOption.push(this.value);
                            });

                            existInOption = false;

                            for (var j = 0; j < existingOption.length ; j++ ) {

                                if (existingOption[j] != data[i]['id']) {
                                    existInOption = true;

                                    newOptions.push();

                                    break;
                                }
                            }

                            if (existInOption==false)  {
                                $(activeSelector).append('<option value="' + data[i]['id'] + '">' + data[i]['label'] + '</option>');

                            }
                        }) ;
                    }

                } //end of out date*/

            })  ;

            //if there is new options then append it
            if (newOptions.length > 0) {
                for (var i = 0; i < newOptions.length; i++) {
                    $(activeSelector).append('<option value="' + newOptions[i]['id'] + '">' + newOptions[i]['label'] + '</option>');

                }

            }
            //get the email template in marketing in json


        },
        bindAction: function () {
            var self = this;

            $('[data-action="delete-row"]').click(function() {
                $(this).parent().parent().remove();
            }) ;
            $('select[data-role="followup-email-template"]').change(function() {
                self.activeSelectorEmail = this;
                var selectedEmail = this;
                var optionSelected = $(this).find("option:selected");
                var valueSelected  = optionSelected.val();
                var role   = optionSelected.data('action');
                

                if (role === 'add-new-email-template')  {
                    this.modal = $('[data-role="wrapper-modal-new-email"]').modal({
                        modalClass: 'modal-followup-email-slide',
                        type: 'slide',
                        transitionEvent : false,
                        trigger:['closed'],
                        buttons: [],
                        closed:function () {

                            self.updateEmailTemplate();

                            $(self.activeSelectorEmail).val(1);
                        }
                    });

                    this.modal.modal('openModal');

                }
            }) ;

            //make the required input
            self.table.find('[data-role="require-anchor"]').not('[data-sample="email-chain"]').addClass('required-entry').addClass('_required');
            self.table.find('[data-require="required-entry"]').not('[data-sample="email-chain"]').addClass('required-entry').addClass('_required');

            self.table.find('[data-sample="email-chain"]').removeClass('required-entry').removeClass('_required');
        },
        escapeRegExp: function (string) {
            return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
        },

        replaceAll: function(string, find, replace) {
            var pin = this;
            return string.replace(new RegExp(pin.escapeRegExp(find), 'g'), replace);
        },

        _addButton:function(config) {
            var self = this;

            var table = self.table.first();
            var addBtn =  table.find('[data-role="add-new-row"]');

            addBtn.click(function() {

                var rowIds =  new Array();
                var template = self.table.find('[data-role="row-pattern"]').html();

                var trElements =self.table.find('tbody').find('tr');

                jQuery(trElements).each(function(index, element) {
                    if (jQuery(element).data('order') != null) {

                        rowIds.push(jQuery(element).data('order'));


                    }
                }) ;


                var row_id=Math.max.apply(rowIds, rowIds);
                var next_id = parseInt(row_id) + 1;


                var templateRow = '<tr ' + ' data-order =' + next_id + '>' + template;
                var valueFind = '/value=\".+\"/';
                templateRow = self.replaceAll(templateRow, valueFind, ' ');

                var find = "[1000]";
                var re = new RegExp(find, 'g');

                var replace = "[" + next_id + "]";

                var res = self.replaceAll(templateRow, find, replace);

                find = '_1000_';
                replace = '_' + next_id + '_';
                re = new RegExp(find, 'g');
                res = self.replaceAll(res, find, replace);

                var newRow =  res + '</tr>';


                var appendRow = jQuery(newRow);

                table.find('tbody').append(newRow);

                self.bindAction();

            });
        }



    });
});

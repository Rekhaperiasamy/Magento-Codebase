/**
 * Copyright © 2015 Netstaretr. All rights reserved.
 * add + - to QUANTITY at cart page
 * By Nalinda Bandara
 */
define([
    "jquery",
    "jquery/ui"
], function ($, mediaCheck) {
    "use strict";

    $.widget('mage.cartCount', {
        options: {
            max: '1000',
            submitObject:null
        },
        VAR: {
            thisElement: null
        },

        _create: function () {

            var self = this;
            this.VAR.thisElement = $(this.element);

            var max = this.options.max;

            var box = this.VAR.thisElement.find('.control input');

            //setTimeout(function(){                    box.trigger('change');
            //},100);

            //+
            this.VAR.thisElement.find('.qty-plus').click(function () {


                var num = parseInt(box.val());
                if (box.attr('disabled') != "disabled") {

                    num++;
                    if (num > max) {
                        num = max;
                    }
                    box.val(num);
                    sumbits();
                }
                $(this).trigger('plusClicked');
            });

            this.VAR.thisElement.find('.qty-minus').click(function () {
                $(this).trigger('minusClickedBefore');


                var num = parseInt(box.val());
                if (box.attr('disabled') != "disabled") {
                    if (num > 1) {
                        num--;
                        box.val(num);
                        
                        sumbits();

                    }
                }
                $(this).trigger('minusClicked');

            });

            box.change( function() {
                sumbits();
            })
            //  this.VAR.thisElement.find('.qty').change(function(){updateCart();});

            var sumbits = function () {
                //setTimeout(function(){                    box.trigger('change');
                //},100);
                //var input = $("<input>").attr("type", "hidden").attr("name", "update_cart_action").val("update_qty");
                //box.trigger('change');
                //console.log('ss');
               if(self.options.submitObject !== null)
               {
                   $(self.options.submitObject).submit();
               }
            }


        }


    });


    return $.mage.cartCount;
});
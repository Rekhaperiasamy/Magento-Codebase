/**
 * Copyright © 2015 Netstaretr. created by Nalinda
 * product listing page custom requiermant
 */
define([
    "jquery",
    "matchMedia",
    "jquery/ui"
], function ($) {
    "use strict";

    $.widget('dilmah.layeredCustom', {
        options: {},
        VAR: {
            device:null
        },



        _create: function () {



            var _self = this;
            //this._initialization(_self);
            this._bindEvents(_self);


            this.resize(_self);


        },

        _bindEvents: function (_self) {
            //resize
            $(window).on('resize', function(){
                _self.resize(_self);
            });

        },

        resize: function (_self) {

            var _self = this;
            //responsive
            mediaCheck({
                media: '(max-width: 767px)',
                //switch to mobile
                entry: function () {
                    if(_self.VAR.device !== "mob")
                    {
                        _self.VAR.device = "mob";
                        _self.mobile();
                    }
                },
                //switch to tablet
                exit: function () {
                    if(_self.VAR.device !== "desk")
                    {
                        _self.VAR.device = "desk";
                        _self.desktop();
                    }
                }
            });


        } ,

        desktop:function () {

            jQuery('.block-title.filter-title').css('display','')


        },
        mobile:function () {
             if(!jQuery('.product-items>.product-item').length)
             {
                 jQuery('.block-title.filter-title').css('display','none')
             }
            else
             {
                 jQuery('.block-title.filter-title').css('display','')

             }

        }



    });

    return $.dilmah.layeredCustom;
});

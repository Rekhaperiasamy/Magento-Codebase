/**
 * Dilmah - productSlider.js
 * Created by Shabith on 2/3/16.
 */

define([
    "jquery",
    "slick"
], function($){
    "use strict";

    $.widget('dilmah.productSlider', {
       VAR: {
            $productSlider: null,
            options: {
               dots: true,
               slidesToShow: 4,
               slidesToScroll: 4,
               autoplay: true,
               duration: 5000,
               responsive: [{
                   breakpoint: 480,
                   settings: {
                       slidesToShow: 1,
                       slidesToScroll: 1
                   }
               },{
                   breakpoint: 768,
                   settings: {
                       slidesToShow: 2,
                       slidesToScroll: 2
                   }
               },{
                   breakpoint: 1024,
                   settings: {
                       slidesToShow: 3,
                       slidesToScroll: 3
                   }
               }]
            }
       },

        _create: function() {
            var _self = this;
            this._initialization(_self);
            this._bindEvents(_self);

            if(_self.options){
                if(_self.options.desktopLength){
                    this.VAR.options.slidesToShow = _self.options.desktopLength;
                    this.VAR.options.slidesToScroll = _self.options.desktopLength;
                }

                if(_self.options.tabLength){
                    this.VAR.options.responsive[2].settings.slidesToShow = _self.options.tabLength;
                    this.VAR.options.responsive[2].settings.slidesToScroll = _self.options.tabLength;
                }

                if(_self.options.mobileLength){
                    this.VAR.options.responsive[1].settings.slidesToShow = _self.options.mobileLength;
                    this.VAR.options.responsive[1].settings.slidesToScroll = _self.options.mobileLength;
                }
            }

            this.VAR.$productSlider.slick(this.VAR.options);
        },

        _initialization: function (_self) {
            this.VAR.$productSlider = $(_self.element);
        },

        _bindEvents: function (_self) {

        }
    });

    return $.dilmah.productSlider;
});
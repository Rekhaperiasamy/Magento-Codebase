/**
 * Dilmah - custom-dropdown.js
 * Created by Shabith on 2/26/2016.
 */

define([
    "jquery",
    "nsdropdown",
    "jquery/ui"
], function($){
    "use strict";

    $.widget('dilmah.customDropdown', {
       VAR: {
           $dropdown: undefined

       },

        _create: function() {
            var _self = this;
            this._init(_self);
            this.bindEvents(_self);

            if(!this.isTouch()){
                this.VAR.$dropdown.each(function(){
                    jQuery(this).nsdropdown();
                });
            }

        },

        _init: function (_self) {
           this.VAR.$dropdown =  $('select.custom');
        },

        isTouch: function(){
            var _isTouch = false;
            if(typeof Modernizr!=='undefined'){
                _isTouch = Modernizr.touch;
            }else{
                _isTouch = 'ontouchstart' in window // works on most browsers
                    || 'onmsgesturechange' in window; // works on ie10;
            }

            return _isTouch;
        },

        reinit: function() {
            if(!this.isTouch()){
                jQuery('select:not(nsd-hide)').each(function(){
                    jQuery(this).nsdropdown();
                });
            }

        },

        bindEvents: function(_self) {
            var _self = _self;
            $(window).on('nsLayeredNavAjaxComplete', function(){
                _self.reinit();
            });
        }


    });

    return $.dilmah.customDropdown;
});
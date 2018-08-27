/**
 * Dilmah - header.js
 * Created by Shabith on 2/8/16.
 */

define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";

    $.widget('dilmah.header', {
       VAR: {
          $headerLinks: null,
          $headerWrapper: null
       },

        _create: function() {
            var _self = this;
            this._initialization();
            this._bindEvents(_self);

           var href= jQuery('.special-orders-wrapper .free-shipping a').attr('href');
            jQuery('.special-orders-wrapper .free-shipping a').attr('href', href+'#free-shipping');

        },

        _initialization: function () {
            this.VAR.$headerLinks = $('.header.links');
            this.VAR.$headerWrapper = $('header .panel.header .header.links')

            this.VAR.$headerLinks.find('> li > a').each(function() {
                var _class = $(this).attr('class');
                $(this).parents('li').addClass(_class);
            });

            this.VAR.$headerWrapper.addClass('active');
        },

        _bindEvents: function (_self) {

        }
    });

    return $.dilmah.header;
});
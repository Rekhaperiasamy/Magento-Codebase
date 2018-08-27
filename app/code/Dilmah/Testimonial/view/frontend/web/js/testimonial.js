/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Testimonial
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

define([
    "jquery",
    "slick"
], function($){
    "use strict";

    $.widget('dilmah.testimonial', {
       VAR: {
          $testimonials: null,
          options: {
              autoplay: true,
              autoplaySpeed: 6000,
              dots: true,
              variableWidth: false,
              draggable: true
          }
       },

        _create: function() {
            var _self = this;
            this._initialization(_self);
            this._bindEvents(_self);

            if(this.options) {
                $.extend(this.VAR.options, this.options);
            }

            this.VAR.$testimonials.slick(this.VAR.options);

        },

        _initialization: function (_self) {

            this.VAR.$testimonials = $(_self.element);

        },

        _bindEvents: function (_self) {


        }




    });

    return $.dilmah.testimonial;


});
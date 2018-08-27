/**
 * codepool - news-events
 * Created by Shabith on 4/25/2016.
 */

/* global define, Dilmah */
define([
    "jquery",
    "gray",
    "jquery/ui",
], function($){
    "use strict";

    $.widget('Dilmah.newsEvents', {
       VAR: {
            $article: null,
            $grayscale: null
       },

        _create: function() {
            var _self = this;
            this._initialization(_self);
            this._bindEvents(_self);
        },

        _initialization: function (_self) {
            this.VAR.$article = $('.article');
            this.VAR.$grayscale = $('.grayscale:not(.grayscale-replaced)');
        },

        _bindEvents: function (_self) {
            _self.VAR.$grayscale.gray();
               _self.VAR.$article.on('hover', function () {
                    $(this).find('.grayscale').toggleClass('grayscale-off');
                });

        }
    });

    return $.Dilmah.newsEvents;
});
/**
 * Copyright © 2015 Netstaretr. created by Nalinda
 * product page tabs
 */
define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";

    $.widget('mage.readMore', {
        //'{"readMore":{"limitTextBy":30,"moreText":"Read More","lessText":"Read Less"}}' >
        options: {
            'limitTextBy': 250,
            'readMore': 'More',
            'readLess': 'Less',
            'targetElement': null
        },
        VAR: {
            'targetElement': null
        },


        _create: function () {

            var _self = this;
            _self.VAR.targetElement = _self.options.targetElement = null ? $(_self.element) : $(_self.element).find(_self.options.targetElement);

            var texts = _self.VAR.targetElement.text();
            var texlen = texts.length;
            if (texts.length > _self.options.limitTextBy) {


                _self.VAR.targetElement.html('<span>' + texts.substring(0, _self.options.limitTextBy) + '</span><span class="view-all-category-desc">.. <a href="#">' + _self.options.readMore + '</a></span><span style="display: none" class="read-more-part">' + texts.substring(_self.options.limitTextBy, texlen) + '</span><span  style="display: none" class="view-less-category-desc">  <a  href="#">' + _self.options.readLess + '</a></span>');

            }

            $("body").on("click", ".view-all-category-desc", function (e) {
                e.preventDefault();
                jQuery(this).parent().find('.read-more-part').css('display', '');
                jQuery(this).parent().find('.view-less-category-desc').css('display', '');
                jQuery(this).css('display', 'none');
            });

            $("body").on("click", ".view-less-category-desc", function (e) {
                e.preventDefault();
                jQuery(this).parent().find('.read-more-part').css('display', 'none');
                jQuery(this).parent().find('.view-all-category-desc').css('display', '');
                jQuery(this).css('display', 'none');
            });


        }


    });

    return $.mage.readMore;
});

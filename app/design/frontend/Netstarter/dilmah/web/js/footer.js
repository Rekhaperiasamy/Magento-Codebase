/**
 * Dilmah - footer.js
 * Created by Shabith on 1/29/16.
 */

define([
    "jquery",
    "underscore",
    "matchMedia",
    "mage/accordion",
    "jquery/jquery.cookie",
    "jquery/ui"
], function ($, _) {
    "use strict";

    $.widget('dilmah.footer', {
        VAR: {
            $newsLetterContainer:null,
            $newsLetterCloseBtn:null,
            $footerWrapper: null,
            _self: null,
            cookieName: null,
            options: {
                cookieName: 'newsletter-popup'
            }
        },

        _create: function () {

            var _self = this;
            this._initialization(_self);
            this._bindEvents(_self);

            //this.showNewsLetter();

            this.resize(_self);
        },

        _initialization: function (_self) {
            //initialization
            this.VAR.$newsLetterContainer = $('.bottom-newsletter-slider-wrapper');
            this.VAR.$newsLetterCloseBtn = $('.bottom-newsletter-slider-wrapper .close-icon');
            this.VAR.$footerWrapper = $('.footer-wrapper');
            if(_self.options) {
                $.extend(this.VAR.options, _self.options);
            }

        },

        _bindEvents: function (_self) {
            //newsletter close button
            this.VAR.$newsLetterCloseBtn.on('click', function(e) {
                e.preventDefault();
                _self.removeNewsLetter();
            });

            //resize
            $(window).on('resize',_.throttle(function(){
                _self.resize(_self)
            }, 100));

        },

        resize: function(_self){
            //responsive
            mediaCheck({
                media: '(max-width: 767px)',
                //switch to mobile
                entry: function(){
                    _self.inMobile(_self);
                },
                //switch to desktop
                exit: function(){
                    _self.inDesktop(_self);
                }
            });
        },

        showNewsLetter: function(forced){
            //check for newsletter
            if(forced){
                //if forced, show newsletter without checking cookie
                this.VAR.$newsLetterContainer.slideDown();
            }else if($.cookie(this.VAR.options.cookieName) !== '1'){
                //show newsletter
                this.VAR.$newsLetterContainer.slideDown();
            }
        },

        removeNewsLetter: function(forced) {
            this.VAR.$newsLetterContainer.slideUp();
            if(!forced) {
                //if it is not forced, remove newsletter and update the cookie
                $.cookie(this.VAR.options.cookieName, '1');
            }

        },

        inDesktop: function(_self) {
            //remove footer links accordion
            if(_self.VAR.$footerWrapper.data('accordion') === 'true'){
                _self.VAR.$footerWrapper.accordion('destroy');
                _self.VAR.$footerWrapper.find('[data-role="tabpanel"]').removeAttr('style');
                _self.VAR.$footerWrapper.data('accordion', 'false');
            }
        },

        inMobile: function(_self) {
            //footer links accordion
            if(_self.VAR.$footerWrapper.data('accordion') !== 'true'){
                _self.VAR.$footerWrapper.accordion({
                    header: '> div:not(.bottom-links):not(.footer-right) [data-role=tab]',
                    collapsible: true,
                    active: [],
                    animate: true,
                    content: '> div:not(.bottom-links):not(.footer-right) [data-role=tabpanel]',
                    heightStyle: "content"
                });
                _self.VAR.$footerWrapper.data('accordion', 'true');
            }
        }


    });

    return $.dilmah.footer;

});

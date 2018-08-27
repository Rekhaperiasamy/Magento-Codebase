/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
define([
    "jquery"
], function ($) {
    "use strict";

    $.widget('mage.faq', {
        options: {
            accordionDuration: 300,
            from: '.header',
            to: 0,
            offSet: -100
        },
        VAR: {
            faqWrapper: null,
            catTitle: null,
            catHeader: null,
            faqOverflow: null,
            mobile: false,
            mode: 'd'
        },

        _create: function () {

            mediaCheck({
                media: '(max-width: 767px)',
                entry: $.proxy(function () {
                    this._toggleMobileMode();
                }, this),
                exit: $.proxy(function () {
                }, this)
            });

            mediaCheck({
                media: '(min-width: 768px) and (max-width: 1023px)',
                entry: $.proxy(function () {
                    this._toggleTabMode();
                }, this),
                exit: $.proxy(function () {
                }, this)
            });

            mediaCheck({
                media: '(min-width: 1024px)',
                entry: $.proxy(function () {
                    this._toggleDesktopMode();
                }, this),
                exit: $.proxy(function () {
                }, this)
            });

            var self = this;
            self.VAR.faqWrapper = $('.faq-wrapper');
            self.VAR.faqOverflow = $('.faq-overflow');
            self.VAR.catTitle = $('.faq-cat-title-wrapper');
            self.VAR.catHeader = $('.faq-header .faq-cat-head');
            var headers = $('.faq-wrapper .faq-cat-title-wrapper');
            var contentAreas = $('.faq-wrapper .faq-content').hide()
                .first().show().end();
            var expandLink = $('.expand-all');
            var mobilecontentAreas = null;
            if (this.VAR.mode != 'd' ) {
                mobilecontentAreas = $('.faq-link-wrapper .faq-link-set').hide();
            }

            /*self.VAR.faqWrapper.accordion(
             {
             header: self.VAR.catTitle,
             collapsible: true,
             active: true,
             heightStyle: "content",
             animate: self.options.accordionDuration
             }
             );*/

            $('.faq-content').accordion(
                {
                    header: $('.faq-question'),
                    collapsible: true,
                    active: true,
                    heightStyle: "content",
                    animate: self.options.accordionDuration
                }
            );

            //clicking link to scroll to anchor point
            $('.faq-cat-name').click(function (e) {
                e.preventDefault();
                var link = $(this).attr("href");
                //trigger accorian item to show (if it is not already open)
                if (!$(link).find('.faq-cat-title-wrapper').hasClass('ui-accordion-header-active')) {
                    $(link).find('.faq-cat-title-wrapper').trigger("click");
                }

                var cthis = $(this);

                //scroll to position only in desktop
                // set timeout for do this after the accordion animation
                setTimeout(function () {
                    var ctop = parseInt(self.VAR.faqWrapper.css("top")) ? parseInt(self.VAR.faqWrapper.css("top")) : 0;
                    var scrollValue = ( $(link).offset().top - ctop - self.VAR.faqOverflow.offset().top);
                    self.VAR.faqWrapper.animate({top: -scrollValue}, self.options.accordionDuration);

                }, self.options.accordionDuration);

                //add selected class to parent
                $('.faq-header .faq-cat-head.selected').removeClass('selected');
                $(this).parent().addClass("selected");

                jQuery('.faq-navs .up ,.faq-navs .down').removeClass('disable');
                if (cthis.parent().index() === 0) {
                    jQuery('.faq-navs .down').addClass('disable');
                } else if (cthis.parent().index() === self.VAR.catHeader.length - 1) {
                    jQuery('.faq-navs .up').addClass('disable');
                }
                $('.faq-category').hide();
                $(link).show();
            });

            $('.faq-cat-name-anchor').click(function (e) {
                e.preventDefault();
                var title = $(this).attr("title");
                var aTag = $("a[name='" + title + "']");
                $('html,body').animate({scrollTop: aTag.offset().top}, 'slow');
            });

            //animate it with arrow up down
            jQuery('.faq-navs .up ,.faq-navs .down').click(function () {
                $('.faq-category').show();
                jQuery('.faq-navs .up ,.faq-navs .down').removeClass('disable');
                var currentItem = 0;
                if (jQuery('.faq-header .faq-cat-head.selected').length) {
                    currentItem = jQuery('.faq-header .faq-cat-head.selected').index();
                    jQuery('.faq-header .faq-cat-head.selected').removeClass('selected');
                }
                if ($(this).hasClass("up")) {
                    currentItem++;
                    if (currentItem >= self.VAR.catHeader.length - 1) {
                        currentItem = self.VAR.catHeader.length - 1;
                    }
                }
                else {
                    currentItem--;
                    if (currentItem <= 0) {
                        currentItem = 0;
                    }
                }
                self.VAR.catHeader.eq(currentItem).find('.faq-cat-name').trigger("click").parent().addClass('selected');
            });

            //animate it with reset
            jQuery('.faq-navs .reset').click(function () {
                var currentItem = 0;
                var link = null;
                if (jQuery('.faq-header .faq-cat-head.selected').length) {
                    currentItem = jQuery('.faq-header .faq-cat-head.selected').index();
                    link = self.VAR.catHeader.eq(currentItem).find('.faq-cat-name').attr("href");
                    //if (!$(link).find('.faq-cat-title-wrapper').hasClass('ui-accordion-header-active')) {
                    //$(link).find('.faq-cat-title-wrapper').trigger("click");
                    //}
                    jQuery('.faq-header .faq-cat-head.selected').removeClass('selected');
                    var ctop = 0;
                    var scrollValue = ( $(link).offset().top - ctop - self.VAR.faqOverflow.offset().top);
                    self.VAR.faqWrapper.animate({top: -scrollValue}, self.options.accordionDuration);

                    //contentAreas['slideUp']();
                    self._sliding('up');
                    var isAllOpen = $(this).data('isAllOpen');
                    expandLink.text('[ + ]').attr('title', 'Expand').data('isAllOpen', isAllOpen);
                }

                $('.faq-category').show();
            });

            $('.faq-mobile-links-header').click(function () {
                var isOpenMenu = !$(this).data('isOpenMenu');
                mobilecontentAreas = $('.faq-link-wrapper .faq-link-set');
                isOpenMenu ? $('.faq-mobile-links-header').addClass('active') : $('.faq-mobile-links-header').removeClass('active');
                mobilecontentAreas[isOpenMenu ? 'slideDown' : 'slideUp']();
                $('.faq-mobile-links-header').data('isOpenMenu', isOpenMenu);
                return false;
            });

            // add the accordion functionality
            headers.click(function () {
                var elementOpen = $(this).next().data('elementOpen');
                // close all panels
                //contentAreas.slideUp();
                self._sliding('up');
                if (!elementOpen) {
                    // open the appropriate panel
                    $(this).next().slideDown();
                    $(this).next().data('elementOpen', true);
                }
                // reset Expand all button
                expandLink.text('[ + ]').attr('title', 'Expand').data('isAllOpen', false);
                // stop page scroll
                return false;
            });

            // hook up the expand/collapse all
            expandLink.click(function () {
                jQuery('.faq-navs .reset').trigger("click");
                var isAllOpen = !$(this).data('isAllOpen');
               // console.log({isAllOpen: isAllOpen, contentAreas: contentAreas})
                contentAreas[isAllOpen ? 'slideDown' : 'slideUp']();

                expandLink.text(isAllOpen ? '[ - ]' : '[ + ]').attr('title', isAllOpen ? 'Collapse' : 'Expand')
                    .data('isAllOpen', isAllOpen);
            });
            //console.log({mobile: self.VAR.mobile})
            if (this.VAR.mode == 'm' ) {
                this._sliding('up');
            } else {
                this._sliding('down');
            }
        },
        /*mobmode:function(){
         console.log('mobmode')
         },
         tabmode:function(){
         console.log('tabmode')

         },
         dskmode:function(){

         console.log('dskmode')
         },*/

        /**
         * change the height for faq wrapper
         */
        changeWrapSize: function () {
        },

        scrollAndResize: function () {
        },

        _toggleMobileMode: function () {
            this.VAR.mobile = true;
            this.VAR.mode = 'm';
            jQuery('.faq-navs .reset').trigger("click");
            $('.faq-sidebar-content-block').appendTo($('.faq-mobile_need_help'));
            $('.faq-mobile-links-header').show();
            $('.faq-link-wrapper .faq-link-set').hide();
            $('.faq-header').hide();
            $('.faq-navs').hide();
            $('.breadcrumbs').show();
            this._sliding('up');
            //console.log('mobile');
            //$('.page-title-wrapper').appendTo($('.columns'));

        },

        _toggleDesktopMode: function () {
            this.VAR.mobile = false;
            this.VAR.mode = 'd';
            $('.faq-sidebar-content-block').appendTo($('.faq-desktop_need_help'));
            $('.faq-mobile-links-header').hide();
            $('.faq-link-wrapper .faq-link-set').show();
            $('.faq-header').show();
            $('.faq-navs').show();
            this._sliding('down');
            //$('.page-title-wrapper').insertBefore($('.dilmah-tfaq-content'));
           // console.log('desktop');
        },
        _toggleTabMode: function () {
            this.VAR.mobile = true;
            this.VAR.mode = 't';
            $('.faq-link-wrapper .faq-link-set').hide();
            $('.faq-mobile-links-header').show();
            $('.faq-header').show();
            $('.faq-navs').show();
            $('.breadcrumbs').show();
            this._sliding('down');
            if($('.faq-desktop_need_help').text()!=''){
                $('.faq-sidebar-content-block').appendTo($('.faq-mobile_need_help'));
            }
            //$('.page-title-wrapper').appendTo($('.columns'));
           // console.log('tab');
        },
        _sliding: function (mode) {
            var contentAreas = $('.faq-wrapper .faq-content').hide();
            var expandLink = $('.expand-all');
            var headers = $('.faq-wrapper .faq-cat-title-wrapper');
            if (mode == 'up') {
                contentAreas['slideUp']();
                contentAreas.data('elementOpen', false);
                expandLink.text('[ + ]').attr('title', 'Collapse').data('isAllOpen', false);
                //headers.find('span.ui-accordion-header-icon').removeClass('ui-icon ui-icon-triangle-1-s');
                //headers.find('span.ui-accordion-header-icon').addClass('ui-icon ui-icon-triangle-1-e');
            } else {
                contentAreas.data('elementOpen', true);
                contentAreas['slideDown']();
                expandLink.text('[ - ]').attr('title', 'Expand').data('isAllOpen', true);
                //headers.find('span.ui-accordion-header-icon').removeClass('ui-icon ui-icon-triangle-1-e');
                //headers.find('span.ui-accordion-header-icon').addClass('ui-icon ui-icon-triangle-1-s');
            }

        }

    });

    return $.mage.faq;
});
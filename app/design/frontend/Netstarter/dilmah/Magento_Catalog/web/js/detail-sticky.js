/**
 * Copyright © 2015 Netstaretr. created by Nalinda
 * product page tabs
 */
define([
    "jquery",
    "matchMedia",
    "jquery/ui"

], function ($) {
    "use strict";

    $.widget('mage.productSticky', {
        options: {
            stickyfrom: 0, // additional space before it come to top of the page
            desktopOnly: true, //only add the sticky in desktop
            desktopSize: 767, // get the min size for adding sticky
            stickyFromItemExtra: 350,
            //stickyFromItem: '#maincontent',
            stickyFromItem: '#block-upsell-heading',
            stickyToItem: 'footer',
            stickyItem: null,
            hideItem: '.product-title-container',
            extraGapTop: 56
        },
        VAR: {
            device: null,
            stikcypos: 0,
            stickyFromItem: null,
            stickyToItem: null,
            stickyItem: null,
            stickyTo: null
        },


        _create: function () {
            var _self = this;
            _self.VAR.stickyFromItem = _self.options.stickyFromItem === null ? $(_self.element).parent() : $(_self.options.stickyFromItem);
            _self.VAR.stickyToItem = $(_self.options.stickyToItem);




            $(window).on('resize', function () {
                _self.resize();
            });
            setTimeout(function () {
                _self.resize();
            }, 100);

            //_self.VAR.stickyFromItem = $(_self.options.stickyFromItem);

            _self.VAR.stickyItem = (_self.options.stickyItem === null) ? $(_self.element) : $(_self.options.stickyItem);
            ccc = _self.VAR.stickyItem;

        },

        resize: function () {

            var _self = this;
            //responsive
            mediaCheck({
                media: '(max-width: ' + _self.options.desktopSize + 'px)',
                //switch to mobile
                entry: function () {
                    if (_self.VAR.device !== "mob") {
                        _self.VAR.device = "mob";
                        _self.mobile();
                    }
                },
                //switch to tablet
                exit: function () {
                    if (_self.VAR.device !== "desk") {
                        _self.VAR.device = "desk";
                        _self.desktop();
                    }
                }
            });

            //when to start the sticky section
            _self.options.stickyfrom = _self.VAR.stickyFromItem.offset().top;
            _self.VAR.stickyTo = _self.VAR.stickyToItem.offset().top;


        },

        desktop: function () {

            this.callSticky();

        },
        mobile: function () {
            //adding tab view for desktop

            this.destroySticky();

        },

        callSticky: function () {
            var _self = this;
            //call scroll function
            var scrollDoneTimer = setTimeout(function () {
                _self.placeSticky();
            }, 100);


            $(window).on('scroll.stab', function (e) {
                clearTimeout(scrollDoneTimer);

                 //if want to aniamte use this
                 scrollDoneTimer = setTimeout(function(){
                 //_self.placeSticky();
                     setTimeout(function(){ _self.placeSticky()},10);
                 },100);

                //_self.placeSticky();

            });


        },
        destroySticky: function () {

            $(window).off('scroll.stab');

        },
        placeSticky: function () {

            var _self = this;
            _self.options.stickyFromItemExtra = jQuery('.page-header').height() + jQuery('.product-title-container').height() - 30;
            //_self.options.stickyfrom = _self.VAR.stickyFromItem.offset().top;
            _self.options.stickyfrom = Math.max(_self.options.stickyfrom, _self.VAR.stickyFromItem.offset().top - _self.options.stickyFromItemExtra);

            var postop = $(window).scrollTop();
            _self.VAR.stickyTo = _self.VAR.stickyToItem.offset().top;


            var offset = postop + _self.options.extraGapTop - _self.options.stickyFromItemExtra;

            var stickyToExactPos = _self.VAR.stickyTo - _self.VAR.stickyItem.height() - _self.options.stickyfrom - _self.options.stickyFromItemExtra;

////////////////////////////

            _self.VAR.parentTag = jQuery('.columns');

            var notscrollheight = _self.VAR.stickyItem.height() - $(_self.options.hideItem).height();

            var stickyHeaderHeight = parseInt($(window).height() - jQuery('.sticky-container.fixed').height());

            //get the value to stop scrolling when reach the footer
            var backscroll = jQuery('footer').offset().top - jQuery(window).scrollTop() - jQuery(window).height() + (jQuery(window).height() - (_self.VAR.stickyItem.height() + jQuery('.sticky-container.fixed').height()));

            if (backscroll > 0) {
                backscroll = 0;
            }


            if (notscrollheight > stickyHeaderHeight) {
                $(_self.options.hideItem).css('display', '');
                _self.VAR.stickyItem.css("top", "");
                if(($('.product-info-price').offset().top) < postop && ($(window).scrollTop()+$(window).height() < $('footer').offset().top))
                {
                    jQuery('.sticky-add-to-cart').addClass('active');
                }
                else
                {
                    jQuery('.sticky-add-to-cart').removeClass('active');

                }
            }
            else if (jQuery('.columns').offset().top < postop) {

                // $(_self.options.hideItem).css('display', 'none');
                $(_self.options.hideItem).slideUp();
                //_self.VAR.stickyItem.css("top", (jQuery('.sticky-container.fixed').height() + jQuery(window).scrollTop() - jQuery('.columns').offset().top + backscroll) + "px");
                _self.VAR.stickyItem.stop().animate({top:jQuery('.sticky-container.fixed').height() + jQuery(window).scrollTop() - jQuery('.columns').offset().top + backscroll},300);
                jQuery('.sticky-add-to-cart').removeClass('active');


            }
            else {
                $(_self.options.hideItem).css('display', '');
                _self.VAR.stickyItem.css("top", "");
                jQuery('.sticky-add-to-cart').removeClass('active');



            }


        }


    });

    return $.mage.productSticky;
});


var ccc;
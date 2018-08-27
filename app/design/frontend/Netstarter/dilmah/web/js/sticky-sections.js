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

    $.widget('dilmah.stickySection', {
        options: {
            stickyfrom: 0, // additional space before it come to top of the page
            desktopOnly: true, //only add the sticky in desktop
            desktopSize: 767, // get the min size for adding sticky
            stickyFromItemExtra: 350,
            stickyFromItem: '#block-upsell-heading',
            stickyToItem: 'footer',
            stickyItem: null,
            hideItem: '.product-title-container',
            extraGapTop: 56,
            stickyToBefore:0
        },
        VAR: {
            device: null,
            stikcypos: 0,
            stickyFromItem: null,
            stickyToItem: null,
            stickyItem: null,
            stickyTo: null,
            stikcyHeight: 0
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
            if( _self.VAR.stickyFromItem.length)
            {
                _self.options.stickyfrom = _self.VAR.stickyFromItem.offset().top;
            }
            else{
                _self.options.stickyfrom = 0;
            }
            $(_self.options.hideItem).css('display', 'block');
            _self.VAR.stikcyHeight = $(  _self.VAR.stickyItem ).outerHeight();
            $(_self.options.hideItem).css('display', '');


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
                scrollDoneTimer = setTimeout(function () {
                    //_self.placeSticky();
                    setTimeout(function () {
                        _self.placeSticky()
                    }, 10);

                    setTimeout(function(){

                        if( $(_self.options.hideItem).height()) _self.placeSticky()

                    },120);


                }, 100);


                //_self.placeSticky();

            });


        },
        destroySticky: function () {

            $(window).off('scroll.stab');

        },
        placeSticky: function () {

            var _self = this;

            var postop = $(window).scrollTop();

            var windowArea =  $(window).height();
            var stickyHeaderHeight = _self.VAR.stikcyHeight;


            //get the value to stop scrolling when reach the footer

            _self.VAR.stickyTo = _self.VAR.stickyToItem.offset().top - _self.options.stickyToBefore;


            if (stickyHeaderHeight >= windowArea) {
                $(_self.options.hideItem).css('display', '');
                _self.VAR.stickyItem.css('top','');

            }
            else if (_self.options.stickyfrom < postop) {
                $(_self.options.hideItem).css('display', 'none');
                var backscroll = _self.VAR.stickyTo - jQuery(window).scrollTop()  + - _self.VAR.stickyItem.outerHeight();
                if (backscroll > 0) {
                    backscroll = 0;
                }


                //_self.VAR.stickyItem.css("top", (jQuery('.sticky-container.fixed').height() + jQuery(window).scrollTop() - jQuery('.columns').offset().top + backscroll) + "px");
                _self.VAR.stickyItem.stop().animate({top: jQuery(window).scrollTop() - _self.options.stickyfrom + backscroll}, 300);
            }
            else {
                $(_self.options.hideItem).css('display', '');
                _self.VAR.stickyItem.css("top", "");

            }

        }





});

return $.dilmah.stickySection;
})
;

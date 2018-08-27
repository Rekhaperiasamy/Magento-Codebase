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

    $.widget('mage.tabcatalog', {
        options: {
            stickyfrom: 0, // additional space before it come to top of the page
            desktopOnly: true, //only add the sticky in desktop
            desktopSize: 767, // get the min size for adding sticky
            stickyFromItem: '#block-upsell-heading',
            stickyFromItemExtra: 350,
            stickyToItem: 'footer',
            stickyItem: null,
            hideItem: '.product-title-container', //what items to be show when sticky and hide when not sticky
            extraGapTop: 100, //extra gap to be added for using anchor points
            anchorLinkSection: null,
        },
        VAR: {
            device: null,
            stikcypos: 0,
            stickyFromItem: null,
            stickyToItem: null,
            stickyItem: null,
            stickyTo: null,
            anchorLinkSection: null,
            stickyHeight: 0,
            anchorLinkItems:null,
            anchorLinkTarget:null
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

            _self.VAR.stickyItem = _self.options.stickyItem === null ? $(_self.element) : $(_self.options.stickyFromItem);
            _self.VAR.anchorLinkSection = _self.options.anchorLinkSection === null ? $(_self.element) : _self.VAR.stickyItem.find(_self.options.anchorLinkSection);

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
            _self.options.stickyfrom = _self.VAR.stickyFromItem.offset().top - _self.options.stickyFromItemExtra;
            _self.VAR.stickyTo = _self.VAR.stickyToItem.offset().top;

            //get sticky section height
            if (_self.VAR.stickyItem.hasClass('fixed')) {
                this.VAR.stickyHeight = _self.VAR.stickyItem.height();

            }
            else {
                _self.VAR.stickyItem.css('opacity', 0).addClass('fixed');
                this.VAR.stickyHeight = _self.VAR.stickyItem.height();
                _self.VAR.stickyItem.removeClass('fixed').css('opacity', 1);
            }


        },

        desktop: function () {
            var _self = this;
            this.callSticky();

            _self.VAR.anchorLinkItems = _self.VAR.stickyItem.find('a[href*=\\#]:not([href=\\#])');
            _self.VAR.anchorLinkTarget = [];


            
            


            for (var  i= 0; i <_self.VAR.anchorLinkItems.length ; i++) {

               var item = _self.VAR.anchorLinkItems.eq(i).attr('href');
                _self.VAR.anchorLinkTarget.push($(item));

            }




            //adding scroll to function

            _self.VAR.anchorLinkItems.click(function () {
                if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') || location.hostname === this.hostname) {

                    var target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                    if (target.length) {
                        var tops = target.offset().top - _self.VAR.stickyHeight;
                        if (!_self.VAR.stickyItem.hasClass('fixed')) {
                            tops = target.offset().top - _self.VAR.stickyHeight - _self.VAR.stickyItem.height();

                        }
                        $('html,body').animate({
                            scrollTop: tops
                        }, 1000);

                        return false;
                    }
                }
            });

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
                 _self.placeSticky();
                 },100);

                //_self.placeSticky();

            });


        },
        destroySticky: function () {

            $(window).off('scroll.stab');
            this.VAR.stickyItem.css('opacity', 0).removeClass('fixed');


        },
        placeSticky: function () {
            _self = this;
            _self.options.stickyfrom = Math.max(_self.options.stickyfrom, _self.VAR.stickyFromItem.offset().top - _self.options.stickyFromItemExtra);
            var _self = this;
            var postop = $(window).scrollTop();
            var stikyend = jQuery('footer').offset().top -250;

            if (_self.options.stickyfrom < postop && stikyend>postop) {
                _self.VAR.stickyItem.addClass("fixed");

            }
            else {
                _self.VAR.stickyItem.removeClass("fixed");
            }

            //mark current anchor

             var clink;
           if(_self.VAR.anchorLinkTarget) {
                for (var i = 0; i < _self.VAR.anchorLinkTarget.length; i++) {
                    if (postop > _self.VAR.anchorLinkTarget[i].offset().top - _self.VAR.stickyHeight - 20) {
                        clink = $(_self.VAR.anchorLinkItems[i]);
                    }
                }
           }

            if(clink !== undefined)
            {
                    _self.VAR.anchorLinkSection.find('a').removeClass("active");
                    clink.addClass("active");
            }

            
            

        }


    });

    return $.mage.tabcatalog;
});
   var sss;
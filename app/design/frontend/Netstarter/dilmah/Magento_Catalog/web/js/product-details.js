/**
 * Copyright © 2015 Netstaretr. created by Nalinda
 * product page tabs
 */
define([
    "jquery",
    "jquery/ui",
    'matchMedia',
    'mage/tabs',
    "productSticky",
    "mage/gallery/gallery"

], function ($) {
    "use strict";

    $.widget('mage.productDetails', {
        options: {},
        VAR: {
            device: null,
            stickeyHeight: 0
        },


        _create: function () {


            //add jquery bridge to avoid conflict with mage.accordion
            $.widget.bridge('ui_accordion', $.ui.accordion);


            $('.product-info-main').mage('productSticky', {
                'extraGapTop': 270
            });
            $('.sticky-container').mage('stickymenu', {
                'extraGapTop': 147, 'anchorLinkSection': '.section-navigator'//,'stickyFromItem':'#block-upsell-heading'
            });
            var _self = this;
            //this._initialization(_self);
            this._bindEvents(_self);


            //add accordion to scroll section
            jQuery('#reviews').ui_accordion({
                "header": ".write-review",
                "openedState": "active",
                "collapsible": true,
                "animate": true,
                "active": false,
                heightStyle: "content"
            });
            //cms section
            jQuery('.recipe-section').ui_accordion({
                "header": "h2",
                "openedState": "active",
                "collapsible": true,
                "animate": true,
                "active": false,
                heightStyle: "content",
                activate: function (event, ui) {
                    _self.scrolltotarget(ui.newHeader)
                }
            });

            //attribute check
            if (jQuery.trim(jQuery('.additional-attributes-wrapper').text()) === '') {
                jQuery('.additional-attributes-wrapper').addClass('no-attributes');
            }

            //scroll to review function
            jQuery('.product-title-container > .product-reviews-summary > .reviews-actions-change a').click(function (e) {
                e.preventDefault();

                jQuery('#reviews').css('display', 'block')
                if ($('.write-review').next().css('display') == "none") {
                    jQuery('.write-review').trigger('click');
                }

                var tops = jQuery('.write-review').offset().top;
                if (_self.VAR.device == "desk") {
                    tops -= _self.VAR.stickeyHeight + 80;
                }

                $('html,body').animate({
                    scrollTop: tops
                }, 400);


            });

            if (window.location.href.split('#addreview').length > 1) {
                setTimeout(function () {
                    jQuery('#reviews').css('display', 'block')
                    if ($('.write-review').next().css('display') == "none") {
                        jQuery('.write-review').trigger('click');
                    }

                    var tops = jQuery('.write-review').offset().top;
                    if (_self.VAR.device == "desk") {
                        tops -= _self.VAR.stickeyHeight + 80;
                    }

                    $('html,body').animate({
                        scrollTop: tops
                    }, 400);
                }, 1000);

            }
            //page come from other location
            else if (window.location.href.split('=cart').length > 1) {
               if(jQuery('.option-select-heading').length)
               {
                   setTimeout(function () {
                       var tops = jQuery('.option-select-heading').offset().top;
                       console.log(tops);
                       $('html,body').animate({
                           scrollTop: tops
                       }, 400);
                   }, 1000);
               }
               else if ($('.sticky-add-to-cart:visible').length) {
                   setTimeout(function () {
                       jQuery('html,body').animate({
                           scrollTop: jQuery('.product-options-wrapper').offset().top
                       }, 400);
                   }, 1000);
               }

            }
            else if (window.location.href.split('startcustomization=1').length > 1) {
                setTimeout(function () {
                    var tops = jQuery('.mix-match-heading').offset().top;
                    jQuery('html,body').animate({
                        scrollTop: tops
                    }, 400);
                }, 1000);
            }

            //stop a link for sociel sharing - firefox
            jQuery('.social-links a').click(function (e) {
                e.preventDefault();
                popup('https://' + $(this).attr('data-link').split(',')[0].toString(), $(this).attr('data-link').split(',')[1], $(this).attr('data-link').split(',')[2]);
            });

            function popup(link, name, win) {
                window.open(link, name, win);
                return false;
            }

            //mobile sticky bottom for add to cart
            //change qty
            jQuery(".mobile-qty-click").click(function () {
                jQuery('#qty').attr('value', jQuery('#mobile-qty').val());
            });
            jQuery(".qty-main-btn").click(function () {
                jQuery('#mobile-qty').attr('value', jQuery('#qty').val());
            });

            jQuery('#mobile-product-addtocart-button').click(function () {
                jQuery('#product-addtocart-button').trigger('click');

                //in mobile scroll to error
                setTimeout(function () {
                    if (jQuery('.product-options-wrapper .mage-error:visible').length) {
                        $('html,body').animate({
                            //scrollTop: jQuery('.product-options-wrapper').offset().top
                            scrollTop: jQuery('.product-options-wrapper .mage-error:visible').eq(0).offset().top
                        }, 400);
                    }

                }, 500);

            });


            //scroll to tab icons for product additonal links
            jQuery('.recommendation .pairing').click(function () {
                if (_self.VAR.device == "mob") {
                    if (jQuery('#tab-label-description').attr('aria-selected') == 'true') {
                        jQuery('#tab-label-description').trigger('click');
                    }
                    jQuery('#tab-label-description').trigger('click');
                }
                else {
                    jQuery('.brewing').trigger('click');
                }
            })
            jQuery('.recommendation .cooking,.recommendation .cocktail').click(function () {

                if (_self.VAR.device == "mob") {
                    if (jQuery('#tab-label-brewing').attr('aria-selected') == 'true') {
                        jQuery('#tab-label-brewing').trigger('click');
                    }
                    jQuery('#tab-label-brewing').trigger('click');

                }
                else {
                    jQuery('.recipe').trigger('click');
                }

            });

            //change mobile and desktop dropdown conection
            $('#qty').change(function () {
                $('#mobile-qty').val($('#qty').val())
            });
            $('#mobile-qty').change(function () {
                $('#qty').val($('#mobile-qty').val())
            });

            jQuery('body').on('mousemove', '.modal-popup ,.submenu', function (e) {
                e.stopPropagation();
            });

            //mix and match
            var mixMatchWord = {
                1: 'one',
                2: 'two',
                3: 'three',
                4: 'four',
                5: 'five',
                6: 'six',
                7: 'seven',
                8: 'eight',
                9: 'nine',
                10: 'ten',
                12: 'twelve',
                14: 'fourteen',
                16: 'sixteen',
                18: 'eighteen',
                20: 'twenty'
            };
            var mm_count = $('#mix-and-match-pack-count').text();
            $('#mix-and-match-count').text(mixMatchWord[mm_count]);
            //stop background moving


            this.resize(_self);

        },

        _bindEvents: function (_self) {
            //resize
            $(window).on('resize', function () {
                _self.resize(_self);
            });

        },

        resize: function (_self) {
            var _self = this;


            //responsive
            mediaCheck({
                media: '(max-width: 767px)',
                //switch to mobile
                entry: function () {
                    if (_self.VAR.device !== "mob") {

                        _self.mobile();
                        _self.VAR.device = "mob";
                    }
                },
                //switch to tablet
                exit: function () {
                    if (_self.VAR.device !== "desk") {

                        _self.desktop();
                        _self.VAR.device = "desk";
                    }
                }
            });


        },

        desktop: function () {

            //removing tab view for desktop
            var _self = this;

            jQuery('.sticky-container').css('opacity', 0);
            jQuery('.sticky-container').addClass('fixed')
            _self.VAR.stickeyHeight = jQuery('.sticky-container').height();
            jQuery('.sticky-container').removeClass('fixed').css('opacity', 1)

            $.widget.bridge('ui_accordion', $.ui.accordion);

            if (_self.VAR.device === "mob") {
                $('.details-tabs').ui_accordion('destroy');
            }
            $('.details-tabs>.content').css("display", "block");

            if (jQuery('.fotorama').length) {
                jQuery('.fotorama').data('fotorama').stopAutoplay();
            }


        },
        mobile: function () {
            var _self = this;

            _self.VAR.stickeyHeight = 0;

            //adding tab view for desktop
            $.widget.bridge('ui_accordion', $.ui.accordion);

            $('.details-tabs').ui_accordion({
                "openedState": "active",
                "collapsible": true,
                "animate": true,
                "active": false,
                heightStyle: "content",
                activate: function (event, ui) {
                    _self.scrolltotarget(ui.newHeader)
                }
            });

            //start auto rotation gallery

            var fotoramacheck = setInterval(function () {
                if (jQuery('.fotorama').length) {
                    jQuery('.fotorama').data('fotorama').startAutoplay(5000);
                    clearInterval(fotoramacheck);
                }

            }, 500);

            //change text for update cart
            $('#mobile-product-addtocart-button').html($('#product-updatecart-button').html());

            $('#mobile-product-addtocart-button').click(function () {
                $('#product-updatecart-button').trigger('click');
            });


        },
        scrolltotarget: function (s) {
            var headerheight = jQuery('.sticky-container.fixed').height() == null ? 0 : jQuery('.sticky-container.fixed').height();
            if (s.length) {


                //if (this.VAR.device == "mob") {
                $('html,body').animate({
                    scrollTop: $(s).offset().top - headerheight
                }, 400);

                //}
            }

        }


    });

    return $.mage.productDetails;
});

/**
 * Dilmah - productSlider.js
 * Created by Shabith on 2/3/16.
 */

define([
    "jquery",
    "slick",
    "jquery/ui"
], function ($) {
    "use strict";

    $.widget('dilmah.productDetailSlider', {
        options: {
            desktopOnly: true,
            desktopSize: 480,
            device:null
        },
        VAR: {
            $productSlider: null,
            device: null,
            options: {
                dots: false,
                slidesToShow: 4,
                slidesToScroll: 4,
                responsive: [{
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        autoplay: true,
                        duration: 5000
                    }
                }, {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                }, {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3
                    }
                }]
            }
        },

        _create: function () {

            var _self = this;


            $(window).on('resize', function () {
                _self.resize();
            });
            setTimeout(function () {
                _self.resize();
            }, 100);

        },

        resize: function () {

            var _self = this;
            this._initialization(_self);
            this._bindEvents(_self);

            if (_self.options) {
                if (_self.options.desktopLength) {
                    this.VAR.options.slidesToShow = _self.options.desktopLength;
                    this.VAR.options.slidesToScroll = _self.options.desktopLength;
                }

                if (_self.options.tabLength) {
                    this.VAR.options.responsive[2].settings.slidesToShow = _self.options.tabLength;
                    this.VAR.options.responsive[2].settings.slidesToScroll = _self.options.tabLength;
                }

                if (_self.options.mobileLength) {
                    this.VAR.options.responsive[1].settings.slidesToShow = _self.options.mobileLength;
                    this.VAR.options.responsive[1].settings.slidesToScroll = _self.options.mobileLength;
                }

                //override for checkout page
                if($('body').hasClass('checkout-cart-index')){
                    this.VAR.options.slidesToShow = 4;
                    this.VAR.options.slidesToScroll = 4;
                    ///tab
                    this.VAR.options.responsive[2].settings.slidesToShow = 3;
                    this.VAR.options.responsive[2].settings.slidesToScroll = 3;


                }
            }

          var obj = $(this.element);
            if (this.options.desktopOnly) {
                if (this.options.desktopOnly) {
                    mediaCheck({
                        media: '(max-width: ' + _self.options.desktopSize + 'px)',
                        //switch to mobile
                        entry: function () {
                            if ( obj.attr('data-device') !== "mob") {
                                obj.attr('data-device',"mob");
                                _self.mobile();
                            }
                        },
                        //switch to tablet
                        exit: function () {
                            if (obj.attr('data-device') !== "desk") {
                                obj.attr('data-device',"desk");
                                _self.desktop();
                                //_self.mobile();
                            }
                        }
                    });
                }
                else {
                    this.VAR.$productSlider = $(this.element);
                    this.VAR.$productSlider.slick(this.VAR.options);
                }

            }

        },

        desktop: function () {
            this.VAR.$productSlider = $(this.element);
            $('.see-more-products').remove();
            var $pitems = this.VAR.$productSlider.find('.product-item');
            $pitems.css('display', '');
            this.VAR.$productSlider = $(this.element);
            this.VAR.$productSlider.slick(this.VAR.options);
        },
        mobile: function () {
            var _self = this;
            this.VAR.$productSlider = $(this.element);
            if (this.VAR.$productSlider.hasClass('slick-initialized')) {
                this.VAR.$productSlider.slick('unslick');
            }

            var displaycount = 0;
            var $pitems = this.VAR.$productSlider.find('.product-item');

            $pitems.css('display', 'none');


            _self.VAR.$productSlider.after('<div class="see-more-products">See More</div>');
            var thisseemore =   _self.VAR.$productSlider.parent().find('.see-more-products');

            thisseemore.click(function () {

                if ($pitems.length > displaycount) {
                    $pitems.eq(displaycount).css('display', 'block');
                    displaycount++;
                    if ($pitems.length > displaycount) {
                        $pitems.eq(displaycount).css('display', 'block');
                        displaycount++;
                    }
                }
                    if ($pitems.length <= displaycount)
                    {
                        thisseemore.remove();
                    }

            });
            thisseemore.trigger('click');
        },


        _initialization: function (_self) {

        },

        _bindEvents: function (_self) {

        }
    });

    return $.dilmah.productDetailSlider;
});
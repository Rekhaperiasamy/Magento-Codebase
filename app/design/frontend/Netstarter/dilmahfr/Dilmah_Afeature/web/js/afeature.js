/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_ProductSlider
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

/**globals mediaCheck **/

define([
    "jquery",
    "slick",
    "matchMedia",
    "jquery/ui"
], function ($) {
    "use strict";

    $.widget('dilmah.afeature', {
        VAR: {
            $afeature: null,
            delaydevicefix: true,
            options: {
                autoplay: true,
                autoplaySpeed: 3000,
                dots: true,
                draggable: true,
                appendArrows: $('.main-banner-arrows'),
                appendDots: $('.main-banner-dots')

            }
        },

        _create: function () {
            var _self = this;
            this._initialization(_self);
            this._bindEvents(_self);

            if (this.options) {
                $.extend(this.VAR.options, this.options);
            }

            this.resize(_self);

            this.VAR.$afeature.slick(this.VAR.options);
            this.VAR.$afeature .css("opacity",1); //fixing image tag show



        },

        _initialization: function (_self) {

            this.VAR.$afeature = $(_self.element);

        },

        _bindEvents: function (_self) {
            //resize
            $(window).on('resize', function () {
                _self.resize(_self);
            });

        },

        resize: function (_self) {
            //responsive
            mediaCheck({
                media: '(max-width: 767px)',
                //switch to mobile
                entry: function () {
                    _self._loadImages('mobile');
                },
                //switch to tablet
                exit: function () {
                    if ($(window).width() < 1024) {
                        _self._loadImages('tablet');
                    }
                }
            });

            //responsive
            mediaCheck({
                media: '(max-width: 1023px)',
                //switch to tablet
                entry: function () {
                    _self._loadImages('tablet');
                },
                //switch to desktop
                exit: function () {
                    if ($(window).width() > 1023) {

                        //retina
                        if(_self._isRetinaDisplay()){
                            _self._loadImages('retina');
                        }else{
                            _self._loadImages('desktop');
                        }

                    }
                }
            });
        },

        _loadImages: function (type) {
           var _self = this;
            //fixing tab call in mobile
            setTimeout(function () {
                _self.VAR.delaydevicefix = true;
            }, 10);
            if (_self.VAR.delaydevicefix) {
                _self.VAR.$afeature.find('.afeature-item img[data-desktop]').each(function () {
                    $(this).attr('src', $(this).data(type));
                });

                if (_self.VAR.$afeature.hasClass('slick-initialized')) {
                    _self.VAR.$afeature.slick('setPosition');
                }
                _self.VAR.delaydevicefix = false;
            }



        },

        _isRetinaDisplay: function() {
            if (window.matchMedia) {
                var mq = window.matchMedia("only screen and (min--moz-device-pixel-ratio: 1.3), only screen and (-o-min-device-pixel-ratio: 2.6/2), only screen and (-webkit-min-device-pixel-ratio: 1.3), only screen  and (min-device-pixel-ratio: 1.3), only screen and (min-resolution: 1.3dppx)");
                return (mq && mq.matches || (window.devicePixelRatio > 1));
            }else {
                //fallback
                return false;
            }
        }



    });

    return $.dilmah.afeature;


});
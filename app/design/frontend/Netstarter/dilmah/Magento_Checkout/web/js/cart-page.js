/**
 * Copyright © 2015 Netstaretr. created by Nalinda
 * product listing page custom requiermant
 */
define([
    "jquery",
    "jquery/ui",
    "matchMedia"

], function ($) {
    "use strict";

    $.widget('mage.cartPage', {
        options: {},
        VAR: {
        },


        _create: function () {

            var _self = this;


            $(window).on('resize', function () {
                _self.resize(_self);
            });
            this.resize(_self);

            //change mobile banner place
            jQuery('.page-title-wrapper').before(jQuery('.cart-banner-mobile'));
        },


        resize: function (_self) {

            var _self = this;
            //responsive
            mediaCheck({
                media: '(max-width: 767px)',
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




        },

        desktop: function () {
            jQuery('.cart-discount').wrapInner('<div class="wrap-promo"></div>');
            jQuery('.cart-discount').append(jQuery('.continue-shopping-wrap'));

        },
        mobile: function () {


        }


    });

    return $.mage.cartPage;
});
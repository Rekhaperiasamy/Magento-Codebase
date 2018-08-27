/**
 * Dilmah - mobile-menu.js
 * Created by Shabith on 2/8/16.
 */

/* globals mediaCheck, define */
define([
    "jquery",
    "underscore",
    "jquery/ui",
    "matchMedia",
    "mage/menu"
], function($, _){
    "use strict";

    $.widget('dilmah.mobileMenu', {
       VAR: {
        $navigation: null,
        $nav: null,
        $header: null,
        $headerWrapper: null,
        $navSectionLinks: null,
        $langSwitcher: null,
        $curSwitcher: null,
        $teaTypes: null,
        $teaTypesWrapper: null,
        $ma_menuTitle : null,
        $ma_sectionSelected : null
       },

        _create: function() {

            var _self = this;
            this._initialization();
            this._bindEvents(_self);

            this.resize(_self);

            this.ma_mobileMenu(_self);


        },

        _initialization: function () {
            this.VAR.$navigation = $('.header-navigation-wrapper').data('desktop', 'true');
            this.VAR.$nav = this.VAR.$navigation.find('nav.navigation[role=navigation]');
            this.VAR.$header = $('header.page-header');
            this.VAR.$headerWrapper = $('.header-content-wrapper');
            this.VAR.$navSectionLinks = $('.nav-sections ul.header.links');
            this.VAR.$langSwitcher = $('#switcher-language-nav');
            this.VAR.$curSwitcher = $('#switcher-currency-nav');
            this.VAR.$teaTypes = $('.tea-types');
            this.VAR.$specialOrderWrapper = $('.special-orders-wrapper');
            this.VAR.$specialOrderLi = this.VAR.$specialOrderWrapper.find('.special-order').parents('li');
            this.VAR.$freeShippingLi = this.VAR.$specialOrderWrapper.find('.free-shipping');
            this.VAR.$ma_menuTitle = $('.sidebar-main .block-collapsible-nav .block-collapsible-nav-title strong');
            this.VAR.$ma_sectionSelected = $('.sidebar-main .block-collapsible-nav .block-collapsible-nav-content').find('>ul >li.nav.item.current');
        },

        _bindEvents: function (_self) {
            //resize
            $(window).on('resize',_.throttle(function(){
                _self.resize(_self);
            }, 100));
        },

        ma_mobileMenu: function (_self){
            var selectedTxt = _self.VAR.$ma_sectionSelected.text();
            if(selectedTxt === ''){
                if(jQuery('body').hasClass('wishlist-index-share')){
                    selectedTxt = 'Share Wishlist';
                }
            }
            _self.VAR.$ma_menuTitle.text(selectedTxt);
        },

        resize: function (_self){
            //move menu in tab and mobile
            mediaCheck({
                media: '(max-width: 1023px)',
                //switch to mobile/tablet
                entry: function() {
                   if(_self.VAR.$navigation.data('desktop') === 'true'){
                       //move main navigation
                       _self.VAR.$header.append(_self.VAR.$navigation);
                       _self.VAR.$navigation.data('desktop', 'false');
                   }
                },
                //switch to desktop
                exit: function() {
                    if(_self.VAR.$navigation.data('desktop') === 'false' || _self.VAR.$navigation.data('desktop') === undefined){
                        //move main navigation
                        _self.VAR.$headerWrapper.append(_self.VAR.$navigation);
                        _self.VAR.$navigation.data('desktop', 'true');
                    }

                }
            });

            //move special order in mobile and tablet
            mediaCheck({
                media: '(max-width: 767px)',
                //switch to mobile
                entry: function() {
                    if(_self.VAR.$specialOrderLi.data('mobile') === 'false' || _self.VAR.$specialOrderLi.data('mobile') === undefined) {
                        _self.VAR.$nav.find('>ul').append(_self.VAR.$specialOrderLi);
                        _self.VAR.$specialOrderLi.data('mobile', 'true');
                    }
                    if(_self.VAR.$freeShippingLi.data('mobile') === 'false' || _self.VAR.$freeShippingLi.data('mobile') === undefined) {
                        _self.VAR.$nav.find('>ul').append(_self.VAR.$freeShippingLi);
                        _self.VAR.$freeShippingLi.data('mobile', 'true');
                    }

                },
                //switch to tablet/desktop
                exit: function() {
                    if(_self.VAR.$specialOrderLi.data('mobile') === 'true') {
                        _self.VAR.$specialOrderWrapper.append(_self.VAR.$specialOrderLi);
                        _self.VAR.$specialOrderLi.data('mobile', 'false');
                    }
                    if(_self.VAR.$freeShippingLi.data('mobile') === 'true') {
                        _self.VAR.$specialOrderWrapper.append(_self.VAR.$freeShippingLi);
                        _self.VAR.$freeShippingLi.data('mobile', 'false');
                    }

                }
            });
        }
    });

    return $.dilmah.mobileMenu;
});
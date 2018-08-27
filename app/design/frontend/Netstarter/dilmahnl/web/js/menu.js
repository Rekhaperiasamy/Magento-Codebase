/**
 * Dilmah - menu.js
 * Created by Shabith on 2/9/16.
 */

define([
    "jquery",
    "mage/menu",
    "jquery/ui"
], function($){
    "use strict";

    $.widget('dilmah.menu', $.mage.menu, {

        VAR: {
            $navigation: null,
            $nav: null,
            $navSectionLinks: null,
            $langSwitcher: null,
            $curSwitcher: null,
            $teaTypes: null,
            $teaTypesWrapper: null,
            $mobNav: null,
            $mobSearch: null,
            $search: null
        },

        _create: function() {

            this._super();
        },

        _init: function(){

            this.VAR.$navigation = $('.header-navigation-wrapper').data('desktop', 'true');
            this.VAR.$nav = this.VAR.$navigation.find('nav.navigation[role=navigation]');
            this.VAR.$navSectionLinks = $('.nav-sections ul.header.links');
            this.VAR.$langSwitcher = $('#switcher-language-nav');
            this.VAR.$curSwitcher = $('#switcher-currency-nav');
            this.VAR.$teaTypes = $('.tea-types');
            this.VAR.$mobNav = $('[data-action="toggle-mobile-nav"]');
            this.VAR.$mobSearch = $('[data-action="toggle-mobile-search"]');
            this.VAR.$search = $('.block-search');


            this.amendMenu();

            this.addEvents(this);


            this._super();
        },

        amendMenu: function(){
            //add Account and Settings content to main navigation

            //Account Section
            var liContent = [
                '<li class="level0 nav-extra no-show-all level-top parent ui-menu-item mobile-only" role="presentation">',
                '<a id="ui-id-11" href="/tea-ranges.html" class="level-top ui-corner-all" aria-haspopup="true" role="menuitem">',
                '<span class="ui-menu-icon ui-icon"></span><span>' + $.mage.__('My Account') + '</span>',
                '</a>',
                '</li>'
            ].join('');

            var $ul = $('<ul />')
                .addClass('level0 mega-menu ui-menu ui-widget ui-widget-content ui-corner-all submenu')
                .attr({
                    'role': 'menu',
                    'aria-hidden': 'true',
                    'aria-expanded': 'false',
                    'tab-index': '-1'
                }).hide().append(this.VAR.$navSectionLinks);

            var $liContent = $(liContent).append($ul);
            this.VAR.$nav.find('>ul').append($liContent);

            //Settings Section
           /* var liContent2 = [
                '<li class="level0 nav-extra no-show-all last level-top parent ui-menu-item mobile-only" role="presentation">',
                '<a id="ui-id-12" href="#" class="level-top ui-corner-all" aria-haspopup="true" role="menuitem">',
                '<span class="ui-menu-icon ui-icon"></span><span>' + $.mage.__('Settings') + '</span>',
                '</a>',
                '</li>'
            ].join('');*/

           /* var $ul2 = $('<ul />')
                .addClass('level0 mega-menu ui-menu ui-widget ui-widget-content ui-corner-all submenu')
                .attr({
                    'role': 'menu',
                    'aria-hidden': 'true',
                    'aria-expanded': 'false'
                }).hide().append(this.VAR.$langSwitcher).append(this.VAR.$curSwitcher);*/

            /*var $liContent2 = $(liContent2).append($ul2);
            this.VAR.$nav.find('>ul').append($liContent2);*/

            //tea types block
            var teaLiContent = [
                '<li class="level0 nav-extra no-show-all last level-top parent ui-menu-item mobile-only" role="presentation">',
                '<a id="ui-id-12" href="#" class="level-top ui-corner-all" aria-haspopup="true" role="menuitem">',
                '<span class="ui-menu-icon ui-icon"></span><span>' + $.mage.__('Thee per type') + '</span>',
                '</a>',
                '</li>'
            ].join('');

            this.VAR.$teaTypesWrapper = $('<ul />')
                .addClass('level0 mega-menu ui-menu ui-widget ui-widget-content ui-corner-all submenu')
                .attr({
                    'id': 'tea-by-type-mobile',
                    'role': 'menu',
                    'aria-hidden': 'true',
                    'aria-expanded': 'false'
                }).hide().append(this.VAR.$teaTypes.html());

            var $teaLiContent = $(teaLiContent).append(this.VAR.$teaTypesWrapper);
            this.VAR.$nav.find('>ul').prepend($teaLiContent);
        },

        addEvents: function(_self){
            //mobile nav
            this.VAR.$mobNav.on('click', function(){
                $(this).toggleClass('active');
                //if mobile search is active close it.
                if(_self.VAR.$mobSearch.hasClass('active')){
                    _self.VAR.$mobSearch.toggleClass('active');
                    _self.VAR.$search.slideUp();
                }
                _self.VAR.$navigation.toggleClass('active').slideToggle();

            });

            //mobile search
            this.VAR.$mobSearch.on('click', function() {
                $(this).toggleClass('active');
                //if mobile nav is active close it.
                if(_self.VAR.$mobNav.hasClass('active')){
                    _self.VAR.$mobNav.trigger('click');
                    _self.VAR.$navigation.slideUp();
                }
                _self.VAR.$search.toggleClass('active').slideToggle();

            });
        },

        _toggleMobileMode: function () {
          //update the mobile menu to animate  - Nalinda
           var subMenus = this.element.find('.level-top:not(".no-show-all")');
            $.each(subMenus, $.proxy(function (index, item) {
                var category = $(item).find('> a span').not('.ui-menu-icon').text(),
                    categoryUrl = $(item).find('> a').attr('href'),
                    menu = $(item).find('> .ui-menu');

                this.categoryLink = $('<a>')
                    .attr('href', categoryUrl)
                    .text(category + $.mage.__(' All'));

                this.categoryParent = $('<li>').addClass('ui-menu-item all-category');

                if (!$(item).hasClass('no')) {
                    this.categoryParent.html(this.categoryLink);
                }


                if (menu.find('.all-category').length === 0) {
                    menu.prepend(this.categoryParent);
                }

            }, this));

            $.widget.bridge('ui_accordion', $.ui.accordion);

            $(this.element).ui_accordion(
                {
                    header: this.element.find('>.parent>a'),
                    collapsible: true,
                    active: true,
                    heightStyle: "content",
                    activate: function (event, ui) {
                        ui.oldHeader.removeClass('ui-acc-active');
                        ui.newHeader.addClass('ui-acc-active');
                    }
                }
            );

        }


    });

    return {
        menu: $.dilmah.menu,
        navigation: $.mage.navigation
    };
});
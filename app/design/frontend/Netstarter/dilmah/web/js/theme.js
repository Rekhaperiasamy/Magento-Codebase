/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/smart-keyboard-handler',
    'jquery/ui',
    'mage/mage',
    'stickySection',
    'mage/ie-class-fixer',
    'domReady!'

], function ($, keyboardHandler) {
    'use strict';

    if ($('body').hasClass('checkout-cart-index')) {
        if ($('#co-shipping-method-form .fieldset.rates').length > 0 && $('#co-shipping-method-form .fieldset.rates :checked').length === 0) {
            $('#block-shipping').on('collapsiblecreate', function () {
                $('#block-shipping').collapsible('forceActivate');
            });
        }
    }


    // bug HOT fix
    var stickyToItemDiv = '.crosssell';

    if($('.crosssell').length < 1){
         stickyToItemDiv = '.wrap-promo';
     }



    setTimeout(function(){
        //cart JS
        $('.cart-summary').mage('stickySection', {
            'extraGapTop': 0 ,'stickyFromItemExtra':0,'hideItem':'.shopping-cart-info-wrapper','stickyFromItem':'.cart.table-wrapper','stickyToItem':stickyToItemDiv
        });
    }, 1000);



    $('.panel.header > .header.links').clone().appendTo('#store\\.links');

    keyboardHandler.apply();


    $.widget.bridge('ui_accordion', $.ui.accordion);
    jQuery('.cms-menu').ui_accordion({
        animate: 400,
        collapsible: false
    });


    function addSnowFall() {

        var $body  = $('body');
        // Enable only for specific pages
        if($body.hasClass('snow-global')){
            $body.mage('xmasPage');
        }
    }

    addSnowFall();

});

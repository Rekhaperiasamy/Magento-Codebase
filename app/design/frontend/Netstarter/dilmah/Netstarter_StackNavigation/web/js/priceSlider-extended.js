/**
 * Copyright © 2015 Netstarter. All rights reserved.
 *
 * PHP version 5
 *
 * @category  JavaScript
 * @author    Netstarter M2 Stack Team <contact@netstarter.com>
 * @copyright 2007-2015 NETSTARTER PTY LTD (ABN 28 110 067 96)
 * @license   licence.txt ©
 * @link      http://netstarter.com.au
 */
/*jshint strict:true, noempty:true, noarg:true, eqeqeq:true, devel:true, jquery:true, browser:true, bitwise:true, curly:true, undef:true, nonew:true, forin:true */
/*global define */

define([
    "jquery",
    "jquery/ui",
    'Netstarter_StackNavigation/js/priceSlider'
], function ($) {
    "use strict";

    $.widget('dilmah.priceSlider',$.netstarter.priceSlider, {

        //$.widget('netstarter.priceSlider', {
        _create: function () {

            this._super();
            this._addPriceRullerSeparaters();


        },

        _addPriceRullerSeparaters: function () {
            $this = this;

            if(!$this.options.minPriceMin){
                //$this.options.minPriceMin = $this.options.minPrice - 1; // -1 is to fix slider price relate issue

                $this.options.minPriceMin = Math.floor($('[data-min-price-val]').data('minPriceVal'));

                if($this.options.minPriceMin <= -1){
                    $this.options.minPriceMin = 0;
                }
            }
            if(!$this.options.maxPriceMax){
                //$this.options.maxPriceMax = $this.options.maxPrice - 1; // -1 is to fix slider price relate issue
                $this.options.maxPriceMax = Math.ceil($('[data-max-price-val]').data('maxPriceVal')); // -1 is to fix slider price relate issue
            }


            var maxl =  $this.options.maxPriceMax ;
            var minl =  $this.options.minPriceMin ;
            

            //$('.price-ruller').parent().remove();
            $('[data-section="Price"] .filter-options-content').prepend('<div class="wrapper price-slider-split"><div class="price-ruller"></div></div>');
            var count       = 6 ,
                $this       = this, // current widget scope
                points      = '',
                priceGap    = '',
                wrap        = $( '.price-ruller' ),
                unit        = $this.options.priceUnit;

            priceGap =   (maxl - minl)/(count-1);
            


            // create points
            for ( var i = 0; i < count; i++ ) {
                var pri = Math.round(priceGap * i);
                //if(i==0)
                //{
                //    pri = minl;
                //}
                //else if (i==(count-1))
                //{
                //    pri = maxl;
                //
                //}

                if(i==0){
                    points += '<span> '+ unit + (minl+pri) +'</span>';
                } else {
                    points += '<span>'+ (minl+pri) +'</span>';
                }
            }

            // append points
            wrap.append( points );


            // set width
            $( 'span', wrap ).width( ( 100/count ) + '%' );

            wrap.after('<div class="border-price-slider"></div>');



        }

    });

    return $.dilmah.priceSlider;
});







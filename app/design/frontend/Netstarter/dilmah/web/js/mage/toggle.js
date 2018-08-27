/**
 * codepool - toggle.js
 * Created by Shabith on 3/17/2016.
 */
/*jshint browser:true jquery:true*/

/***
 * extends mage.toggleAdvance to add slide down and slide up animation
 *
 */
define([
    "jquery",
    "jquery/ui",
    "mage/toggle"
], function($){
    "use strict";

    $.widget('mage.toggleAdvanced', $.mage.toggleAdvanced, {

        options: {
            animate: false
        },

        /**
         * Method responsible for hiding and revealing specified DOM elements
         * If data-toggle-selectors attribute is present - toggle will be done on these selectors
         * Otherwise we toggle the class on clicked element
         *
         * @protected
         * @override
         */
        _toggleSelectors: function () {
            this._super();
            if (this.options.toggleContainers) {
                if($(this.options.toggleContainers).hasClass(this.options.selectorsToggleClass)){
                   if(this.options.animate){
                       $(this.options.toggleContainers).removeClass('collapsed');
                       $(this.options.toggleContainers).addClass('collapsing');
                       $(this.options.toggleContainers).slideDown( function (){
                           $(this).removeClass('collapsing');
                       });
                   }

                }else{
                    if(this.options.animate){
                        $(this.options.toggleContainers).removeClass('collapsed');
                        $(this.options.toggleContainers).addClass('collapsing');
                        $(this.options.toggleContainers).slideUp( function (){
                            $(this).removeClass('collapsing');
                            $(this).addClass('collapsed');
                        });
                    }

                }
            }

        }
    });

    return $.mage.toggleAdvanced;
});
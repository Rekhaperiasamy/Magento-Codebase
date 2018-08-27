define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, ko, quote) {
        'use strict';
        ko.bindingHandlers.myTestHandler = {
            init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                // This will be called when the binding is first applied to an element
                // Set up any initial state, event handlers, etc. here
                if(window.discountEnable == 0){
                   // $('.iwd-discount-code-wrapper').hide();
                }
            },
            update: function (element, valueAccessor, allBindings, viewModel, bindingContext) {

                // This will be called once when the binding is first applied to an element,
                // and again whenever any observables/computeds that are accessed change
                // Update the DOM element based on the supplied values here.
            }

        }
    });
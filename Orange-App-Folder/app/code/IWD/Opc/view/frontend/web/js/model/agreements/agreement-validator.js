/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'mage/validation'
    ],
    function ($) {
        'use strict';
        var agreementsConfig = window.checkoutConfig.checkoutAgreements;
        return {
            /**
             * Validate checkout agreements
             *
             * @returns {boolean}
             */
            validate: function() {
                if (!agreementsConfig.isEnabled) {
                    return true;
                }

                var form = $('form#iwd-checkout-agreements-form');
                form.validation();
                return form.validation('isValid');
            }
        }
    }
);

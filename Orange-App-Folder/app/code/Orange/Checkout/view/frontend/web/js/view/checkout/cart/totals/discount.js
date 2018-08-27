/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'Orange_Checkout/js/view/checkout/summary/discount'
    ],
    function (Component) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Orange_Checkout/checkout/cart/totals/discount'
            },
            /**
             * @override
             *
             * @returns {boolean}
             */
            isDisplayed: function () {
                return this.getPureValue() !== 0;
            }
        });
    }
);

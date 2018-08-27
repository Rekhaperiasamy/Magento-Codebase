/*browser:true*/
/*global define*/

define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/shipping'
    ],
    function ($, Component) {
        'use strict';

        return Component.extend({
            isCalculated: function () {
                return this.totals() && this.isFullMode();
            }
        });
    }
);

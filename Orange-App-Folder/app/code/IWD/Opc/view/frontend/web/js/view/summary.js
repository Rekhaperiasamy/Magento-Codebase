/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'uiComponent',
        'IWD_Opc/js/model/totals'
    ],
    function(Component, totals) {
        'use strict';
        return Component.extend({
            isLoading: totals.isLoading
        });
    }
);

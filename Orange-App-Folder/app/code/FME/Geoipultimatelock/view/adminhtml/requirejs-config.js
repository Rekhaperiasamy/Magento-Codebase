/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
var config = {
    map: {
        '*': {
            gaccordian: 'FME_Geoipultimatelock/js/jquery.accordion.source'
            
        }
    },
    shim: {
        "gaccordian": {
            deps: ["jquery"]
        }
    }
};
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            productDetails:       'Magento_Catalog/js/product-details',
            stickup:       'Magento_Catalog/js/plugins/stickUp.min',
            toolBarCustom:       'Magento_Catalog/js/tool-bar-custom',
            productSticky:        'Magento_Catalog/js/detail-sticky',
            stickymenu:       'js/sticky-menu',
            bundleCustom:       'Magento_Catalog/js/bundle-custom',
            layeredCustom:       'Magento_Catalog/js/layered-custom',
            catalogAddToCart:      'Magento_Catalog/js/catalog-add-to-cart',
            snowfall:   'Magento_Catalog/js/snowfall.jquery.min',
            xmasPage: 'Magento_Catalog/js/xmasPage'

            //productSliderRelated: "Magento_Catalog/js/product-slider",
            //slick: "Magento_Catalog/js/plugins/slickjs/slick.min"
        }
    },
    "shim": {
        "stickup": ["jquery"],
        "snowfall": ["jquery"]
    }
};




//
//var config = {
//    map: {
//        "*": {
//            afeature: "Dilmah_Afeature/js/afeature",
//            slick: "Dilmah_Afeature/js/plugins/slickjs/slick.min"
//        }
//    },
//    "shim": {
//        "slick": ["jquery"]
//    }
//};
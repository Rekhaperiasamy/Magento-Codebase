/**
 * Dilmah - requirejs-config.js
 * Created by Shabith on 1/29/16.
 */

var config = {
    map: {
        "*": {
            cms:'Magento_Cms/js/cms',
            stickupNav:'Magento_Cms/js/plugins/stickUp.min',



        }
    },
    "shim": {
        "stickupNav": ["jquery"]
    }
};

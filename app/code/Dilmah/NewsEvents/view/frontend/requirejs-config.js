/**
 * codepool - requirejs-config.js
 * Created by Shabith on 4/25/2016.
 */

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            newsEvents: 'Dilmah_NewsEvents/js/news-events',
            gray: "Dilmah_NewsEvents/js/plugins/gray/jquery.gray.min"
        }
    },
    "shim": {
        "gray": ["jquery"]
    }
};
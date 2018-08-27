/**
 * Dilmah - requirejs-config.js
 * Created by Shabith on 1/29/16.
 */

var config = {
    map: {
        "*": {
            header: "js/header",
            footer: "js/footer",
            menu: "js/menu",
            faqExtended: "js/faq-extended",
            mobileMenu  : "js/mobile-menu", //extend the default menu
            slick: "js/plugins/slickjs/slick.min",
            productDetailSlider: "js/product-slider",
            readMore: "js/read-more",
            customDropdown: 'js/custom-dropdown',
            stickySection: 'js/sticky-sections',
            nsdropdown: "js/plugins/nslibrary/nsdropdown",
            "mage/gallery/gallery": "js/mage/gallery/gallery",
            "theme": "js/theme",
            cartCount: "js/cartcount", //product Slider for all the pages
            toggleAdvanced: "js/mage/toggle"//,
        }
    }  ,
    "shim": {
        "slick": ["jquery"],
        "nsdropdown": ["jquery"]
    }
};

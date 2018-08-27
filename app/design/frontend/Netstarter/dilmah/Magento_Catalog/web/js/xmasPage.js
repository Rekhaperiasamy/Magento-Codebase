define([
    "jquery",
    "jquery/ui",
    "snowfall"
], function ($) {
    "use strict";

    $.widget('dilmah.xmasPage', {
        options: {},
        VAR: {
            $xMasProdList: undefined,
            $loadMore: undefined
        },



        _create: function () {

            var _self = this;
            this._initialization(_self);
            this._bindEvents(_self);

            //Start the snow default options you can also make it snow in certain elements, etc.
            $(document).snow({minSize: 5, maxSize:20});




            _self.VAR.$xMasProdList.after(_self.VAR.$loadMore);

        },

        _initialization: function (_self) {
            _self.VAR.$xMasProdList = jQuery('.category-x-mas-page .products-grid .product-items');
            _self.VAR.$loadMore = jQuery('<a class="load-more-products" href="#">View All Products</a>');
        },

        _bindEvents: function (_self) {
            _self.VAR.$loadMore.on('click', function (e) {
                e.preventDefault();
                _self.VAR.$xMasProdList.addClass('show-all');
            });
        }



    });

    return $.mage.toolBarCustom;
});

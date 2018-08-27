/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */


var CodeMirrorProductPattern = null;
var Configuration = {};

require(["jquery","Magento_Ui/js/modal/confirm"], function ($,confirm) {

    Configuration = {
        CodeMirrorTxt: null,
        generate: function () {
            confirm({
                title: "Generate data feed",
                content: "Generate a data feed can take a while. Are you sure you want to generate it now ?",
                actions: {
                    confirm: function () {
                        $('#generate_i').val('1');
                        $('#edit_form').submit();
                    }
                }
            });
        },
        delete: function () {
            confirm({
                title: "Delete data feed",
                content: "Are you sure you want to delete this feed ?",
                actions: {
                    confirm: function () {
                        $('#back_i').val('1');
                        $('#edit_form').submit();
                    }
                }
            });
        }

    };
});


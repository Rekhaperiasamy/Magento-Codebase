/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
var Index = null;


require(["jquery", "Magento_Ui/js/modal/confirm", "jquery/ui", "Magento_Ui/js/modal/modal"], function ($, confirm) {
    $(function () {
        
        Index = {
            feeds: {
                generate: function (url) {
                    confirm({
                        title: "Generate data feed",
                        content: "Generate a data feed can take a while. Are you sure you want to generate it now ?",
                        actions: {
                            confirm: function () {
                                document.location.href = url;
                            },
                            cancel: function () {
                                $('.col-action select.admin__control-select').val("");
                            }
                        }
                    });
                },
                delete: function (url) {
                    confirm({
                        title: "Delete data feed",
                        content: "Are you sure you want to delete this feed ?",
                        actions: {
                            confirm: function () {
                                document.location.href = url;
                            },
                            cancel: function () {
                                $('.col-action select.admin__control-select').val("");
                            }
                        }
                    });
                }
            },
            updater: {
                init: function () {
                    data = new Array();
                    $('.updater').each(function () {
                        var feed = [$(this).attr('id').replace("feed_", ""), $(this).attr('cron')];
                        data.push(feed);
                    });

                    $.ajax({
                        url: Index.updater.url,
                        data: {
                            data: JSON.stringify(data)
                        },
                        type: 'GET',
                        showLoader: false,
                        success: function (data) {
                            data.each(function (r) {
                                $("#feed_" + r.id).parent().html(r.content)
                            });
                            setTimeout(Index.updater.init, 1000)
                        }
                    });

                }
            }
        };

        Index.updater.init();
    });
});
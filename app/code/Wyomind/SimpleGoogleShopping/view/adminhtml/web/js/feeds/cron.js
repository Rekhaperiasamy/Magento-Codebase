var Cron = {};

require(["jquery"], function ($) {

    Cron = {
        /**
         * Load the selected days and hours
         */
        loadExpr: function () {
            if ($('#cron_expr').val() === "") {
                $('#cron_expr').val("{}");
            }
            var val = $.parseJSON($('#cron_expr').val());
            if (val !== null) {
                if (val.days)
                    val.days.each(function (elt) {
                        $('#d-' + elt).parent().addClass('selected');
                        $('#d-' + elt).prop('checked', true);
                    });
                if (val.hours)
                    val.hours.each(function (elt) {
                        var hour = elt.replace(':', '');
                        $('#h-' + hour).parent().addClass('selected');
                        $('#h-' + hour).prop('checked', true);
                    });
            }
        },
        /**
         * Update the json representation of the cron schedule
         */
        updateExpr: function () {
            var days = new Array();
            var hours = new Array();
            $('.cron-box.day').each(function () {
                if ($(this).prop('checked') === true) {
                    days.push($(this).attr('value'));
                }
            });
            $('.cron-box.hour').each(function () {
                if ($(this).prop('checked') === true) {
                    hours.push($(this).attr('value'));
                }
            });

            $('#cron_expr').val(JSON.stringify({days: days, hours: hours}));
        }
    };
});


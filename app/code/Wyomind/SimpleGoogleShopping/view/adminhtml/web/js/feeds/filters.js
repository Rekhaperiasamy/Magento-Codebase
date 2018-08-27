var Filters = {};


require(["jquery"], function ($) {

    Filters = {
        /**
         * Load the selected product types
         * @returns {undefined}
         */
        loadProductTypes: function () {
            var values = $('#simplegoogleshopping_type_ids').val();
            if ($('#simplegoogleshopping_type_ids').val() === "") {
                $('#simplegoogleshopping_type_ids').val('*');
                values = '*';
            }
            if (values !== '*') {
                values = values.split(',');
                values.each(function (v) {
                    $('#type_id_' + v).prop('checked', true);
                    $('#type_id_' + v).parent().addClass('selected');
                });
            } else {
                $('#type-ids-selector').find('input').each(function () {
                    $(this).prop('checked', true);
                    $(this).parent().addClass('selected');
                });
            }
        },
        /**
         * Check if all product types are selected
         * @returns {Boolean}
         */
        isAllProductTypesSelected: function () {
            var all = true;
            $(document).find('.filter_product_type').each(function () {
                if ($(this).prop('checked') === false)
                    all = false;
            });
            return all;
        },
        /**
         * Update product types selection
         * @returns {undefined}
         */
        updateProductTypes: function () {
            var values = new Array();
            $('.filter_product_type').each(function (i) {
                if ($(this).prop('checked')) {
                    values.push($(this).attr('identifier'));
                }
            });
            $('#simplegoogleshopping_type_ids').val(values.join());
            this.updateUnSelectLinksProductTypes();
        },
        /**
         * Load the selected atribute set
         * @returns {undefined}
         */
        loadAttributeSets: function () {
            var values = $('#simplegoogleshopping_attribute_sets').val();
            if ($('#simplegoogleshopping_attribute_sets').val() === "") {
                $('#simplegoogleshopping_attribute_sets').val('*');
                values = '*';
            }
            if (values !== '*') {
                values = values.split(',');
                values.each(function (v) {
                    $('#attribute_set_' + v).prop('checked', true);
                    $('#attribute_set_' + v).parent().addClass('selected');
                });
            } else {
                $('#attribute-sets-selector').find('input').each(function () {
                    $(this).prop('checked', true);
                    $(this).parent().addClass('selected');
                });
            }
        },
        /**
         * Update attribute sets selection
         * @returns {undefined}
         */
        updateAttributeSets: function () {
            var values = new Array();
            var all = true;
            $('.filter_attribute_set').each(function (i) {
                if ($(this).prop('checked')) {
                    values.push($(this).attr('identifier'));
                } else {
                    all = false;
                }
            });
            if (all) {
                $('#simplegoogleshopping_attribute_sets').val('*');
            } else {
                $('#simplegoogleshopping_attribute_sets').val(values.join());
            }
            this.updateUnSelectLinksAttributeSets();
        },
        /**
         * Check if all attribute sets are selected
         * @returns {Boolean}
         */
        isAllAttributeSetsSelected: function () {
            var all = true;
            $(document).find('.filter_attribute_set').each(function () {
                if ($(this).prop('checked') === false)
                    all = false;
            });
            return all;
        },
        /**
         * Load the selected product visibilities
         * @returns {undefined}
         */
        loadProductVisibilities: function () {
            var values = $('#simplegoogleshopping_visibility').val();
            if ($('#simplegoogleshopping_visibility').val() === '') {
                $('#simplegoogleshopping_visibility').val('*');
                values = '*';
            }
            if (values !== '*') {
                values = values.split(',');
                values.each(function (v) {
                    $('#visibility_' + v).prop('checked', true);
                    $('#visibility_' + v).parent().addClass('selected');
                });
            } else {
                $('#visibility-selector').find('input').each(function () {
                    $(this).prop('checked', true);
                    $(this).parent().addClass('selected');
                });
            }
        },
        /**
         * Update visibilities selection
         * @returns {undefined}
         */
        updateProductVisibilities: function () {
            var values = new Array();
            $('.filter_visibility').each(function (i) {
                if ($(this).prop('checked')) {
                    values.push($(this).attr('identifier'));
                }
            });
            $('#simplegoogleshopping_visibility').val(values.join());
            this.updateUnSelectLinksProductVisibilities();
        },
        /**
         * Check if all product visibilities are selected
         * @returns {Boolean}
         */
        isAllProductVisibilitiesSelected: function () {
            var all = true;
            $(document).find('.filter_visibility').each(function () {
                if ($(this).prop('checked') === false)
                    all = false;
            });
            return all;
        },
        /**
         * Check if we need to display 'Select All' or 'Unselect All' for each kind of filters
         * @returns {undefined}
         */
        updateUnSelectLinks: function () {
            this.updateUnSelectLinksProductTypes();
            this.updateUnSelectLinksAttributeSets();
            this.updateUnSelectLinksProductVisibilities();
        },
        /**
         * Check if we need to display 'Select All' or 'Unselect All' for product types
         * @returns {undefined}
         */
        updateUnSelectLinksProductTypes: function () {
            if (this.isAllProductTypesSelected()) {
                $('#type-ids-selector').find('.select-all').removeClass('visible');
                $('#type-ids-selector').find('.unselect-all').addClass('visible');
            } else {
                $('#type-ids-selector').find('.select-all').addClass('visible');
                $('#type-ids-selector').find('.unselect-all').removeClass('visible');
            }
        },
        /**
         * Check if we need to display 'Select All' or 'Unselect All' for attributes sets
         * @returns {undefined}
         */
        updateUnSelectLinksAttributeSets: function () {
            if (this.isAllAttributeSetsSelected()) {
                $('#attribute-sets-selector').find('.select-all').removeClass('visible');
                $('#attribute-sets-selector').find('.unselect-all').addClass('visible');
            } else {
                $('#attribute-sets-selector').find('.select-all').addClass('visible');
                $('#attribute-sets-selector').find('.unselect-all').removeClass('visible');
            }
        },
        /**
         * Check if we need to display 'Select All' or 'Unselect All' for product visibilities
         * @returns {undefined}
         */
        updateUnSelectLinksProductVisibilities: function () {
            if (this.isAllProductVisibilitiesSelected()) {
                $('#visibility-selector').find('.select-all').removeClass('visible');
                $('#visibility-selector').find('.unselect-all').addClass('visible');
            } else {
                $('#visibility-selector').find('.select-all').addClass('visible');
                $('#visibility-selector').find('.unselect-all').removeClass('visible');
            }
        },
        /**
         * Load the selected advanced filters
         * @returns {undefined}
         */
        loadAdvancedFilters: function () {
            var filters = $.parseJSON($('#simplegoogleshopping_attributes').val());
            if (filters === null) {
                filters = new Array();
                $('#simplegoogleshopping_attributes').val(JSON.stringify(filters));
            }
            var counter = 0;
            while (filters[counter]) {
                filter = filters[counter];
                $('#attribute_' + counter).prop('checked', filter.checked);

                $('#name_attribute_' + counter).val(filter.code);
                $('#value_attribute_' + counter).val(filter.value);
                $('#condition_attribute_' + counter).val(filter.condition);
                if (filter.statement) {
                    $('#statement_attribute_' + counter).val(filter.statement);
                }

                this.updateRow(counter, filter.code);

                $('#name_attribute_' + counter).prop('disabled', !filter.checked);
                $('#condition_attribute_' + counter).prop('disabled', !filter.checked);
                $('#value_attribute_' + counter).prop('disabled', !filter.checked);
                $('#pre_value_attribute_' + counter).prop('disabled', !filter.checked);
                $('#statement_attribute_' + counter).prop('disabled', !filter.checked);


                $('#pre_value_attribute_' + counter).val(filter.value);

                counter++;
            }
        },
        /**
         * Update the advanced filters json string
         * @returns {undefined}
         */
        updateAdvancedFilters: function () {
            var newval = {};
            var counter = 0;
            $('.advanced_filters').each(function () {
                var checkbox = $(this).find('#attribute_' + counter).prop('checked');
                // is the row activated
                if (checkbox) {
                    $('#name_attribute_' + counter).prop('disabled', false);
                    $('#condition_attribute_' + counter).prop('disabled', false);
                    $('#value_attribute_' + counter).prop('disabled', false);
                    $('#pre_value_attribute_' + counter).prop('disabled', false);
                    $('#statement_attribute_' + counter).prop('disabled', false);
                } else {
                    $('#name_attribute_' + counter).prop('disabled', true);
                    $('#condition_attribute_' + counter).prop('disabled', true);
                    $('#value_attribute_' + counter).prop('disabled', true);
                    $('#pre_value_attribute_' + counter).prop('disabled', true);
                    $('#statement_attribute_' + counter).prop('disabled', true);
                }
                var statement = $(this).find('#statement_attribute_' + counter).val();
                var name = $(this).find('#name_attribute_' + counter).val();
                var condition = $(this).find('#condition_attribute_' + counter).val();
                var pre_value = $(this).find('#pre_value_attribute_' + counter).val();
                var value = $(this).find('#value_attribute_' + counter).val();
                if (attribute_codes[name] && attribute_codes[name].length > 0) {
                    value = pre_value;
                }
                var val = {checked: checkbox, code: name, statement: statement, condition: condition, value: value};
                newval[counter] = val;
                counter++;
            });
            $('#simplegoogleshopping_attributes').val(JSON.stringify(newval));
        },
        /**
         * Update an advanced filter row (display custom value or not, display multi select, ...)
         * @param {type} id
         * @param {type} attribute_code
         * @returns {undefined}
         */
        updateRow: function (id, attribute_code) {
            if (attribute_codes[attribute_code] && attribute_codes[attribute_code].length > 0) {

                // enable multi select or dropdown
                $('#pre_value_attribute_' + id).prop('disabled', false);

                // full the multi select / dropdown
                $('#pre_value_attribute_' + id).html("");
                attribute_codes[attribute_code].each(function (elt) {

                    $('#pre_value_attribute_' + id).append($('<option>', {
                        value: elt.value,
                        text: elt.label
                    }));
                });
                $('#pre_value_attribute_' + id).val(attribute_codes[attribute_code][0].value);


                // if "in/not in", then multiselect
                if ($('#condition_attribute_' + id).val() === "in" || $('#condition_attribute_' + id).val() === "nin") {
                    $('#pre_value_attribute_' + id).attr('size', '5');
                    $('#pre_value_attribute_' + id).prop('multiple', true);
                    $('#name_attribute_' + id).parent().parent().parent().parent().addClass('multiple-value').removeClass('one-value').removeClass('dddw');
                    $('#value_attribute_' + id).css('display', 'none');

                } else if ($('#condition_attribute_' + id).val() === "null" || $('#condition_attribute_' + id).val() === "notnull") {
                    $('#name_attribute_' + id).parent().parent().parent().parent().removeClass('multiple-value').addClass('one-value').removeClass('dddw');
                    $('#value_attribute_' + id).css('display', 'none');

                } else { // else, dropdown
                    $('#pre_value_attribute_' + id).prop('size', '1');
                    $('#pre_value_attribute_' + id).prop('multiple', false);
                    $('#name_attribute_' + id).parent().parent().parent().parent().removeClass('multiple-value').addClass('one-value').addClass('dddw');
                    $('#value_attribute_' + id).css('display', 'none');
                }



            } else {
                $('#name_attribute_' + id).parent().parent().parent().parent().removeClass('multiple-value').addClass('one-value').removeClass('dddw');
                $('#pre_value_attribute_' + id).prop('disabled', true);
                if ($('#condition_attribute_' + id).val() === "null" || $('#condition_attribute_' + id).val() === "notnull") {
                    $('#value_attribute_' + id).css('display', 'none');
                } else {
                    $('#value_attribute_' + id).css('display', 'inline');
                }
            }
        },
        /**
         * Click on select all link
         * @param {type} elt
         * @returns {undefined}
         */
        selectAll: function (elt) {
            var fieldset = elt.parents('.fieldset')[0];
            $(fieldset).find('input[type=checkbox]').each(function () {
                $(this).prop('checked', true);
                $(this).parent().addClass('selected');
            });
            this.updateProductTypes();
            this.updateProductVisibilities();
            this.updateAttributeSets();
            elt.removeClass('visible');
            $(fieldset).find('.unselect-all').addClass('visible');
        },
        /**
         * Click on unselect all link
         * @param {type} elt
         * @returns {undefined}
         */
        unselectAll: function (elt) {
            var fieldset = elt.parents('.fieldset')[0];
            $(fieldset).find('input[type=checkbox]').each(function () {
                $(this).prop('checked', false);
                $(this).parent().removeClass('selected');
            });
            this.updateProductTypes();
            this.updateProductVisibilities();
            this.updateAttributeSets();
            elt.removeClass('visible');
            $(fieldset).find('.select-all').addClass('visible');
        }
    };
});
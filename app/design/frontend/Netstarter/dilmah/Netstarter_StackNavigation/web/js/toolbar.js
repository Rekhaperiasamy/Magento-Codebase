/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    "jquery",
    "jquery/ui"

], function($) {
    /**
     * ProductListToolbarForm Widget - this widget is setting cookie and submitting form according to toolbar controls
     */
    $.widget('mage.productListToolbarForm', {

        options: {
            modeControl: '[data-role="mode-switcher"]',
            directionControl: '[data-role="direction-switcher"]',
            orderControl: '[data-role="sorter"]',
            limitControl: '[data-role="limiter"]',
            mode: 'product_list_mode',
            direction: 'product_list_dir',
            order: 'product_list_order',
            new: 'is_new',
            limit: 'product_list_limit',
            modeDefault: 'grid',
            directionDefault: 'asc',
            orderDefault: 'position',
            limitDefault: '9',
            url: ''
        },

        _create: function () {
            this._bind($(this.options.modeControl), this.options.mode, this.options.modeDefault);
            this._bind($(this.options.orderControl).find(':selected').eq(0), this.options.direction, this.options.directionDefault);
            //this._bind($(this.options.directionControl), this.options.direction, this.options.directionDefault);
            this._bind($(this.options.orderControl), this.options.order, this.options.orderDefault);
            this._bind($(this.options.limitControl), this.options.limit, this.options.limitDefault);
        },

        _bind: function (element, paramName, defaultValue) {
            if (element.is("select")) {
                element.on('change', {paramName: paramName, default: defaultValue}, $.proxy(this._processSelect, this));
            } else {
                element.on('click', {paramName: paramName, default: defaultValue}, $.proxy(this._processLink, this));
            }
        },

        _processLink: function (event) {
            event.preventDefault();
            this.changeUrl(
                event.data.paramName,
                $(event.currentTarget).data('value'),
                event.data.default
            );
        },

        _processSelect: function (event) {
            this.changeUrl(
                event.data.paramName,
                event.currentTarget.options[event.currentTarget.selectedIndex].value,
                event.data.default
            );
        },

        changeUrl: function (paramName, paramValue, defaultValue) {
            var urlPaths = this.options.url.split('?'),
                baseUrl = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                paramData = {},
                parameters;
            for (var i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                paramData[parameters[0]] = parameters[1] !== undefined
                    ? window.decodeURIComponent(parameters[1].replace(/\+/g, '%20'))
                    : '';
            }
            paramData[paramName] = paramValue;
            // sort direction inclusion to url
            if (paramName == this.options.order) {
                if (direction = $(this.options.orderControl).find(':selected').eq(0)) {
                    if (direction.data()['role'] == 'direction-switcher') {
                        paramData[this.options.direction] = direction.data()['value'] ? direction.data()['value'] : '';
                    }
                }
            }
            if (paramValue == this.options.new) {
                if (direction = $(this.options.orderControl).find(':selected').eq(0)) {
                    if (direction.data()['role'] == 'direction-switcher') {
                        paramData[this.options.direction] = 'desc';
                    }
                }
            }
            /*if (paramValue == defaultValue) {
                delete paramData[paramName];
            }*/
            paramData = $.param(paramData);
            console.log( baseUrl + (paramData.length ? '?' + paramData : '') );

            var url = baseUrl + (paramData.length ? '?' + paramData : '');
            $('body').trigger('init-callAjax', [ url ] );

            //console.log( baseUrl + (paramData.length ? '?' + paramData : '') );
            //location.href = baseUrl + (paramData.length ? '?' + paramData : '');
        }
    });

    return $.mage.productListToolbarForm;
});

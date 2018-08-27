    define([
        'underscore',
        'uiRegistry',
        'Magento_Ui/js/form/element/select',
        'Magento_Ui/js/modal/modal'
    ], function (_, uiRegistry, select, modal) {
        'use strict';

        return select.extend({

            /**
             * Init
             */
            initialize: function () {
                this._super();

                this.fieldDepend(this.value());

                return this;
            },

            /**
             * On value change handler.
             *
             * @param {String} value
             */
            onUpdate: function (value) {
                this.fieldDepend(value);

                return this._super();
            },

            /**
             * Update field dependency
             *
             * @param {String} value
             */
            fieldDepend: function (value) {
                var field = uiRegistry.get('index = shipping_amount');

                if (value == 'hide') {
                    field.hide();
                } else {
                    field.show();
                }

                return this;
            }
        });
    });
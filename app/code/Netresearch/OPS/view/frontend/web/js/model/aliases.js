define([
    'ko',
    'uiClass'
], function (ko, Class) {
    'use strict';

    return Class.extend({


        initialize: function () {
            this._super()
                .initObservable();

            return this;
        },

        initObservable: function () {
            this.aliases = ko.observableArray([]);
            return this;
        },

        addAlias: function (alias) {
            this.aliases.push(alias);
        },

        addCcAlias: function (alias) {
            this.addAlias(alias);
        },


        addDcAlias: function (alias) {
            this.addAlias(alias);
        },


        getCcAliases: function () {
            return this.getAliases('ops_cc');
        },


        getDcAliases: function () {
            return this.getAliases('ops_dc');
        },

        getAliases: function (code) {
            return this.aliases.filter(function (alias) {
                return alias.paymentMethod == code
            });
        },

        clear: function () {
            this.aliases.removeAll();
        }
    });
});

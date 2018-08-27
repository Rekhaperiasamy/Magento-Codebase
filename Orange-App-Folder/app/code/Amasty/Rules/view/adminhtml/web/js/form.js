define([
    'jquery',
    'uiRegistry'
], function ($, registry) {

    var fieldset_prefix = 'sales_rule_form.sales_rule_form.actions.';

    var amrulesForm = {
        update: function () {

            this.resetFields();

            var actionFieldset = $('#sales_rule_formrule_actions_fieldset_').parent();

            window.amRulesHide = 0;

            actionFieldset.show();
            if (typeof window.amPromoHide !="undefined" && window.amPromoHide == 1) {
                actionFieldset.hide();
            }

            var action = $('[data-index="simple_action"] select').val();

            switch (action) {
                case 'thecheapest':
                case 'themostexpencive':
                case 'moneyamount':
                case 'aftern_fixed':
                case 'aftern_disc':
                case 'aftern_fixdisc':
                case 'eachn_perc':
                case 'eachn_fixdisc':
                case 'eachn_fixprice':
                case 'groupn':
                case 'groupn_disc':
                    this.showFields(['amrulesrule[skip_rule]', 'amrulesrule[priceselector]', 'amrulesrule[max_discount]']);
                    break;
                case 'eachmaftn_perc':
                case 'eachmaftn_fixdisc':
                case 'eachmaftn_fixprice':
                    this.showFields(['amrulesrule[eachm]', 'amrulesrule[skip_rule]', 'amrulesrule[priceselector]', 'amrulesrule[max_discount]']);
                    break;
                case 'buyxgety_perc':
                case 'buyxgety_fixprice':
                case 'buyxgety_fixdisc':
                    this.showFields(['amrulesrule[promo_skus]', 'amrulesrule[promo_cats]', 'amrulesrule[skip_rule]', 'amrulesrule[priceselector]', 'amrulesrule[max_discount]']);
                    break;
                case 'buyxgetn_perc':
                case 'buyxgetn_fixprice':
                case 'buyxgetn_fixdisc':
                    this.showFields(['amrulesrule[promo_skus]', 'amrulesrule[nqty]', 'amrulesrule[promo_cats]', 'amrulesrule[skip_rule]', 'amrulesrule[priceselector]', 'amrulesrule[max_discount]']);
                    break;
                case 'setof_percent':
                case 'setof_fixed':
                    actionFieldset.hide();
                    window.amRulesHide = 1;
                    this.showFields(['amrulesrule[promo_skus]', 'amrulesrule[promo_skus]', 'amrulesrule[promo_cats]', 'amrulesrule[max_discount]']);

                    //this.hideFields(['discount_step']);
                    break;
            }



        },

        resetFields: function () {
            this.showFields([
                'discount_qty', 'discount_step', 'apply_to_shipping', 'simple_free_shipping'
            ]);
            this.hideFields([
                'amrulesrule[skip_rule]',
                'amrulesrule[max_discount]',
                'amrulesrule[nqty]',
                'amrulesrule[promo_skus]',
                'amrulesrule[promo_cats]',
                'amrulesrule[priceselector]',
                'amrulesrule[eachm]'
            ]);
        },

        hideFields: function (names) {
            return this.toggleFields('hide', names);
        },

        showFields: function (names) {
            return this.toggleFields('show', names);
        },

        addPrefix: function (names) {
            for (var i = 0; i < names.length; i++) {
                names[i] = fieldset_prefix + names[i];
            }

            return names;
        },

        toggleFields: function (method, names) {
            registry.get(this.addPrefix(names), function () {
                for (var i = 0; i < arguments.length; i++) {
                    arguments[i][method]();
                }
            });
        }

    };

    return amrulesForm;
});
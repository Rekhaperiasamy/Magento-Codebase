define(['uiComponent', 'Netresearch_OPS/js/model/deviceFingerprinting'], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Netresearch_OPS/deviceFingerprinting'
        },

        initialize: function () {
            this._super();
            var consentUrl = window.checkoutConfig.payment.ops.consentUrl;
            window.consentHandler = new ConsentHandler(consentUrl);
            return this;
        }
    });
});

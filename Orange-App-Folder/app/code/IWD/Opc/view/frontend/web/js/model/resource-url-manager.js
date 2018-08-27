define(
    [
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/url-builder',
        'mageUtils'
    ],
    function(customer, urlBuilder, utils) {
        "use strict";
        return {
            getAdditionalInformationUrl: function(quote) {
                var params = (this.getCheckoutMethod() == 'guest') ? {cartId: quote.getQuoteId()} : {};
                var urls = {
                    'guest': '/guest-carts/:cartId/additional-information',
                    'customer': '/carts/mine/additional-information'
                };
                return this.getUrl(urls, params);
            },

            /** Get url for service */
            getUrl: function(urls, urlParams) {
                var url;

                if (utils.isEmpty(urls)) {
                    return 'Provided service call does not exist.';
                }

                if (!utils.isEmpty(urls['default'])) {
                    url = urls['default'];
                } else {
                    url = urls[this.getCheckoutMethod()];
                }
                return urlBuilder.createUrl(url, urlParams);
            },

            getCheckoutMethod: function() {
                return customer.isLoggedIn() ? 'customer' : 'guest';
            }
        };
    }
);

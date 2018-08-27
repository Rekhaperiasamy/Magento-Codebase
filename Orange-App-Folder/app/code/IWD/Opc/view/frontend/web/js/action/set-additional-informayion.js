define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'IWD_Opc/js/model/resource-url-manager',
        'Magento_Checkout/js/model/error-processor',
        'Magento_SalesRule/js/model/payment/discount-messages',
        'mage/storage',
        'Magento_Checkout/js/action/get-totals',
        'mage/translate',
    ],
    function (
        ko,
        $,
        quote,
        urlManager,
        errorProcessor,
        messageContainer,
        storage,
        getTotalsAction,
        $t
    ) {
        'use strict';
        return function (data, isLoading) {
            var url = urlManager.getAdditionalInformationUrl(quote);
            var message = $t('Your information was saved.');
            return storage.post(
                url, JSON.stringify(data)
            ).done(
                function (response) {
                    if (response) {
                        var deferred = $.Deferred();
                        isLoading(false);
                        getTotalsAction([], deferred);
                        messageContainer.addSuccessMessage({'message': message});
                    }
                }
            ).fail(
                function (response) {
                    isLoading(false);
                    errorProcessor.process(response, messageContainer);
                }
            );
        };
    }
);

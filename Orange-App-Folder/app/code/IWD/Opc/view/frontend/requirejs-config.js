var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'IWD_Opc/js/model/agreements/place-order-mixin': true,
                'IWD_Opc/js/model/place-order-with-comments-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'IWD_Opc/js/model/payment/place-order-mixin': true
            }
        }
    },
    map: {
        "*": {
            "Magento_Checkout/js/model/shipping-save-processor/default": "IWD_Opc/js/model/shipping-save-processor/default",
			"Magento_Checkout/js/view/payment/list": "IWD_Opc/js/view/payment/list",
			"Magento_Checkout/template/payment-methods/list.html": "IWD_Opc/template/payment-methods/list.html",
			"Magento_Checkout/js/view/form/element/email": "Magenest_AbandonedCartReminder/js/view/form/element/email",
			"Magento_Checkout/js/view/payment/default": "IWD_Opc/js/view/payment/default",
			"mage/validation": "IWD_Opc/js/model/validation",
			"Magento_Checkout/js/view/cart/totals":"IWD_Opc/js/view/cart/totals",
			"Magento_Customer/js/customer-data":"IWD_Opc/js/customer-data",
			"Magento_Checkout/template/cart/totals.html":"IWD_Opc/template/cart/totals.html",
        }
    }
};

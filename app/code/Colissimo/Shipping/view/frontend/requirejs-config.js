var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/billing-address': {
                'Colissimo_Shipping/js/view/billing-address': true
            },
            'Magento_Checkout/js/view/shipping-information': {
                'Colissimo_Shipping/js/view/shipping-information': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Colissimo_Shipping/js/view/shipping': true
            }
        }
    }
};
<?php

namespace IWD\Opc\Model\Payments\Braintree;

use \Braintree\PaymentMethod as brainTreeConfig;

class PaymentMethod extends brainTreeConfig{

    protected function getChannel()
    {
        return 'Magento-IWD';
    }
}
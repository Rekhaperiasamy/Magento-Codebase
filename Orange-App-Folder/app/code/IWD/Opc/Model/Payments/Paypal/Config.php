<?php

namespace IWD\Opc\Model\Payments\Paypal;

use \Magento\Paypal\Model\Config as paypalConfig;

class Config extends paypalConfig{

    public function getBuildNotationCode()
    {
        return 'IWD_SI_MagentoCE_WPS';

    }
}
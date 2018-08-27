<?php

namespace IWD\Opc\Block\Js;

use Magento\Customer\Model\Url;
use Magento\Framework\View\Element\Template;

class Components extends \Magento\Framework\View\Element\Template
{

    const DISCOUNTS_ENABLE = 'iwd_opc/general/opc_discount_enable';

    public function getDiscountsEnable(){
        return $this->_scopeConfig->getValue(self::DISCOUNTS_ENABLE);
    }
}
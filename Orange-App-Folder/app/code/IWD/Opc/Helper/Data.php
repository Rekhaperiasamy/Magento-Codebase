<?php

namespace IWD\Opc\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const ENABLE_OPC = 'iwd_opc/general/enable_in_frontend';
    const META_TITLE = 'iwd_opc/general/opc_title';



    public function getEnable(){
        return (bool)$this->scopeConfig->getValue(self::ENABLE_OPC);
    }

    public function getMetaTitle(){
        return $this->scopeConfig->getValue(self::META_TITLE);
    }

}
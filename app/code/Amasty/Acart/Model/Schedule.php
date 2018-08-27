<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Model;

class Schedule extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init('Amasty\Acart\Model\ResourceModel\Schedule');
    }

    public function getConfig()
    {
        $config = $this->getData();

        unset($config['rule_id']);

        $config['discount_amount'] = $config['discount_amount'] * 1;
        $config['discount_qty'] = $config['discount_qty'] * 1;
        
        return $config;
    }

    public function getDeliveryTime()
    {
        return ($this->getDays() * 24 * 60 * 60) +
            ($this->getHours() * 60 * 60) +
            ($this->getMinutes() * 60);
    }
}
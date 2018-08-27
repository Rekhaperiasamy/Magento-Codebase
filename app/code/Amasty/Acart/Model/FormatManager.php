<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Model;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class FormatManager extends \Magento\Framework\DataObject
{
    const TYPE_HISTORY = 'history';
    const TYPE_QUOTE = 'quote';
    const TYPE_RULE_QUOTE = 'rule_quote';


    protected $_config;
    protected $_dateTime;
    protected $_priceCurrency;

    public function init($config)
    {
        $this->_config = $config;
        return $this;
    }

    public function __construct(

        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        PriceCurrencyInterface $priceCurrency,

        array $data = []
    ) {
        $this->_dateTime = $dateTime;
        $this->_date = $date;
        $this->_priceCurrency = $priceCurrency;
    }

    public function formatDate($type, $field)
    {
        $ret = null;
        $object = isset($this->_config[$type]) ? $this->_config[$type] : null;

        if ($object) {
            $ret = $this->_dateTime->formatDate($object->getData($field), false);
        }

        return $ret;
    }

    public function formatTime($type, $field)
    {

        $ret = null;
        $object = isset($this->_config[$type]) ? $this->_config[$type] : null;

        if ($object) {
            $ret = $this->_dateTime->formatDate($object->getData($field), true);
        }

        return $ret;
    }

    public function formatPrice($type, $field)
    {
        $object = isset($this->_config[$type]) ? $this->_config[$type] : null;

        return $this->_priceCurrency->format(
            $object->getData($field),
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $object->getStore()
        );
    }
}
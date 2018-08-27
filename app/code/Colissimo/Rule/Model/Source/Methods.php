<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model\Source;

use Magento\Shipping\Model\Config\Source\Allmethods;
use Colissimo\Rule\Model\Rule;

/**
 * Class Method
 */
class Methods extends Allmethods
{

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        $output = [];

        if (isset($options[Rule::RULE_SHIPPING_CODE]['value'])) {
            $output = $options[Rule::RULE_SHIPPING_CODE]['value'];
        }

        return $output;
    }
}
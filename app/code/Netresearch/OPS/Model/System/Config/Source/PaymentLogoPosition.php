<?php

namespace Netresearch\OPS\Model\System\Config\Source;

/**
 * Class PaymentLogoPosition
 *
 * @package Netresearch\OPS\Model\System\Config\Source
 */
class PaymentLogoPosition implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'left',   'label' => __('Left')],
            ['value' => 'right',  'label' => __('Right')],
            ['value' => 'hidden', 'label' => __('Hidden')],
        ];
    }
}

<?php

namespace Orange\Reserve\Model\Config\Source;
 
class Custom implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    { 
        return [
            ['value' => 0, 'label' => __('Name')],
            ['value' => 1, 'label' => __('First Name')],
            ['value' => 2, 'label' => __('Ip Address')],
            ['value' => 3, 'label' => __('Email ID')]
        ];
    }
}
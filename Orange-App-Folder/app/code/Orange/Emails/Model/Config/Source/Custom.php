<?php

namespace Orange\Emails\Model\Config\Source;
 
class Custom implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    { 
        return [
            ['value' => 0, 'label' => __('Dev Environment')],
            ['value' => 1, 'label' => __('Production Environment')]
        ];
    }
}
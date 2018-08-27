<?php
namespace ViralLoops\Campaign\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Position implements ArrayInterface
{
    /**
     * Return array of positions
     *
     * @return array
     */
    public function toOptionArray()
    {
        $array = [
            ['value' => 'bottom-right', 'label' => 'bottom-right'],
            ['value' => 'bottom-left', 'label' => 'bottom-left'],
            ['value' => 'top-right', 'label' => 'top-right'],
            ['value' => 'top-left', 'label' => 'top-left']
        ];

        return $array;
    }
}

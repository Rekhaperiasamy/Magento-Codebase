<?php

namespace FME\Geoipultimatelock\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{

    const ENABLED = 1;
    const DISABLED = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $options = array(
            self::ENABLED => __('Enabled'),
            self::DISABLED => __('Disabled')
        );

        return $options;
    }
}

<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Model\Rule\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsActive
 */
class IsActive implements OptionSourceInterface
{

    /**
     * @var \FME\Geoipultimatelock\Model\Rule
     */
    protected $_geoipultimatelockRule;

    /**
     * Constructor
     *
     * @param \FME\Geoipultimatelock\Model\Rule $_geoipultimatelockRule
     */
    public function __construct(\FME\Geoipultimatelock\Model\Rule $_geoipultimatelockRule)
    {
        $this->_geoipultimatelockRule = $_geoipultimatelockRule;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->_geoipultimatelockRule->getAvailableStatuses();
        $options = array();
        foreach ($availableOptions as $key => $value) {
            $options[] = array(
                'label' => $value,
                'value' => $key,
            );
        }

        return $options;
    }
}

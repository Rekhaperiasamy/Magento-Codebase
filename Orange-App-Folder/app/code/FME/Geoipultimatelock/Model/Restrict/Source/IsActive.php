<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Model\Restrict\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsActive
 */
class IsActive implements OptionSourceInterface
{

    /**
     * @var \FME\Geoipultimatelock\Model\Restrict
     */
    protected $_geoipultimatelockRestrict;

    /**
     * Constructor
     *
     * @param \FME\Geoipultimatelock\Model\Rule $_geoipultimatelockRestrict
     */
    public function __construct(\FME\Geoipultimatelock\Model\Restrict $_geoipultimatelockRestrict)
    {
        $this->_geoipultimatelockRestrict = $_geoipultimatelockRestrict;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->_geoipultimatelockRestrict->getAvailableStatuses();
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

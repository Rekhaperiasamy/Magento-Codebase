<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Geoipultimatelock\Model\Rule\Source;

/**
 * Is active filter source
 */
class IsActiveFilter extends IsActive
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array_merge(array(array('label' => '', 'value' => '')), parent::toOptionArray());
    }
}

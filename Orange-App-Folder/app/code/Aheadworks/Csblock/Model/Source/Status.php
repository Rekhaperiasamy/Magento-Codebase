<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\Csblock\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Product status functionality model
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Product Status values
     */
    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 0;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [self::STATUS_ENABLED => __('Enable'), self::STATUS_DISABLED => __('Disable')];
    }

    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_ENABLED,  'label' => __('Enabled')],
            ['value' => self::STATUS_DISABLED,  'label' => __('Disabled')],
        ];
    }

    public function toOptionArrayForMassStatus()
    {
        return [
            ['value' => self::STATUS_ENABLED,  'label' => __('Enable')],
            ['value' => self::STATUS_DISABLED,  'label' => __('Disable')],
        ];
    }
}

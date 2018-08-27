<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Config\Source;

/**
 * Class Position
 * @package Scommerce\Gdpr\Model\Config\Source
 */
class Position implements \Magento\Framework\Option\ArrayInterface
{
    const TOP = 0;
    const BOTTOM = 1;

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::BOTTOM, 'label' => __('Bottom')],
            ['value' => self::TOP,    'label' => __('Top')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::TOP => __('Top'), self::BOTTOM => __('Bottom')];
    }
}

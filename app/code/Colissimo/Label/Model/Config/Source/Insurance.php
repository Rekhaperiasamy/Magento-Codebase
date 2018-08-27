<?php
/**
 * Colissimo Label Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Label\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Insurance
 */
class Insurance implements ArrayInterface
{
    /**
     * Get options as array
     *
     * @return array
     */
    public function toOptionArray()
    {

        return [
            [
                'value' => 0,
                'label' => __('Without insurance')
            ],
            [
                'value' => 1,
                'label' => __('With insurance')
            ],
        ];
    }

    /**
     * Get options as array
     *
     * @return array
     */
    public function toArray()
    {
        return [0, 1];
    }
}
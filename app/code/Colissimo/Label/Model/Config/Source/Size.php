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
 */
class Size implements ArrayInterface
{
    /**
     * Get options as array
     *
     * @return array
     */
    public function toOptionArray()
    {

        $options = [];

        foreach ($this->toArray() as $value => $label)
        {
            $options[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $options;
    }

    /**
     * Get options as array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'PDF_10x15_300dpi' => '10x15 300dpi',
            'PDF_A4_300dpi'    => 'A4 300dpi',
        ];
    }
}
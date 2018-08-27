<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Model\Config\Backend\Serialized;

use Magento\Config\Model\Config\Backend\Serialized;

/**
 * Class ArraySerialized
 */
class ArraySerialized extends Serialized
{

    /**
     * Unset array element with '__empty' key
     *
     * @return Serialized
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
            $convert = ['price', 'weight_from', 'weight_to'];
            foreach ($value as $key => $data) {
                foreach ($convert as $field) {
                    if (!isset($data[$field])) {
                        continue;
                    }
                    if (!$data[$field]) {
                        continue;
                    }
                    $value[$key][$field] = $this->convertFloat($data[$field]);
                }
            }
        }
        $this->setValue($value);
        return parent::beforeSave();
    }

    /**
     * Convert value to float
     *
     * @param string $value
     * @return float
     */
    protected function convertFloat($value)
    {
        return floatval(preg_replace('/,/', '.', $value));
    }
}

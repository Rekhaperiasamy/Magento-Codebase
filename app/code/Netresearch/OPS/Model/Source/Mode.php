<?php
/**
 * Mode.php
 * @author  paul.siedler@netresearch.de
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License
 */

namespace Netresearch\OPS\Model\Source;

class Mode
{
    const PROD = 'prod';
    const TEST = 'test';
    const CUSTOM = 'custom';

    /**
     *
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TEST, 'label' => __('%1', self::TEST)],
            ['value' => self::PROD, 'label' => __('%1', self::PROD)],
            ['value' => self::CUSTOM, 'label' => __('%1', self::CUSTOM)]
        ];
    }
}

<?php
/**
 * TemplateType.php
 * @author  paul.siedler@netresearch.de
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License
 */

namespace Netresearch\OPS\Model\Source;

class TemplateType
{
    const URL = 'url';
    const ID  = 'id';

    public function toOptionArray()
    {
        return [
            ['value' => self::URL, 'label' => __('%1', self::URL)],
            ['value' => self::ID, 'label' => __('%1' . self::ID)]
        ];
    }
}

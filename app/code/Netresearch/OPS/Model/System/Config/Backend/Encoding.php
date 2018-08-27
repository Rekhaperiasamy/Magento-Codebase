<?php
/**
 * Netresearch OPS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2012 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Model\System\Config\Backend;

/**
 *
 *
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @author      Sebastian Ertner <sebastian.ertner@netresearch.de>
 */

class Encoding extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'utf-8', 'label' => __('UTF-8')],
            ['value' => 'other', 'label' => __('Other')],
        ];
    }
}

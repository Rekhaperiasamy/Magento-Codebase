<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Model\Source\Kwixo;

/**
 * Source Model for ShipMethodTypes
 */
class ShipMethodType
{

    const PICK_UP_AT_MERCHANT = 1;

    const COLLECTION_POINT = 2;

    const COLLECT_AT_AIRPORT = 3;

    const TRANSPORTER = 4;

    const DOWNLOAD = 5;

    /**
     * return options as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => "",
                'label' => __('--Please select--')
            ],
            [
                'value' => self::PICK_UP_AT_MERCHANT,
                'label' => __('Pick up at merchant')
            ],
            [
                'value' => self::COLLECTION_POINT,
                'label' => __('Collection point (Kiala...)')
            ],
            [
                'value' => self::COLLECT_AT_AIRPORT,
                'label' => __('Collect at airport, train station or travel agency')
            ],
            [
                'value' => self::TRANSPORTER,
                'label' => __('Transporter (La Poste, UPS...)')
            ],
            [
                'value' => self::DOWNLOAD,
                'label' => __('Download')
            ]
        ];
    }

    public function getValidValues()
    {
        return [
            self::PICK_UP_AT_MERCHANT,
            self::COLLECTION_POINT,
            self::COLLECT_AT_AIRPORT,
            self::TRANSPORTER,
            self::DOWNLOAD
        ];
    }
}

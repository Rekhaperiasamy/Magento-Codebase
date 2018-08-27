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
 * Source Model for ProductCategories
 */
class ProductCategories
{
    /**
     * return the product categories as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 1,
                'label' => __('Food & gastronomy')
            ],
            [
                'value' => 2,
                'label' => __('Car & Motorbike')
            ],
            [
                'value' => 3,
                'label' => __('Culture & leisure')
            ],
            [
                'value' => 4,
                'label' => __('Home & garden')
            ],
            [
                'value' => 5,
                'label' => __('Appliances')
            ],
            [
                'value' => 6,
                'label' => __('Auctions and bulk purchases')
            ],
            [
                'value' => 7,
                'label' => __('Flowers & gifts')
            ],
            [
                'value' => 8,
                'label' => __('Computer & software')
            ],
            [
                'value' => 9,
                'label' => __('Health & beauty')
            ],
            [
                'value' => 10,
                'label' => __('Services for individuals')
            ],
            [
                'value' => 11,
                'label' => __('Services for professionals')
            ],
            [
                'value' => 12,
                'label' => __('Sports')
            ],
            [
                'value' => 13,
                'label' => __('Clothing & accessories')
            ],
            [
                'value' => 14,
                'label' => __('Travel & tourism')
            ],
            [
                'value' => 15,
                'label' => __('Hifi, photo & video')
            ],
            [
                'value' => 16,
                'label' => __('Telephony & communication')
            ],
            [
                'value' => 17,
                'label' => __('Jewelry & precious metals')
            ],
            [
                'value' => 18,
                'label' => __('Baby articles and accessories')
            ],
            [
                'value' => 19,
                'label' => __('Sound & light')
            ]
        ];
    }

    public function getValidKwixoCategoryIds()
    {
        $kwixoValues = [];
        foreach ($this->toOptionArray() as $option) {
            $kwixoValues[] = $option['value'];
        }
        return $kwixoValues;
    }
}

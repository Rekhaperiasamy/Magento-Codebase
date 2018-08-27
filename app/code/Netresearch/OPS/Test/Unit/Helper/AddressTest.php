<?php
/**
 * Netresearch_OPS
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
 * @copyright Copyright (c) 2017 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 *
 * AddressTest.php
 *
 * @category  Shop
 * @package   Netresearch_OPS
 * @author    Paul Siedler <paul.siedler@netresearch.de>
 */
namespace Test\Unit\Helper;

use Netresearch\OPS\Helper\Address;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideAddresses
     *
     * @param  string[] $test
     * @param  string[] $result
     */
    public function testSplitStreet($test, $result)
    {
        $this->assertEquals($result, Address::splitStreet($test));
    }

    public function provideAddresses()
    {
        return [
            'Expect Fallback'            => [
                ['Clematis Cottage, Mill Lane', 'Bartholomew'],
                [
                    Address::STREET_NAME   => 'Clematis Cottage, Mill Lane',
                    Address::STREET_NUMBER => '',
                    Address::SUPPLEMENT    => 'Bartholomew'
                ]
            ],
            'Street, Number, Supplement' => [
                ['3940 Radio Road', 'Unit 110'],
                [
                    Address::STREET_NAME   => 'Radio Road',
                    Address::STREET_NUMBER => '3940',
                    Address::SUPPLEMENT    => 'Unit 110'
                ]
            ],
            'Street, Number'             => [
                ['Nafarroa Kalea 9'],
                [
                    Address::STREET_NAME   => 'Nafarroa Kalea',
                    Address::STREET_NUMBER => '9',
                    Address::SUPPLEMENT    => ''
                ]
            ],
            'Austrian Address'           => [
                ['Lieblgasse 2/41/7/21'],
                [
                    Address::STREET_NAME   => 'Lieblgasse',
                    Address::STREET_NUMBER => '2/41/7/21',
                    Address::SUPPLEMENT    => ''
                ]
            ]
        ];
    }
}

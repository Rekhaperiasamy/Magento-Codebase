<?php

namespace Netresearch\OPS\Test\Unit\Block\Form;

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
 * @copyright Copyright (c) 2016 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 */

/**
 * InterSolveTest.php
 *
 * @category Payment
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */
?>
<?php


class InterSolveTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInterSolveBrands()
    {
        $configMock = $this->getMock('Netresearch\OPS\Model\Config', ['getIntersolveBrands'], [], '', false, false);
        $configMock->expects($this->once())
            ->method('getIntersolveBrands')
            ->will($this->returnValue($this->getBrandArray()));

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        /** @var \Netresearch\OPS\Block\Form\InterSolve $block */
        $block = $objectManager->getObject('Netresearch\OPS\Block\Form\InterSolve', ['oPSConfig' => $configMock]);

        $this->assertEquals($this->getBrandArray(), $block->getInterSolveBrands());
    }

    private function getBrandArray()
    {
        return [
            ['brand' => 'foo', 'title' => 'bar']
        ];
    }
}

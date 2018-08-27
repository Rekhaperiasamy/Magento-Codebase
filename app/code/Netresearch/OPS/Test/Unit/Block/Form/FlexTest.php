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
 * FlexTest.php
 *
 * @category Payment
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */
?>
<?php


class FlexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
    }

    public function testGetFlexMethods()
    {
        $method = $this->getMockForAbstractClass('Magento\Payment\Model\MethodInterface');
        $method->expects($this->any())
            ->method('getConfigData')
            ->with('methods')
            ->will($this->returnValue($this->getMethodArray()));

        /** @var \Netresearch\OPS\Block\Form\Flex $block */
        $block = $this->objectManager->getObject('Netresearch\OPS\Block\Form\Flex');
        $block->setMethod($method);

        $this->assertTrue(is_array($block->getFlexMethods()));
        $this->assertEquals(1, count($block->getFlexMethods()));
    }

    private function getMethodArray()
    {
        return [
            [
                'title' => 'foo',
                'brand' => 'bar',
                'pm'    => 'zzz'
            ]
        ];
    }

    public function testGetDefaultOptionTitle()
    {
        $method = $this->getMockForAbstractClass('Magento\Payment\Model\MethodInterface');
        $method->expects($this->any())
            ->method('getConfigData')
            ->with('default_title')
            ->will($this->returnValue('flex'));

        /** @var \Netresearch\OPS\Block\Form\Flex $block */
        $block = $this->objectManager->getObject('Netresearch\OPS\Block\Form\Flex');
        $block->setMethod($method);

        $this->assertEquals('flex', $block->getDefaultOptionTitle());
    }

    public function testIsDefaultOptionActive()
    {
        $method = $this->getMockForAbstractClass('Magento\Payment\Model\MethodInterface');
        $method->expects($this->any())
            ->method('getConfigData')
            ->with('default')
            ->will($this->returnValue(true));

        /** @var \Netresearch\OPS\Block\Form\Flex $block */
        $block = $this->objectManager->getObject('Netresearch\OPS\Block\Form\Flex');
        $block->setMethod($method);

        $this->assertTrue($block->isDefaultOptionActive());
    }
}

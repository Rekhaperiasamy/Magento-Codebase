<?php

namespace Netresearch\OPS\Test\Unit\Block\Info;

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
    public function testGetFlexTitle()
    {
        $infoInstance = $this->getMockForAbstractClass('\Magento\Payment\Model\InfoInterface');
        $infoInstance->expects($this->once())
            ->method('getAdditionalInformation')
            ->with(\Netresearch\OPS\Model\Payment\Flex::INFO_KEY_TITLE)
            ->will($this->returnValue('FLEX'));
        $method = new \Magento\Framework\DataObject();
        $method->setInfoInstance($infoInstance);

        $info = $this->getMockForAbstractClass('Magento\Payment\Model\InfoInterface');
        $info->expects($this->once())
            ->method('getMethodInstance')
            ->will($this->returnValue($method));

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        /** @var \Netresearch\OPS\Block\Info\Flex $block */
        $block = $objectManager->getObject('Netresearch\OPS\Block\Info\Flex');
        $block->setInfo($info);

        $this->assertEquals('FLEX', $block->getFlexTitle());
    }
}

<?php

namespace Netresearch\OPS\Test\Unit\Block\Alias;

/**
 * @category   OPS
 * @package    Netresearch_OPS
 * @author     Thomas Birke <thomas.birke@netresearch.de>
 * @author     Michael Lühr <michael.luehr@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Netresearch_OPS_Test_Block_Alias_ListTest
 *
 * @author     Thomas Birke <thomas.birke@netresearch.de>
 * @author     Michael Lühr <michael.luehr@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ListTest extends \PHPUnit_Framework_TestCase
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

    public function testGetMethodName()
    {
        /** @var \Netresearch\OPS\Block\Alias\AliasList $block */
        $block = $this->objectManager->getObject('Netresearch\OPS\Block\Alias\AliasList');
        $this->assertNull($block->getMethodName('something_stupid'));

        $paymentHelper = $this->getMock('Magento\Payment\Helper\Data', ['getMethodInstance'], [], '', false, false);
        $method = $this->getMockForAbstractClass('Magento\Payment\Model\MethodInterface', ['getTitle']);
        $method->expects($this->once())
            ->method('getTitle')
            ->will($this->returnValue('OPS Credit Card'));
        $paymentHelper->expects($this->once())
            ->method('getMethodInstance')
            ->will($this->returnValue($method));

        /** @var \Netresearch\OPS\Block\Alias\AliasList $block */
        $block = $this->objectManager
            ->getObject(
                'Netresearch\OPS\Block\Alias\AliasList',
                [
                    'paymentHelper' => $paymentHelper
                ]
            );
        $this->assertEquals(
            'OPS Credit Card',
            $block->getMethodName('ops_cc')
        );
    }

    public function testGetAliasDeleteUrl()
    {
        $aliasId = 1;

        $request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false, false);
        $request->expects($this->once())
            ->method('isSecure')
            ->will($this->returnValue(true));

        $urlBuilder = $this->getMock('Magento\Framework\Url', [], [], '', false, false);
        $urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with(
                'ops/customer/deleteAlias/',
                [
                    'id' => $aliasId,
                    '_secure' => true
                ]
            )
            ->will($this->returnValue('opsCustomerDeleteAlias'));

        $context = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false, false);
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilder));

        $context->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        /** @var \Netresearch\OPS\Block\Alias\AliasList $block */
        $block = $this->objectManager
            ->getObject(
                'Netresearch\OPS\Block\Alias\AliasList',
                [
                    'context' => $context
                ]
            );

        $this->assertEquals('opsCustomerDeleteAlias', $block->getAliasDeleteUrl($aliasId));
    }
}

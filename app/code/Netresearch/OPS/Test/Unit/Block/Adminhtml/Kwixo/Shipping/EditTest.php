<?php

namespace Netresearch\OPS\Test\Unit\Block\Adminhtml\Kwixo\Shipping;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
    }

    public function testGetFormActionUrl()
    {
        $urlBuilder = $this->getMock('Magento\Framework\Url', [], [], '', false, false);
        $urlBuilder->expects($this->any())
            ->method('getUrl')
            ->with($this->anything())
            ->will($this->returnValue('adminhtmlKwixoshippingUrl'));
        /** @var \Netresearch\OPS\Block\Adminhtml\Kwixo\Shipping\Edit $block */
        $block = $this->objectManager
            ->getObject(
                'Netresearch\OPS\Block\Adminhtml\Kwixo\Shipping\Edit',
                ['urlBuilder' => $urlBuilder]
            );
        $this->assertEquals('adminhtmlKwixoshippingUrl', $block->getFormActionUrl());
    }

    public function testGetShippingMethods()
    {
        $shippingConfigMock = $this->getMock('Magento\Shipping\Model\Config', [], [], '', false, false);
        $setting = $this->getMock('Netresearch\OPS\Model\Kwixo\Shipping\Setting', ['load', 'getData'], [], '', false, false);
        $setting->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());
        $settingFactory = $this->getMock('Netresearch\OPS\Model\Kwixo\Shipping\SettingFactory', ['create'], [], '', false, false);
        $settingFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($setting));

        $shippingConfigMock->expects($this->any())
            ->method('getAllCarriers')
            ->will(
                $this->returnValue(
                    ['dhl' => 'dhl', 'hermes' => 'hermes', 'ips' => 'ips']
                )
            );

        /** @var \Netresearch\OPS\Block\Adminhtml\Kwixo\Shipping\Edit $block */
        $block = $this->objectManager
            ->getObject(
                'Netresearch\OPS\Block\Adminhtml\Kwixo\Shipping\Edit',
                [
                    'shippingConfig' => $shippingConfigMock,
                    'oPSKwixoShippingSettingFactory' => $settingFactory
                ]
            );

        $result = $block->getShippingMethods();
        $this->assertEquals(3, count($result));
        $this->assertEquals($result[0]['code'], 'dhl');
        $this->assertEquals($result[1]['code'], 'hermes');
        $this->assertEquals($result[1]['label'], 'hermes');
        $this->assertEquals($result[2]['code'], 'ips');
        $this->assertEquals($result[2]['label'], 'ips');

        $block->setData(
            'postData',
            ['dhl' => ['error' => 'sample error']]
        );
        $result = $block->getShippingMethods();
        $this->assertEquals($result[0]['values']['error'], 'sample error');
    }

    public function testGetKwixoShippingTypes()
    {
        $methodType = $this->getMock('Netresearch\OPS\Model\Source\Kwixo\ShipMethodType', ['toOptionArray'], [], '', false, false);
        $methodTypeFactory = $this->getMock('Netresearch\OPS\Model\Source\Kwixo\ShipMethodTypeFactory', ['create'], [], '', false, false);
        $methodTypeFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($methodType));
        $methodType->expects($this->once())
            ->method('toOptionArray')
            ->will($this->returnValue(['dummy_one', 'dummy_two']));
        /** @var \Netresearch\OPS\Block\Adminhtml\Kwixo\Shipping\Edit $block */
        $block = $this->objectManager
            ->getObject(
                'Netresearch\OPS\Block\Adminhtml\Kwixo\Shipping\Edit',
                [
                    'oPSSourceKwixoShipMethodTypeFactory' => $methodTypeFactory
                ]
            );
        $this->assertEquals(['dummy_one', 'dummy_two'], $block->getKwixoShippingTypes());
    }


    public function testGetFormKey()
    {
        $formKey = $this->getMock('Magento\Framework\Data\Form\FormKey', ['getFormKey'], [], '', false, false);
        $formKey->expects($this->once())
            ->method('getFormKey')
            ->will($this->returnValue('_formKey_'));
        /** @var \Netresearch\OPS\Block\Adminhtml\Kwixo\Shipping\Edit $block */
        $block = $this->objectManager
            ->getObject(
                'Netresearch\OPS\Block\Adminhtml\Kwixo\Shipping\Edit',
                [
                    'formKey' => $formKey
                ]
            );
        $this->assertEquals('_formKey_', $block->getFormKey());
    }
}

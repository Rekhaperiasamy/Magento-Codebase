<?php

namespace Netresearch\OPS\Test\Unit\Block;

class PlaceformTest extends \PHPUnit_Framework_TestCase
{
    public function testIsKwixoPaymentMethodTrue()
    {
        $order = new \Magento\Framework\DataObject();
        $payment = new \Magento\Framework\DataObject();
        $method = $this->getMock('Netresearch\OPS\Model\Payment\Kwixo\KwixoAbstract', [], [], '', false, false);
        $payment->setMethodInstance($method);
        $order->setPayment($payment);

        /** @var \Netresearch\OPS\Block\Placeform $blockMock */
        $blockMock = $this->getMock('Netresearch\OPS\Block\Placeform', ['_getOrder'], [], '', false, false);
        $blockMock->expects($this->any())
            ->method('_getOrder')
            ->will($this->returnValue($order));
        
        $this->assertTrue($blockMock->isKwixoPaymentMethod());
    }
    
    public function testIsKwixoPaymentMethodFalse()
    {
        $order = new \Magento\Framework\DataObject();
        $payment = new \Magento\Framework\DataObject();
        $method = $this->getMock('Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $payment->setMethodInstance($method);
        $order->setPayment($payment);

        /** @var \Netresearch\OPS\Block\Placeform $blockMock */
        $blockMock = $this->getMock('Netresearch\OPS\Block\Placeform', ['_getOrder'], [], '', false, false);
        $blockMock->expects($this->any())
            ->method('_getOrder')
            ->will($this->returnValue($order));
        
        $this->assertFalse($blockMock->isKwixoPaymentMethod());
    }
}

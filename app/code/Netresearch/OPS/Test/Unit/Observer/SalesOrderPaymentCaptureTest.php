<?php

namespace Netresearch\OPS\Test\Unit\Observer;

class SalesOrderPaymentCaptureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Observer\SalesOrderPaymentCapture
     */
    private $object;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    private $observer;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->observer      = new \Magento\Framework\Event\Observer();
        $this->object        = $this->objectManager->getObject('\Netresearch\OPS\Observer\SalesOrderPaymentCapture');
    }

    public function testExecute()
    {
        $invoice = new \Magento\Framework\DataObject();
        $paymentMethodInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\PaymentAbstract',
            [],
            [],
            '',
            false,
            false
        );
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['getMethodInstance'], [], '', false, false);
        $payment->expects($this->once())->method('getMethodInstance')->will($this->returnValue($paymentMethodInstance));

        $this->observer->setPayment($payment);
        $this->observer->setInvoice($invoice);

        $this->object->execute($this->observer);

        $this->assertEquals($invoice, $payment->getInvoice());
    }

    public function testExecuteInvalidPayment()
    {
        $invoice = new \Magento\Framework\DataObject();
        $paymentMethodInstance = $this->getMock('Magento\Payment\Model\Method\Free', [], [], '', false, false);
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['getMethodInstance'], [], '', false, false);
        $payment->expects($this->once())->method('getMethodInstance')->will($this->returnValue($paymentMethodInstance));

        $this->observer->setPayment($payment);
        $this->observer->setInvoice($invoice);

        $this->object->execute($this->observer);

        $this->assertEquals(null, $payment->getInvoice());
    }
}

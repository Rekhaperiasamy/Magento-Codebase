<?php

namespace Netresearch\OPS\Test\Unit\Helper\Order;

class CaptureTest extends \PHPUnit_Framework_TestCase
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

    public function testOperationPartialAndTypePartial()
    {
        $invoice = ["items" => "foo"];
        $expected = [
            "items"     => $invoice["items"],
            "operation" => \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_PARTIAL,
            "type"      => "partial",
            "amount"    => 100.00
        ];

        $payment = new \Magento\Framework\DataObject();
        $order = new \Magento\Framework\DataObject();

        $payment->setOrder($order);

        $request = $this->getMock('\Magento\Framework\App\Request\Http', [], [], '', false, false);
        $paymentHelper = $this->getMock('\Netresearch\OPS\Helper\Payment', [], [], '', false, false);

        $request->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue(['invoice' => $invoice]));

        $paymentHelper->expects($this->exactly(2))
            ->method('getBaseGrandTotalFromSalesObject')
            ->with($order)
            ->will($this->returnValue(119.00));

        /** @var \Netresearch\OPS\Helper\Order\Capture $helper */
        $helper = $this->objectManager
            ->getObject(
                'Netresearch\OPS\Helper\Order\Capture',
                [
                    'oPSPaymentHelper' => $paymentHelper,
                    'request' => $request
                ]
            );

        $this->assertEquals($expected, $helper->prepareOperation($payment, 100.00));
    }

    public function testOperationFullAndTypePartial()
    {
        $invoice = ["items" => "foo"];
        $expected = [
            "items"     => $invoice["items"],
            "operation" => \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_FULL,
            "type"      => "partial",
            "amount"    => 100.00
        ];

        $payment = new \Magento\Framework\DataObject();
        $order = new \Magento\Framework\DataObject();

        $payment->setBaseAmountPaidOnline(19.00);
        $payment->setOrder($order);

        $request = $this->getMock('\Magento\Framework\App\Request\Http', [], [], '', false, false);
        $paymentHelper = $this->getMock('\Netresearch\OPS\Helper\Payment', [], [], '', false, false);

        $request->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue(['invoice' => $invoice]));

        $paymentHelper->expects($this->exactly(2))
            ->method('getBaseGrandTotalFromSalesObject')
            ->with($order)
            ->will($this->returnValue(119.00));

        /** @var \Netresearch\OPS\Helper\Order\Capture $helper */
        $helper = $this->objectManager
            ->getObject(
                'Netresearch\OPS\Helper\Order\Capture',
                [
                    'oPSPaymentHelper' => $paymentHelper,
                    'request' => $request
                ]
            );

        $this->assertEquals($expected, $helper->prepareOperation($payment, 100.00));
    }

    public function testOperationFullAndTypeFull()
    {
        $invoice = ["items" => "foo"];
        $expected = [
            "items"     => $invoice["items"],
            "operation" => \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_FULL,
            "type"      => "full",
            "amount"    => 119.00
        ];

        $payment = new \Magento\Framework\DataObject();
        $order = new \Magento\Framework\DataObject();

        $payment->setBaseAmountPaidOnline(0.00);
        $payment->setOrder($order);

        $request = $this->getMock('\Magento\Framework\App\Request\Http', [], [], '', false, false);
        $paymentHelper = $this->getMock('\Netresearch\OPS\Helper\Payment', [], [], '', false, false);

        $request->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue(['invoice' => $invoice]));

        $paymentHelper->expects($this->exactly(2))
            ->method('getBaseGrandTotalFromSalesObject')
            ->with($order)
            ->will($this->returnValue(119.00));

        /** @var \Netresearch\OPS\Helper\Order\Capture $helper */
        $helper = $this->objectManager
            ->getObject(
                'Netresearch\OPS\Helper\Order\Capture',
                [
                    'oPSPaymentHelper' => $paymentHelper,
                    'request' => $request
                ]
            );

        $this->assertEquals($expected, $helper->prepareOperation($payment, 119.00));
    }
}

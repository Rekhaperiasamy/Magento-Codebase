<?php

namespace Netresearch\OPS\Test\Unit\Helper\Order;

class RefundTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * @var \Magento\Framework\DataObject
     */
    private $payment;

    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    private $paymentHelper;

    /**
     * @var \Netresearch\OPS\Helper\Order\Refund
     */
    private $helper;

    public function setUp()
    {
        parent::setUp();

        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $this->order->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(11));
        $this->order->expects($this->any())
                    ->method('getBaseGrandTotal')
                    ->will($this->returnValue(119.00));

        $this->payment = new \Magento\Framework\DataObject();
        $this->payment->setOrder($this->order);
        $this->payment->setBaseAmountRefundedOnline(0.00);

        $this->paymentHelper = $this->getMock('\Netresearch\OPS\Helper\Payment', [], [], '', false, false);
        $this->paymentHelper->expects($this->any())
            ->method('getBaseGrandTotalFromSalesObject')
            ->will($this->returnValue($this->order->getBaseGrandTotal()));

        $this->helper = $this->objectManager->getObject(
            '\Netresearch\OPS\Helper\Order\Refund',
            ['oPSPaymentHelper' => $this->paymentHelper]
        );
        $this->helper->setCreditMemoRequestParams(["creditmemo" => ["items" => "foo"]]);
    }

    public function testDetermineOperationCode()
    {
        // complete refund should lead to RFS
        $this->payment->setBaseAmountRefundedOnline(0.00);
        $amount = 119.00;
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_FULL,
            $this->helper->determineOperationCode($this->payment, $amount)
        );

        // partial refund should lead to RFD
        $this->payment->setBaseAmountRefundedOnline(0.00);
        $amount = 100.00;
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_PARTIAL,
            $this->helper->determineOperationCode($this->payment, $amount)
        );

        // partial refund + new amount to refund should lead to RFS
        $this->payment->setBaseAmountRefundedOnline(19.00);
        $amount = 100.00;
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_FULL,
            $this->helper->determineOperationCode($this->payment, $amount)
        );

        // partial refund + new amount to refund should lead to RFS
        $this->payment->setBaseAmountRefundedOnline(116.99);
        $amount = 2.01;
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_FULL,
            $this->helper->determineOperationCode($this->payment, $amount)
        );

        // partial refund + new amount to refund should lead to RFS
        $this->payment->setBaseAmountRefundedOnline(116.99);
        $amount = 2.00;
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_FULL,
            $this->helper->determineOperationCode($this->payment, $amount)
        );
    }

    public function testOperationPartialAndTypePartial()
    {
        $expected = [
            "items"     => "foo",
            "operation" => \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_PARTIAL,
            "type"      => "partial",
            "amount"    => 100.00
        ];

        $this->payment->setBaseAmountRefundedOnline(0.00);
        $amount = 100.00;
        $this->assertEquals($expected, $this->helper->prepareOperation($this->payment, $amount));
    }

    public function testOperationFullAndTypePartial()
    {
        $expected = [
            "items"     => "foo",
            "operation" => \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_FULL,
            "type"      => "partial",
            "amount"    => 100.00
        ];

        $this->payment->setBaseAmountRefundedOnline(19.00);
        $amount = 100.00;
        $this->assertEquals($expected, $this->helper->prepareOperation($this->payment, $amount));
    }

    public function testOperationFullAndTypeFull()
    {
        $expected = [
            "items"     => "foo",
            "operation" => \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_FULL,
            "type"      => "full",
            "amount"    => 119.00
        ];

        $this->payment->setBaseAmountRefundedOnline(0.00);
        $amount = 119.00;
        $this->assertEquals($expected, $this->helper->prepareOperation($this->payment, $amount));
    }
}

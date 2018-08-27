<?php
namespace Netresearch\OPS\Test\Unit\Observer;

class SetOrderStateDirectLinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Observer\SetOrderStateDirectLink
     */
    private $object;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    private $observer;

    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    private $paymentHelper;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->observer      = new \Magento\Framework\Event\Observer();
        $this->paymentHelper = $this->getMock('\Netresearch\OPS\Helper\Payment', [], [], '', false, false);
        $this->object        = $this->objectManager->getObject(
            '\Netresearch\OPS\Observer\SetOrderStateDirectLink',
            ['oPSPaymentHelper' => $this->paymentHelper]
        );
    }

    public function testExecute()
    {
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['setState', 'setStatus', 'addStatusHistoryComment', 'getState'],
            [],
            '',
            false,
            false
        );
        $order->expects($this->once())->method('getState')
            ->will($this->returnValue(\Magento\Sales\Model\Order::STATE_NEW));
        $paymentMethodInstance = $this->getMock('\Netresearch\OPS\Model\Payment\DirectDebit', [], [], '', false, false);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['getMethodInstance'], [], '', false, false);
        $payment->expects($this->once())->method('getMethodInstance')->will($this->returnValue($paymentMethodInstance));
        $payment->setOrder($order);
        $payment->setAdditionalInformation('status', \Netresearch\OPS\Model\Status::AUTHORIZED);
        $this->paymentHelper->expects($this->once())->method('isInlinePaymentWithOrderId')
            ->with($payment)->will($this->returnValue(true));

        $order->expects($this->once())->method('setStatus')->with(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $order->expects($this->once())->method('setState')->with(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $order->expects($this->once())->method('addStatusHistoryComment')
            ->with(__('Payment has been authorized by Ingenico ePayments, but not yet captured.'));

        $this->observer->setPayment($payment);

        $this->object->execute($this->observer);
    }

    public function testExecuteInvalidPayment()
    {
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['setState', 'setStatus', 'addStatusHistoryComment', 'getState'],
            [],
            '',
            false,
            false
        );
        $order->expects($this->never())->method('getState')
            ->will($this->returnValue(\Magento\Sales\Model\Order::STATE_NEW));
        $paymentMethodInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['getMethodInstance'], [], '', false, false);
        $payment->expects($this->once())->method('getMethodInstance')->will($this->returnValue($paymentMethodInstance));
        $payment->setOrder($order);
        $payment->setAdditionalInformation('status', \Netresearch\OPS\Model\Status::AUTHORIZED);
        $this->paymentHelper->expects($this->never())->method('isInlinePaymentWithOrderId')
            ->with($payment)->will($this->returnValue(true));

        $order->expects($this->never())->method('setStatus')->with(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $order->expects($this->never())->method('setState')->with(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $order->expects($this->never())->method('addStatusHistoryComment')
            ->with(__('Payment has been authorized by Ingenico ePayments, but not yet captured.'));

        $this->observer->setPayment($payment);

        $this->object->execute($this->observer);
    }

    public function testExecuteInvalidPaymentStatus()
    {
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['setState', 'setStatus', 'addStatusHistoryComment', 'getState'],
            [],
            '',
            false,
            false
        );
        $order->expects($this->never())->method('getState')
            ->will($this->returnValue(\Magento\Sales\Model\Order::STATE_NEW));
        $paymentMethodInstance = $this->getMock('\Netresearch\OPS\Model\Payment\DirectDebit', [], [], '', false, false);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['getMethodInstance'], [], '', false, false);
        $payment->expects($this->once())->method('getMethodInstance')->will($this->returnValue($paymentMethodInstance));
        $payment->setOrder($order);
        $payment->setAdditionalInformation('status', \Netresearch\OPS\Model\Status::AUTHORIZATION_WAITING);
        $this->paymentHelper->expects($this->once())->method('isInlinePaymentWithOrderId')
            ->with($payment)->will($this->returnValue(true));

        $order->expects($this->never())->method('setStatus')->with(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $order->expects($this->never())->method('setState')->with(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $order->expects($this->never())->method('addStatusHistoryComment')
            ->with(__('Payment has been authorized by Ingenico ePayments, but not yet captured.'));

        $this->observer->setPayment($payment);

        $this->object->execute($this->observer);
    }

    public function testExecuteInvalidOrderState()
    {
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['setState', 'setStatus', 'addStatusHistoryComment', 'getState'],
            [],
            '',
            false,
            false
        );
        $order->expects($this->once())->method('getState')
            ->will($this->returnValue(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT));
        $paymentMethodInstance = $this->getMock('\Netresearch\OPS\Model\Payment\DirectDebit', [], [], '', false, false);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['getMethodInstance'], [], '', false, false);
        $payment->expects($this->once())->method('getMethodInstance')->will($this->returnValue($paymentMethodInstance));
        $payment->setOrder($order);
        $payment->setAdditionalInformation('status', \Netresearch\OPS\Model\Status::AUTHORIZED);
        $this->paymentHelper->expects($this->once())->method('isInlinePaymentWithOrderId')
            ->with($payment)->will($this->returnValue(true));

        $order->expects($this->never())->method('setStatus')->with(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $order->expects($this->never())->method('setState')->with(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $order->expects($this->never())->method('addStatusHistoryComment')
            ->with(__('Payment has been authorized by Ingenico ePayments, but not yet captured.'));

        $this->observer->setPayment($payment);

        $this->object->execute($this->observer);
    }
}

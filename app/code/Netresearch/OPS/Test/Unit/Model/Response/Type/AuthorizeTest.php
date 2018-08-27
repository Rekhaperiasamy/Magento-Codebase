<?php
namespace Netresearch\OPS\Test\Unit\Model\Response;

/**
 * Created by PhpStorm.
 * User: sebastian
 * Date: 24.11.15
 * Time: 12:51
 */
class AuthorizeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Response\Type\Authorize
     */
    private $model;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->config        = $this->getMock('\Netresearch\OPS\Model\Config', [], [], '', false, false);
        $this->model         = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Response\Type\Authorize',
            ['config' => $this->config]
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\PaymentException
     */
    public function testExceptionThrown()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', null, [], '', false, false);
        $order->setState(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', null, [], '', false, false);
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => 2,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $this->model->handleResponse($response, $paymentInstance, false);
    }

    /**
     * @expectedException \Magento\Framework\Exception\PaymentException
     * @expectedExceptionMessage Payment failed because the authorization was declined! Please choose another payment method.
     */
    public function testExceptionThrownForNoAuthorizeStatus()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', null, [], '', false, false);
        $order->setState(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', null, [], '', false, false);
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => 2,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $this->model->handleResponse($response, $paymentInstance, false);
    }

    public function testHandleResponseWithPendingPaymentAndIntermediate()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', ['addStatusHistoryComment'], [], '', false, false);
        $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['save'], [], '', false, false);
        $payment->expects($this->any())
            ->method('save')
            ->will($this->returnSelf());
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => 51,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $order->expects($this->once())->method('addStatusHistoryComment')->will($this->returnSelf());
        $this->model->handleResponse($response, $paymentInstance, false);
        $this->assertTrue($payment->getIsTransactionPending());
    }

    public function testHandleResponseWithPaymentReviewAndFinal()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', ['addStatusHistoryComment'], [], '', false, false);
        $order->setState(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['save'], [], '', false, false);
        $payment->expects($this->any())
            ->method('save')
            ->will($this->returnSelf());
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => 5,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $order->expects($this->once())->method('addStatusHistoryComment')->will($this->returnSelf());
        $this->model->handleResponse($response, $paymentInstance, false);
        $this->assertEquals($response['status'], $payment->getAdditionalInformation('status'));
    }

    /**
     * @test
     */
    public function testHandleResponseWithPaymentReviewAndIntermediate()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', ['addStatusHistoryComment'], [], '', false, false);
        $order->setState(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['save'], [], '', false, false);
        $payment->expects($this->any())
                ->method('save')
                ->will($this->returnSelf());

        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => 51,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $order->expects($this->once())->method('addStatusHistoryComment')->will($this->returnSelf());
        $this->model->handleResponse($response, $paymentInstance, false);
        $this->assertEquals($response['status'], $payment->getAdditionalInformation('status'));
    }

    public function testHandleResponseWithPaymentReviewAndFinalDeclined()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['addStatusHistoryComment', 'save', 'getInvoiceCollection'],
            [],
            '',
            false,
            false
        );
        $order->setState(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['update', 'save'], [], '', false, false);
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => 2,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $order->expects($this->once())->method('addStatusHistoryComment')->will($this->returnSelf());
        $payment->expects($this->once())->method('update')->will($this->returnSelf());
        $this->model->handleResponse($response, $paymentInstance);
        $this->assertEquals($response['status'], $payment->getAdditionalInformation('status'));
    }

    public function testHandleResponseWithPendingPaymentAndFinal()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', ['addStatusHistoryComment'], [], '', false, false);
        $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['save'], [], '', false, false);
        $payment->expects($this->any())
            ->method('save')
            ->will($this->returnSelf());
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => 5,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $order->expects($this->once())->method('addStatusHistoryComment')->will($this->returnSelf());
        $this->model->handleResponse($response, $paymentInstance, false);
        $this->assertEquals($response['status'], $payment->getAdditionalInformation('status'));
    }

    /**
     * @test
     */
    public function testHandleResponseWithPendingPaymentAndSuspectedFraudStatus()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['addStatusHistoryComment', 'save'],
            [],
            '',
            false,
            false
        );
        $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock(
            '\Magento\Sales\Model\Order\Payment',
            ['save', 'setIsFraudDetected', 'registerAuthorizationNotification'],
            [],
            '',
            false,
            false
        );
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => \Netresearch\OPS\Model\Status::AUTHORIZED_WAITING_EXTERNAL_RESULT,
            'payid'    => 12345678,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $payment->expects($this->once())->method('registerAuthorizationNotification')->will($this->returnSelf());
        $payment->expects($this->once())->method('setIsFraudDetected')->will($this->returnSelf());
        $order->expects($this->once())->method('addStatusHistoryComment')->will($this->returnSelf());
        $this->model->handleResponse($response, $paymentInstance);
        $this->assertEquals(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW, $order->getState());
        $this->assertEquals(\Magento\Sales\Model\Order::STATUS_FRAUD, $order->getStatus());
    }

    public function testHandleResponseWithPaymentReviewAndSuspectedFraudStatus()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['addStatusHistoryComment', 'save'],
            [],
            '',
            false,
            false
        );
        $order->setState(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock(
            '\Magento\Sales\Model\Order\Payment',
            ['save', 'setIsFraudDetected', 'registerAuthorizationNotification'],
            [],
            '',
            false,
            false
        );
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => \Netresearch\OPS\Model\Status::AUTHORIZED_WAITING_EXTERNAL_RESULT,
            'payid'    => 12345678,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $payment->expects($this->once())->method('registerAuthorizationNotification')->will($this->returnSelf());
        $payment->expects($this->once())->method('setIsFraudDetected')->will($this->returnSelf());
        $order->expects($this->once())->method('addStatusHistoryComment')->will($this->returnSelf());
        $this->model->handleResponse($response, $paymentInstance);
        $this->assertEquals(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW, $order->getState());
        $this->assertEquals(\Magento\Sales\Model\Order::STATUS_FRAUD, $order->getStatus());
    }

    public function testStatusAuthorizationUnclear()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['addStatusHistoryComment', 'save'],
            [],
            '',
            false,
            false
        );
        $order->setState(\Magento\Sales\Model\Order::STATE_NEW);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock(
            '\Magento\Sales\Model\Order\Payment',
            ['save', 'setIsFraudDetected', 'registerAuthorizationNotification'],
            [],
            '',
            false,
            false
        );
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => 52,
            'payid'    => 12345678,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $payment->expects($this->once())->method('registerAuthorizationNotification')->will($this->returnSelf());
        $order->expects($this->once())->method('addStatusHistoryComment')->will($this->returnSelf());
        $this->model->handleResponse($response, $paymentInstance);
        $this->assertTrue($payment->getIsTransactionPending());
        $this->assertEquals($response['status'], $payment->getAdditionalInformation('status'));
    }
}

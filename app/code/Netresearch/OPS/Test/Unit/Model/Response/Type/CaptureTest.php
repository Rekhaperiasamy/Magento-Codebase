<?php
namespace Netresearch\OPS\Test\Unit\Model\Response;

/**
 * Netresearch_OPS
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 */
/**
 * CaptureTest.php
 *
 * @category OPS
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */
class CaptureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Response\Type\Capture
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
            '\Netresearch\OPS\Model\Response\Type\Capture',
            ['config' => $this->config]
        );
    }

    public function testHandleResponseWithPaymentReviewAndIntermediate()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', null, [], '', false, false);
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
            'status'   => 91,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $this->model->handleResponse($response, $paymentInstance, false);
        $this->assertEquals(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW, $order->getState());
        $this->assertEquals($response['status'], $payment->getAdditionalInformation('status'));
    }

    public function testHandleResponseWithPendingPaymentAndIntermediate()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', ['save'], [], '', false, false);
        $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock(
            '\Magento\Sales\Model\Order\Payment',
            ['save', 'registerCaptureNotification'],
            [],
            '',
            false,
            false
        );
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', ['save'], [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => 91,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $payment->expects($this->once())->method('registerCaptureNotification')->will($this->returnSelf());
        $this->model->handleResponse($response, $paymentInstance);
        $this->assertEquals($response['status'], $payment->getAdditionalInformation('status'));
    }

    public function testHandleResponseWithPaymentReviewAndFinal()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', ['save'], [], '', false, false);
        $order->setState(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock(
            '\Magento\Sales\Model\Order\Payment',
            ['save', 'setIsTransactionApproved', 'update'],
            [],
            '',
            false,
            false
        );
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', ['save'], [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => 9,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $payment->expects($this->once())->method('setIsTransactionApproved')->will($this->returnSelf());
        $payment->expects($this->once())->method('update')->will($this->returnSelf());
        $this->model->handleResponse($response, $paymentInstance);
    }

    public function testHandleResponseWithPendingPaymentAndFinal()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', ['save'], [], '', false, false);
        $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock(
            '\Magento\Sales\Model\Order\Payment',
            ['save', 'registerCaptureNotification'],
            [],
            '',
            false,
            false
        );
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', ['save'], [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => 9,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $payment->expects($this->once())->method('registerCaptureNotification')->will($this->returnSelf());
        $this->model->handleResponse($response, $paymentInstance);
        $this->assertEquals($response['status'], $payment->getAdditionalInformation('status'));
    }

    /**
     * @expectedException \Magento\Framework\Exception\PaymentException
     */
    public function testExceptionThrown()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', ['save'], [], '', false, false);
        $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock(
            '\Magento\Sales\Model\Order\Payment',
            ['save', 'registerCaptureNotification'],
            [],
            '',
            false,
            false
        );
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', ['save'], [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => 43,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $this->model->handleResponse($response, $paymentInstance);
    }

    /**
     * @test
     */
    public function testAbortBecauseSameStatus()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', ['save'], [], '', false, false);
        $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock(
            '\Magento\Sales\Model\Order\Payment',
            ['save', 'registerCaptureNotification'],
            [],
            '',
            false,
            false
        );
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', ['save'], [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => 9,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $payment->expects($this->once())->method('registerCaptureNotification')->will($this->returnSelf());
        $this->model->handleResponse($response, $paymentInstance);
    }
}

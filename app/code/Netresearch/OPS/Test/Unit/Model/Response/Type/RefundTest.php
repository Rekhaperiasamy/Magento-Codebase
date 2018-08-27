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
class RefundTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Response\Type\Refund
     */
    private $model;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Metadata
     */
    protected $metadata;

    /**
     * @var \Magento\Sales\Model\Order\InvoiceFactory
     */
    protected $invoiceFactory;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $dbTransaction;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Transaction\Repository
     */
    protected $transactionRepository;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager  = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->config         = $this->getMock('\Netresearch\OPS\Model\Config', [], [], '', false, false);
        $this->metadata       = $this->getMock('\Magento\Sales\Model\ResourceModel\Metadata', [], [], '', false, false);
        $this->invoiceFactory = $this->getMock('\Magento\Sales\Model\Order\InvoiceFactory', [], [], '', false, false);
        $dbTransactionFactory = $this->getMock('\Magento\Framework\DB\TransactionFactory', [], [], '', false, false);
        $this->dbTransaction  = $this->getMock('\Magento\Framework\DB\Transaction', [], [], '', false, false);
        $this->transactionRepository = $this->getMock(
            '\Magento\Sales\Model\Order\Payment\Transaction\Repository',
            [],
            [],
            '',
            false,
            false
        );
        $this->model          = $this->objectManager
            ->getObject(
                '\Netresearch\OPS\Model\Response\Type\Refund',
                [
                    'config'                => $this->config,
                    'metadata'              => $this->metadata,
                    'dbTransactionFactory'  => $dbTransactionFactory,
                    'invoiceFactory'        => $this->invoiceFactory,
                    'transactionRepository' => $this->transactionRepository
                ]
            );
        $dbTransactionFactory->expects($this->any())->method('create')->will($this->returnValue($this->dbTransaction));
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
            'status'   => 43,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $this->model->handleResponse($response, $paymentInstance, false);
    }

    /**
     * @expectedException \Magento\Framework\Exception\PaymentException
     * @expectedExceptionMessage 2 is not a refund status!
     */
    public function testExceptionThrownDueToNoRefundStatus()
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
            'status'   => \Netresearch\OPS\Model\Status::AUTHORISATION_DECLINED,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $this->model->handleResponse($response, $paymentInstance, false);
    }

    public function testAbortBecauseSameStatus()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['save', 'addStatusHistoryComment'],
            [],
            '',
            false,
            false
        );
        $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $order->expects($this->any())->method('addStatusHistoryComment')->will($this->returnSelf());
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['save'], [], '', false, false);
        $payment->expects($this->any())
            ->method('save')
            ->will($this->returnSelf());
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditMemo */
        $creditMemo = $this->getMock('\Magento\Sales\Model\Order\Creditmemo', ['save', 'load'], [], '', false, false);
        $creditMemo->expects($this->any())->method('load')->will($this->returnSelf());
        $this->metadata->expects($this->any())->method('getNewInstance')->will($this->returnValue($creditMemo));
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => \Netresearch\OPS\Model\Status::REFUNDED,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $this->dbTransaction->expects($this->once())->method('addObject')->with($creditMemo);
        $this->dbTransaction->expects($this->once())->method('save');
        $this->model->handleResponse($response, $paymentInstance, false);
        $this->assertEquals(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT, $order->getState());
    }

    public function testCreditMemoStateOpenRefundSuccess()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['save', 'addStatusHistoryComment'],
            [],
            '',
            false,
            false
        );
        $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $order->expects($this->any())->method('addStatusHistoryComment')->will($this->returnSelf());
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock(
            '\Magento\Sales\Model\Order\Payment',
            ['save', 'lookupTransaction'],
            [],
            '',
            false,
            false
        );
        $payment->setOrder($order);
        $this->transactionRepository->expects($this->any())->method('getByTransactionId')
            ->will($this->returnValue(null));
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $this->getMock('\Magento\Sales\Model\Order\Invoice', ['save'], [], '', false, false);
        $invoice->setOrder($order);
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditMemo */
        $creditMemo = $this->getMock('\Magento\Sales\Model\Order\Creditmemo', ['save', 'load'], [], '', false, false);
        $creditMemo->setId('1234567/3');
        $creditMemo->setTransactionId(1234);
        $creditMemo->setOrder($order);
        $creditMemo->setState(\Magento\Sales\Model\Order\Creditmemo::STATE_OPEN);
        $creditMemo->setInvoice($invoice);
        $creditMemo->expects($this->any())->method('load')->will($this->returnSelf());
        $this->metadata->expects($this->any())->method('getNewInstance')->will($this->returnValue($creditMemo));
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => \Netresearch\OPS\Model\Status::REFUNDED,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $this->model->handleResponse($response, $paymentInstance, false);
        $this->assertEquals(\Magento\Sales\Model\Order\Creditmemo::STATE_REFUNDED, $creditMemo->getState());
    }

    public function testCreditMemoStateOpenRefundRefused()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['save', 'addStatusHistoryComment', 'canShip', 'canInvoice'],
            [],
            '',
            false,
            false
        );
        $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $order->expects($this->any())->method('addStatusHistoryComment')->will($this->returnSelf());
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock(
            '\Magento\Sales\Model\Order\Payment',
            ['save', 'cancelCreditmemo'],
            [],
            '',
            false,
            false
        );
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $this->getMock('\Magento\Sales\Model\Order\Invoice', ['save', 'load'], [], '', false, false);
        $invoice->setOrder($order);
        $invoice->expects($this->any())->method('load')->will($this->returnSelf());
        $this->invoiceFactory->expects($this->any())->method('create')->will($this->returnValue($invoice));
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditMemo */
        $creditMemo = $this->getMock(
            '\Magento\Sales\Model\Order\Creditmemo',
            ['save', 'load', 'cancel', 'getAllItems'],
            [],
            '',
            false,
            false
        );
        $creditMemo->setId('1234567/3');
        $creditMemo->setOrder($order);
        $creditMemo->setState(\Magento\Sales\Model\Order\Creditmemo::STATE_OPEN);
        $creditMemo->setInvoice($invoice);
        $creditMemo->expects($this->any())->method('load')->will($this->returnSelf());
        $creditMemo->expects($this->any())->method('save')->will($this->returnSelf());
        $creditMemo->expects($this->any())->method('getAllItems')->will($this->returnValue([]));
        $this->metadata->expects($this->any())->method('getNewInstance')->will($this->returnValue($creditMemo));
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => \Netresearch\OPS\Model\Status::REFUND_REFUSED,
            'payid'    => 1234567,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $payment->expects($this->any())->method('cancelCreditmemo')->with($creditMemo);
        $this->model->handleResponse($response, $paymentInstance, false);
        $this->assertEquals(\Magento\Sales\Model\Order::STATE_COMPLETE, $order->getState());
    }

    public function testCreditMemoRefundFinalState()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['save', 'addStatusHistoryComment'],
            [],
            '',
            false,
            false
        );
        $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $order->expects($this->any())->method('addStatusHistoryComment')->will($this->returnSelf());
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['save'], [], '', false, false);
        $payment->expects($this->any())
            ->method('save')
            ->will($this->returnSelf());
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditMemo */
        $creditMemo = $this->getMock('\Magento\Sales\Model\Order\Creditmemo', ['save', 'load'], [], '', false, false);
        $creditMemo->setId('1234567/3');
        $creditMemo->setOrder($order);
        $creditMemo->expects($this->any())->method('load')->will($this->returnSelf());
        $this->metadata->expects($this->any())->method('getNewInstance')->will($this->returnValue($creditMemo));
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => \Netresearch\OPS\Model\Status::REFUNDED,
            'payid'    => 12345679,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $this->model->handleResponse($response, $paymentInstance, false);
    }

    /**
     * @test
     */
    public function testCreditMemoRefundPendingState()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['save', 'addStatusHistoryComment'],
            [],
            '',
            false,
            false
        );
        $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $order->expects($this->any())->method('addStatusHistoryComment')->will($this->returnSelf());
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['save'], [], '', false, false);
        $payment->expects($this->any())
            ->method('save')
            ->will($this->returnSelf());
        $payment->setOrder($order);
        /** @var \Netresearch\OPS\Model\Payment\Cc $payment */
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', null, [], '', false, false);
        $paymentInstance->setInfoInstance($payment);
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditMemo */
        $creditMemo = $this->getMock('\Magento\Sales\Model\Order\Creditmemo', ['save', 'load'], [], '', false, false);
        $creditMemo->setId('1234567/3');
        $creditMemo->setOrder($order);
        $creditMemo->expects($this->any())->method('load')->will($this->returnSelf());
        $this->metadata->expects($this->any())->method('getNewInstance')->will($this->returnValue($creditMemo));
        $this->config->expects($this->any())->method('getAdditionalScoringKeys')->will($this->returnValue([]));
        $response = [
            'status'   => \Netresearch\OPS\Model\Status::REFUND_PENDING,
            'payid'    => 1234567534,
            'payidsub' => 3,
            'amount'   => 33.33
        ];
        $this->model->handleResponse($response, $paymentInstance, false);
    }
}

<?php

namespace Netresearch\OPS\Test\Unit\Helper;

class DirectLinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Helper\Directlink
     */
    private $helper;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Transaction
     */
    private $transaction;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $paymentHelper = $this->getMock('\Netresearch\OPS\Helper\Payment', [], [], '', false, false);
        $paymentHelper->expects($this->any())
            ->method('getBaseGrandTotalFromSalesObject')
            ->will($this->returnValue(184.90));

        $this->helper = $this->objectManager
            ->getObject(
                '\Netresearch\OPS\Helper\Directlink',
                ['oPSPaymentHelper' => $paymentHelper]
            );

        $this->transaction = $this->getMock('\Magento\Sales\Model\Order\Payment\Transaction', null, [], '', false, false);
        $this->transaction->setAdditionalInformation('arrInfo', serialize([
            'amount' => '184.90'
        ]));
        $this->transaction->setIsClosed(0);

        $this->order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $this->order->setGrandTotal('184.90');
        $this->order->setBaseGrandTotal('184.90');
    }

    public function testDeleteActions()
    {
        $this->assertFalse(
            $this->helper
                ->isValidOpsRequest(
                    $this->transaction,
                    $this->order,
                    ['STATUS' => \Netresearch\OPS\Model\Status::PAYMENT_DELETED]
                )
        );
        $this->assertFalse($this->helper->isValidOpsRequest(
            $this->transaction,
            $this->order,
            ['STATUS' => \Netresearch\OPS\Model\Status::PAYMENT_DELETION_PENDING]
        ));
        $this->assertFalse($this->helper->isValidOpsRequest(
            $this->transaction,
            $this->order,
            ['STATUS' => \Netresearch\OPS\Model\Status::PAYMENT_DELETION_UNCERTAIN]
        ));
        $this->assertFalse($this->helper->isValidOpsRequest(
            $this->transaction,
            $this->order,
            ['STATUS' => \Netresearch\OPS\Model\Status::PAYMENT_DELETION_REFUSED]
        ));
        $this->assertFalse($this->helper->isValidOpsRequest(
            $this->transaction,
            $this->order,
            ['STATUS' => \Netresearch\OPS\Model\Status::PAYMENT_DELETION_OK]
        ));
        $this->assertFalse($this->helper->isValidOpsRequest(
            $this->transaction,
            $this->order,
            ['STATUS' => \Netresearch\OPS\Model\Status::DELETION_HANDLED_BY_MERCHANT]
        ));
    }

    public function testRefundActions()
    {
        $opsRequest = [
            'STATUS' => \Netresearch\OPS\Model\Status::REFUNDED,
            'amount' => '184.90'
        ];
        $this->assertTrue($this->helper->isValidOpsRequest($this->transaction, $this->order, $opsRequest), 'Refund should be possible with open transactions');
        $opsRequest['amount'] = '14.90';
        $this->assertFalse($this->helper->isValidOpsRequest($this->transaction, $this->order, $opsRequest), 'Refund should NOT be possible because of differing amount');
    }

    public function testCancelActions()
    {
        $opsRequest = [
            'STATUS' => \Netresearch\OPS\Model\Status::AUTHORIZED_AND_CANCELLED,
            'amount' => '184.90'
        ];
        $this->assertTrue($this->helper->isValidOpsRequest($this->transaction, $this->order, $opsRequest), 'Cancel should be possible with open transactions');
        $opsRequest['amount'] = '14.90';
        $this->assertFalse($this->helper->isValidOpsRequest($this->transaction, $this->order, $opsRequest), 'Cancel should NOT be possible because of differing amount');
    }

    public function testCaptureActions()
    {
        $opsRequest = [
            'STATUS' => \Netresearch\OPS\Model\Status::PAYMENT_REQUESTED,
            'amount' => '184.90'
        ];
        $opsRequest['amount'] = '14.90';
        $this->assertFalse($this->helper->isValidOpsRequest($this->transaction, $this->order, $opsRequest), 'Capture should NOT be possible because of differing amount');
    }

    public function testCleanupParameters()
    {
        $oPSHelper = $this->getMock('Netresearch\OPS\Helper\Data', [], [], '', false, false);
        $oPSHelper->expects($this->atLeastOnce())
            ->method('log')
            ->with($this->isType(\PHPUnit_Framework_Constraint_IsType::TYPE_STRING));
        /** @var \Netresearch\OPS\Helper\DirectLink $helper */
        $helper = $this->objectManager->getObject('Netresearch\OPS\Helper\DirectLink', ['oPSHelper' => $oPSHelper]);
        $expected = 123.45;
        $result = $helper->formatAmount('123.45');
        $this->assertEquals($expected, $result);

        $result = $helper->formatAmount('\'123.45\'');
        $this->assertEquals($expected, $result);

        $result = $helper->formatAmount('"123.45"');
        $this->assertEquals($expected, $result);

        $expected = $helper->formatAmount(0.3);
        $result = $helper->formatAmount(0.1 + 0.2);
        $this->assertEquals($expected . '', $result . '');
        $this->assertEquals((float) $expected, (float) $result);
    }

    public function testProcessFeedback()
    {
        $paymentInstance = $this->getMock('Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $payment->expects($this->once())
            ->method('save')
            ->will($this->returnValue(null));
        $payment->expects($this->once())
                ->method('getMethodInstance')
                ->will($this->returnValue($paymentInstance));

        $this->order->expects($this->exactly(2))
                    ->method('getPayment')
                    ->will($this->returnValue($payment));

        $params = ['STATUS' => \Netresearch\OPS\Model\Status::PAYMENT_REQUESTED];

        $responseHandler = $this->getMock('\Netresearch\OPS\Model\Response\Handler', [], [], '', false, false);
        $responseHandler->expects($this->once())
                        ->method('processResponse')
                        ->with($params, $paymentInstance)
                        ->will($this->returnValue(null));
        $this->helper = $this->objectManager
            ->getObject(
                '\Netresearch\OPS\Helper\Directlink',
                ['responseHandler' => $responseHandler]
            );

        $this->helper->processFeedback($this->order, $params);
    }

    protected function mockOrderConfig()
    {
        $configMock = $this->getModelMock('sales/order_config', ['getDefaultStatus']);
        $configMock->expects($this->any())
            ->method('getDefaultStatus')
            ->will($this->returnArgument(0));
        $this->replaceByMock('singleton', 'sales/order_config', $configMock);
    }
}

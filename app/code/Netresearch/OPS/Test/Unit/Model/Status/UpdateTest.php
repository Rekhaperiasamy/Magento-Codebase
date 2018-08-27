<?php
namespace Netresearch\OPS\Test\Unit\Model\Status;

use Netresearch\OPS\Model\Payment\KwixoApresReception;
use Netresearch\OPS\Test\Unit\Model\Payment\KwixoApresReceptionTest;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UpdateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Status\Update
     */
    private $model;

    /**
     * @var \Netresearch\OPS\Helper\Order
     */
    private $orderHelper;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    /**
     * @var \Netresearch\OPS\Model\Api\Directlink
     */
    private $apiDirectlink;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->orderHelper   = $this->getMock('\Netresearch\OPS\Helper\Order', [], [], '', false, false);
        $this->config        = $this->getMock('\Netresearch\OPS\Model\Config', [], [], '', false, false);
        $this->apiDirectlink = $this->getMock('\Netresearch\OPS\Model\Api\Directlink', [], [], '', false, false);
        $this->model         = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Status\Update',
            [
                                                                   'oPSOrderHelper'   => $this->orderHelper,
                                                                   'oPSApiDirectlink' => $this->apiDirectlink
                                                               ]
        );
        $this->model->setOpsConfig($this->config);
    }

//    protected function mockSessions()
//    {
//        $sessionMock = $this->getModelMock('admin/session', array());
//        $sessionMock->disableOriginalConstructor();
//        $this->replaceByMock('singleton', 'admin/session', $sessionMock);
//    }
    public function testNoUpdateForNonOpsPayments()
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['getMethodInstance'], [], '', false, false);
        $payment->setMethod('checkmo');
        $payment->setMethodInstance(null);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', null, [], '', false, false);
        $order->setPayment($payment);
        $this->model->updateStatusFor($order);
        $this->assertNull($this->model->getOrder());
    }

    public function testBuildParamsForOpsOrderWithOrderId()
    {
        /** @var \Netresearch\OPS\Model\Payment\Cc $paymentInstance */
        $paymentInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\Cc',
            ['hasBrandAliasInterfaceSupport'],
            [],
            '',
            false,
            false
        );
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', null, [], '', false, false);
        $payment->setMethod('checkmo');
        $payment->setMethodInstance($paymentInstance);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', null, [], '', false, false);
        $order->setPayment($payment);
        $paymentInstance->expects($this->any())->method('hasBrandAliasInterfaceSupport')->will($this->returnValue(true));
        $this->orderHelper->expects($this->once())
                          ->method('getOpsOrderId')
                          ->with($order, true)
                          ->will($this->returnValue('#1000000'));
        $this->config->expects($this->any())->method('getDirectLinkMaintenanceApiPath')->will($this->returnValue('url'));
        $this->apiDirectlink->expects($this->any())->method('performRequest')->will($this->returnValue([]));
        $this->model->setOrder($order);
        $this->model->updateStatusFor($order);
        $requestParams = $this->model->getRequestParams();
        $this->assertArrayHasKey('ORDERID', $requestParams);
        $this->assertEquals('#1000000', $requestParams['ORDERID']);
    }

    public function testBuildParamsForOpsOrderWithQuoteId()
    {
        /** @var \Netresearch\OPS\Model\Payment\Cc $paymentInstance */
        $paymentInstance = $this->getMock(
            KwixoApresReception::class,
            [],
            [],
            '',
            false,
            false
        );
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', null, [], '', false, false);
        $payment->setMethod('checkmo');
        $payment->setMethodInstance($paymentInstance);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', null, [], '', false, false);
        $order->setPayment($payment);
        $this->orderHelper->expects($this->once())
                          ->method('getOpsOrderId')
                          ->with($order, false)
                          ->will($this->returnValue('100'));
        $this->config->expects($this->any())->method('getDirectLinkMaintenanceApiPath')->will($this->returnValue('url'));
        $this->apiDirectlink->expects($this->any())->method('performRequest')->will($this->returnValue([]));
        $this->model->setOrder($order);
        $this->model->updateStatusFor($order);
        $requestParams = $this->model->getRequestParams();
        $this->assertArrayHasKey('ORDERID', $requestParams);
        $this->assertEquals('100', $requestParams['ORDERID']);
    }

    public function testBuildParamsForOpsOrderWithPayId()
    {
        /** @var \Netresearch\OPS\Model\Payment\Cc $paymentInstance */
        $paymentInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\Cc',
            ['hasBrandAliasInterfaceSupport'],
            [],
            '',
            false,
            false
        );
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', null, [], '', false, false);
        $payment->setMethod('checkmo');
        $payment->setMethodInstance($paymentInstance);
        $payment->setAdditionalInformation('paymentId', 4711);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', null, [], '', false, false);
        $order->setPayment($payment);
        $this->config->expects($this->any())->method('getDirectLinkMaintenanceApiPath')->will($this->returnValue('url'));
        $this->apiDirectlink->expects($this->any())->method('performRequest')->will($this->returnValue([]));
        $this->model->setOrder($order);
        $this->model->updateStatusFor($order);
        $requestParams = $this->model->getRequestParams();
        $this->assertArrayHasKey('PAYID', $requestParams);
        $this->assertEquals(4711, $requestParams['PAYID']);
    }

    public function testPerformRequestWithPayId()
    {
        /** @var \Netresearch\OPS\Model\Payment\Cc $paymentInstance */
        $paymentInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\Cc',
            ['hasBrandAliasInterfaceSupport'],
            [],
            '',
            false,
            false
        );
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', null, [], '', false, false);
        $payment->setMethod('checkmo');
        $payment->setMethodInstance($paymentInstance);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', null, [], '', false, false);
        $order->setPayment($payment);
        $paymentInstance->expects($this->any())->method('hasBrandAliasInterfaceSupport')->will($this->returnValue(true));
        $this->orderHelper->expects($this->once())
                          ->method('getOpsOrderId')
                          ->with($order, true)
                          ->will($this->returnValue('100'));
        $this->config->expects($this->any())->method('getDirectLinkMaintenanceApiPath')->will($this->returnValue('url'));
        $this->apiDirectlink->expects($this->any())->method('performRequest')->will($this->returnValue(['STATUS' => 5]));
        $this->model->updateStatusFor($order);
        $opsResponse = $this->model->getOpsResponse();
        $this->assertArrayHasKey('STATUS', $opsResponse);
        $this->assertEquals(5, $opsResponse['STATUS']);
    }


    public function testPerformRequestWithPayIdAndPayIdSub()
    {
        /** @var \Netresearch\OPS\Model\Payment\Cc $paymentInstance */
        $paymentInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\Cc',
            ['hasBrandAliasInterfaceSupport'],
            [],
            '',
            false,
            false
        );
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', null, [], '', false, false);
        $payment->setMethod('checkmo');
        $payment->setMethodInstance($paymentInstance);
        $payment->setAdditionalInformation('paymentId', 4711);
        $payment->setAdditionalInformation('status', 91);
        $payment->setLastTransId('4711/1');
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', null, [], '', false, false);
        $order->setPayment($payment);
        $this->config->expects($this->any())->method('getDirectLinkMaintenanceApiPath')->will($this->returnValue('url'));
        $this->apiDirectlink->expects($this->any())->method('performRequest')->with(
            [
                                                                                        'PAYID'    => 4711,
                                                                                        'PAYIDSUB' => 1
                                                                                    ],
            'url',
            null
        )->will($this->returnValue(['STATUS' => 5]));
        $this->model->updateStatusFor($order);
        $opsResponse = $this->model->getOpsResponse();
        $this->assertArrayHasKey('STATUS', $opsResponse);
        $this->assertEquals(5, $opsResponse['STATUS']);
    }

    public function testPerformRequestWithPayIdAndPayIdSubForRefund()
    {
        /** @var \Netresearch\OPS\Model\Payment\Cc $paymentInstance */
        $paymentInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\Cc',
            ['hasBrandAliasInterfaceSupport'],
            [],
            '',
            false,
            false
        );
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', null, [], '', false, false);
        $payment->setMethod('checkmo');
        $payment->setMethodInstance($paymentInstance);
        $payment->setAdditionalInformation('paymentId', 4711);
        $payment->setAdditionalInformation('status', 81);
        $payment->setLastTransId('4711/1');
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', null, [], '', false, false);
        $order->setPayment($payment);
        $this->config->expects($this->any())->method('getDirectLinkMaintenanceApiPath')->will($this->returnValue('url'));
        $this->apiDirectlink->expects($this->any())->method('performRequest')->with(
            [
                                                                                        'PAYID'    => 4711,
                                                                                        'PAYIDSUB' => 1
                                                                                    ],
            'url',
            null
        )->will($this->returnValue(['STATUS' => 8, 'AMOUNT' => 1]));
        $this->model->updateStatusFor($order);
        $opsResponse = $this->model->getOpsResponse();
        $this->assertArrayHasKey('STATUS', $opsResponse);
        $this->assertEquals(8, $opsResponse['STATUS']);
        $this->assertArrayHasKey('AMOUNT', $opsResponse);
        $this->assertArrayHasKey('amount', $opsResponse);
        $this->assertEquals(1, $opsResponse['AMOUNT']);
        $this->assertEquals($opsResponse['amount'], $opsResponse['AMOUNT']);
    }

    public function testUpdatePaymentStatusWithoutStatusChange()
    {
        /** @var \Netresearch\OPS\Model\Payment\Cc $paymentInstance */
        $paymentInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\Cc',
            ['hasBrandAliasInterfaceSupport'],
            [],
            '',
            false,
            false
        );
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', null, [], '', false, false);
        $payment->setMethod('checkmo');
        $payment->setMethodInstance($paymentInstance);
        $payment->setAdditionalInformation('paymentId', 4711);
        $payment->setAdditionalInformation('status', 8);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', null, [], '', false, false);
        $order->setPayment($payment);
        $this->config->expects($this->any())->method('getDirectLinkMaintenanceApiPath')->will($this->returnValue('url'));
        $this->apiDirectlink->expects($this->any())
                            ->method('performRequest')
                            ->with(['PAYID' => 4711], 'url', null)
                            ->will($this->returnValue(['STATUS' => 8]));
        $this->model->updateStatusFor($order);
        $opsResponse = $this->model->getOpsResponse();
        $this->assertArrayHasKey('STATUS', $opsResponse);
        $this->assertEquals(8, $opsResponse['STATUS']);
    }

    public function testUpdatePaymentStatusWithStatusChange()
    {
        /** @var \Netresearch\OPS\Model\Payment\Cc $paymentInstance */
        $paymentInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\Cc',
            ['hasBrandAliasInterfaceSupport'],
            [],
            '',
            false,
            false
        );
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', null, [], '', false, false);
        $payment->setMethod('checkmo');
        $payment->setMethodInstance($paymentInstance);
        $payment->setAdditionalInformation('paymentId', 4711);
        $payment->setAdditionalInformation('status', 5);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', null, [], '', false, false);
        $order->setPayment($payment);
        $this->config->expects($this->any())->method('getDirectLinkMaintenanceApiPath')->will($this->returnValue('url'));
        $this->apiDirectlink->expects($this->any())
                            ->method('performRequest')
                            ->with(['PAYID' => 4711], 'url', null)
                            ->will($this->returnValue(['STATUS' => 91]));
        $this->model->updateStatusFor($order);
        $opsResponse = $this->model->getOpsResponse();
        $this->assertArrayHasKey('STATUS', $opsResponse);
        $this->assertEquals(91, $opsResponse['STATUS']);
    }

    public function testUpdatePaymentStatusWithStatusChangeOnInitialRequest()
    {
        /** @var \Netresearch\OPS\Model\Payment\Cc $paymentInstance */
        $paymentInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\Cc',
            ['hasBrandAliasInterfaceSupport'],
            [],
            '',
            false,
            false
        );
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', null, [], '', false, false);
        $payment->setMethod('checkmo');
        $payment->setMethodInstance($paymentInstance);
        $payment->setAdditionalInformation('paymentId', 4711);
        $payment->setAdditionalInformation('status', 5);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', null, [], '', false, false);
        $order->setPayment($payment);
        $this->config->expects($this->any())->method('getDirectLinkMaintenanceApiPath')->will($this->returnValue('url'));
        $this->apiDirectlink->expects($this->any())
                            ->method('performRequest')
                            ->with(['PAYID' => 4711], 'url', null)
                            ->will($this->returnValue(['STATUS' => 91]));
        $this->model->updateStatusFor($order);
        $opsResponse = $this->model->getOpsResponse();
        $this->assertArrayHasKey('STATUS', $opsResponse);
        $this->assertEquals(91, $opsResponse['STATUS']);
    }
}

<?php
namespace Netresearch\OPS\Test\Unit\Model\Payment;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PaymentAbstractCaptureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\PaymentAbstract
     */
    private $model;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Info
     */
    private $paymentInfo;

    /**
     * @var \Netresearch\OPS\Model\Api\Directlink
     */
    private $apiDirectlink;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    private $stringUtils;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Netresearch\OPS\Helper\Payment\Request
     */
    private $paymentRequestHelper;

    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    private $paymentHelper;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Netresearch\OPS\Helper\Directlink
     */
    private $directlinkHelper;

    /**
     * @var \Netresearch\OPS\Helper\Order
     */
    private $orderHelper;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $salesOrderFactory;

    /**
     * @var \Netresearch\OPS\Model\Backend\Operation\Parameter
     */
    private $backendOperationParameter;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Netresearch\OPS\Model\Status\Update
     */
    private $statusUpdate;

    /**
     * @var \Netresearch\OPS\Model\Response\Handler
     */
    private $responseHandler;

    /**
     * @var \Magento\Framework\Message\Manager
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager        = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->paymentInfo          = $this->getMock(
            '\Magento\Sales\Model\Order\Payment\Info',
            ['save'],
            [],
            '',
            false,
            false
        );
        $this->apiDirectlink        = $this->getMock('\Netresearch\OPS\Model\Api\Directlink', [], [], '', false, false);
        $this->stringUtils          = $this->objectManager->getObject('\Magento\Framework\Stdlib\StringUtils');
        $this->checkoutSession      = $this->getMock('\Magento\Checkout\Model\Session', [], [], '', false, false);
        $this->customerSession      = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false, false);
        $this->paymentRequestHelper = $this->getMock(
            '\Netresearch\OPS\Helper\Payment\Request',
            ['getMandatoryRequestFields', 'extractShipToParameters'],
            [],
            '',
            false,
            false
        );
        $this->config               = $this->getMock('\Netresearch\OPS\Model\Config', [], [], '', false, false);
        $this->salesOrderFactory    = $this->getMock('\Magento\Sales\Model\OrderFactory', [], [], '', false, false);
        $this->paymentHelper        = $this->getMock('\Netresearch\OPS\Helper\Payment', [], [], '', false, false);
        $this->dataHelper           = $this->getMock('\Netresearch\OPS\Helper\Data', [], [], '', false, false);
        $this->directlinkHelper     = $this->getMock('\Netresearch\OPS\Helper\Directlink', [], [], '', false, false);
        $this->orderHelper          = $this->getMock('\Netresearch\OPS\Helper\Order', [], [], '', false, false);
        $this->storeManager         = $this->getMock('\Magento\Store\Model\StoreManager', [], [], '', false, false);
        $this->messageManager       = $this->getMock('\Magento\Framework\Message\Manager', [], [], '', false, false);
        $this->statusUpdate         = $this->getMock('\Netresearch\OPS\Model\Status\Update', [], [], '', false, false);
        $statusUpdateFactory        = $this->getMock(
            '\Netresearch\OPS\Model\Status\UpdateFactory',
            [],
            [],
            '',
            false,
            false
        );
        $statusUpdateFactory->expects($this->any())->method('create')->will($this->returnValue($this->statusUpdate));
        $this->backendOperationParameter  = $this->getMock(
            '\Netresearch\OPS\Model\Backend\Operation\Parameter',
            [],
            [],
            '',
            false,
            false
        );
        $backendOperationParameterFactory = $this->getMock(
            '\Netresearch\OPS\Model\Backend\Operation\ParameterFactory',
            [],
            [],
            '',
            false,
            false
        );
        $backendOperationParameterFactory->expects($this->any())
                                         ->method('create')
                                         ->will($this->returnValue($this->backendOperationParameter));
        $this->responseHandler = $this->getMock('\Netresearch\OPS\Model\Response\Handler', [], [], '', false, false);
        $this->registry        = new \Magento\Framework\Registry();
        $this->model           = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Payment\PaymentAbstract',
            [
                                                                     'stringUtils'                         => $this->stringUtils,
                                                                     'oPSApiDirectlink'                    => $this->apiDirectlink,
                                                                     'checkoutSession'                     => $this->checkoutSession,
                                                                     'oPSPaymentRequestHelper'             => $this->paymentRequestHelper,
                                                                     'oPSConfig'                           => $this->config,
                                                                     'oPSPaymentHelper'                    => $this->paymentHelper,
                                                                     'oPSHelper'                           => $this->dataHelper,
                                                                     'salesOrderFactory'                   => $this->salesOrderFactory,
                                                                     'storeManager'                        => $this->storeManager,
                                                                     'oPSDirectlinkHelper'                 => $this->directlinkHelper,
                                                                     'messageManager'                      => $this->messageManager,
                                                                     'oPSStatusUpdateFactory'              => $statusUpdateFactory,
                                                                     'oPSResponseHandler'                  => $this->responseHandler,
                                                                     'oPSOrderHelper'                      => $this->orderHelper,
                                                                     'customerSession'                     => $this->customerSession,
                                                                     'registry'                            => $this->registry,
                                                                     'oPSBackendOperationParameterFactory' => $backendOperationParameterFactory
                                                                 ]
        );
        $this->model->setInfoInstance($this->paymentInfo);
    }

    public function testCaptureWithAutoCapture()
    {
        $this->registry->register('ops_auto_capture', true);
        $this->model->capture($this->paymentInfo, null);
    }

    public function testCaptureWithPreviousCaptureRequestLeadToRedirect()
    {
        $order   = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $payment = $this->preparePayment($order);
        $amount  = 1;
        $this->directlinkHelper->expects($this->once())->method('checkExistingTransact')->will($this->returnValue(true));
        $this->apiDirectlink->expects($this->never())->method('performRequest')->will($this->returnSelf());
        $this->assertEquals($this->model, $this->model->capture($payment, $amount));
    }

    public function testCaptureWithPreviousVoidRequestLeadToRedirect()
    {
        $order   = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $payment = $this->preparePayment($order);
        $amount  = 1;
        $this->directlinkHelper->expects($this->any())
                               ->method('checkExistingTransact')
                               ->will($this->onConsecutiveCalls(false, true));
        $this->apiDirectlink->expects($this->never())->method('performRequest')->will($this->returnSelf());
        $this->assertEquals($this->model, $this->model->capture($payment, $amount));
    }

    public function testCaptureWillPerformRequest()
    {
        $directLinkGatewayPath = 'dlgp';
        $storeId               = 1;
        $order                 = $this->getMock('\Magento\Sales\Model\Order', ['save'], [], '', false, false);
        $order->setStoreId($storeId);
        $payment = $this->preparePayment($order);
        $payment->setAdditionalInformation('paymentId', 'payID');
        $amount        = 10;
        $requestParams = $this->getCaptureRequestParams($amount, $payment);
        $response      = ['STATUS' => \Netresearch\OPS\Model\Status::PAYMENT_PROCESSED_BY_MERCHANT];
        $this->backendOperationParameter->expects($this->once())
                                        ->method('getParameterFor')
                                        ->will($this->returnValue($requestParams));
        $this->config->expects($this->once())
                     ->method('getDirectLinkGatewayPath')
                     ->will($this->returnValue($directLinkGatewayPath));
        $this->apiDirectlink->expects($this->once())
                            ->method('performRequest')
                            ->with($requestParams, $directLinkGatewayPath, $storeId)
                            ->will($this->returnValue($response));
        $this->responseHandler->expects($this->once())
                              ->method('processResponse')
                              ->with($response, $this->model, false)
                              ->will($this->returnSelf());
        $this->assertEquals($this->model, $this->model->capture($payment, $amount));
    }


    public function testCaptureWillPerformRequestWithPaymentProcessing()
    {
        $directLinkGatewayPath = 'dlgp';
        $storeId               = 1;
        $order                 = $this->getMock('\Magento\Sales\Model\Order', ['save'], [], '', false, false);
        $order->setStoreId($storeId);
        $payment = $this->preparePayment($order);
        $payment->setAdditionalInformation('paymentId', 'payID');
        $amount        = 10;
        $requestParams = $this->getCaptureRequestParams($amount, $payment);
        $response      = ['STATUS' => \Netresearch\OPS\Model\Status::PAYMENT_PROCESSING];
        $this->backendOperationParameter->expects($this->once())
                                        ->method('getParameterFor')
                                        ->will($this->returnValue($requestParams));
        $this->config->expects($this->once())
                     ->method('getDirectLinkGatewayPath')
                     ->will($this->returnValue($directLinkGatewayPath));
        $this->apiDirectlink->expects($this->once())
                            ->method('performRequest')
                            ->with($requestParams, $directLinkGatewayPath, $storeId)
                            ->will($this->returnValue($response));
        $this->responseHandler->expects($this->once())
                              ->method('processResponse')
                              ->with($response, $this->model, false)
                              ->will($this->returnSelf());
        $this->assertEquals($this->model, $this->model->capture($payment, $amount));
    }

    /**
     * @expectedException \Exception
     */
    public function testCaptureWillPerformRequestWithInvalidResponseLeadToException()
    {
        $directLinkGatewayPath = 'dlgp';
        $storeId               = 1;
        $order                 = $this->getMock('\Magento\Sales\Model\Order', ['save'], [], '', false, false);
        $order->setStoreId($storeId);
        $payment = $this->preparePayment($order);
        $payment->setAdditionalInformation('paymentId', 'payID');
        $amount        = 10;
        $requestParams = $this->getCaptureRequestParams($amount, $payment);
        $response      = ['STATUS' => 320];
        $this->backendOperationParameter->expects($this->once())
                                        ->method('getParameterFor')
                                        ->will($this->returnValue($requestParams));
        $this->config->expects($this->once())
                     ->method('getDirectLinkGatewayPath')
                     ->will($this->returnValue($directLinkGatewayPath));
        $this->apiDirectlink->expects($this->once())
                            ->method('performRequest')
                            ->with($requestParams, $directLinkGatewayPath, $storeId)
                            ->will($this->returnValue($response));
        $this->responseHandler->expects($this->once())
                              ->method('processResponse')
                              ->with($response, $this->model, false)
                              ->will($this->throwException(new \Exception()));
        $this->statusUpdate->expects($this->once())->method('updateStatusFor')->with($order)->will($this->returnSelf());
        $this->dataHelper->expects($this->once())->method('log')->will($this->returnSelf());
        $this->model->capture($payment, $amount);
    }

    /**
     * @expectedException \Exception
     */
    public function testCaptureWillPerformRequestWithExceptionWillThrowException()
    {
        $directLinkGatewayPath = 'dlgp';
        $storeId               = 1;
        $order                 = $this->getMock('\Magento\Sales\Model\Order', ['save'], [], '', false, false);
        $order->setStoreId($storeId);
        $payment = $this->preparePayment($order);
        $payment->setAdditionalInformation('paymentId', 'payID');
        $amount        = 10;
        $requestParams = $this->getCaptureRequestParams($amount, $payment);
        $this->backendOperationParameter->expects($this->once())
                                        ->method('getParameterFor')
                                        ->will($this->returnValue($requestParams));
        $this->config->expects($this->once())
                     ->method('getDirectLinkGatewayPath')
                     ->will($this->returnValue($directLinkGatewayPath));
        $this->apiDirectlink->expects($this->once())
                            ->method('performRequest')
                            ->with($requestParams, $directLinkGatewayPath, $storeId)
                            ->will($this->throwException(new \Exception()));
        $this->statusUpdate->expects($this->once())->method('updateStatusFor')->with($order)->will($this->returnSelf());
        $this->dataHelper->expects($this->once())->method('log')->will($this->returnSelf());
        $this->model->capture($payment, $amount);
    }

    /**
     * @return \Magento\Sales\Model\Order\Payment
     */
    protected function preparePayment($order, $method = 'ops_iDeal')
    {
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['save'], [], '', false, false);
        $payment->setOrder($order);
        $payment->setMethod($method);
        $payment->setAdditionalInformation('paymentId', 'payID');
        $order->setPayment($payment);

        return $payment;
    }

    /**
     * @param $amount
     * @param $payment
     *
     * @return array
     */
    protected function getCaptureRequestParams($amount, $payment)
    {
        $requestParams = [
            'AMOUNT'    => $amount * 100,
            'PAYID'     => $payment->getAdditionalInformation('paymentId'),
            'OPERATION' => \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_PARTIAL,
            'CURRENCY'  => 'EUR'
        ];

        return $requestParams;
    }
}

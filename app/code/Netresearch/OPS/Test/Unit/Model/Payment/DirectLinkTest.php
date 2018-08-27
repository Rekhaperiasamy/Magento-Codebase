<?php

namespace Netresearch\OPS\Test\Unit\Model\Payment;

use Magento\Payment\Model\Method\AbstractMethod;
use Netresearch\OPS\Model\Payment\DirectDebit;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DirectLinkTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\DirectDebit
     */
    private $model;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Info
     */
    private $paymentInfo;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \Netresearch\OPS\Helper\Quote
     */
    private $quoteHelper;

    /**
     * @var \Netresearch\OPS\Model\Validator\Payment\DirectDebit
     */
    private $validator;

    /**
     * @var \Netresearch\OPS\Model\Validator\Payment\DirectDebitFactory
     */
    private $validatorFactory;

    /**
     * @var \Netresearch\OPS\Model\Validator\Parameter\FactoryFactory
     */
    private $validatorParameterFactoryFactory;

    /**
     * @var \Netresearch\OPS\Model\Validator\Parameter\Factory
     */
    private $validatorParameterFactory;

    /**
     * @var \Netresearch\OPS\Model\Validator\Parameter\Validator
     */
    private $validatorParameter;

    /**
     * @var \Netresearch\OPS\Helper\DirectDebit
     */
    private $directDebitHelper;

    /**
     * @var \Netresearch\OPS\Helper\Directlink
     */
    private $directLinkHelper;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    private $paymentHelper;

    /**
     * @var \Netresearch\OPS\Model\Response\Handler
     */
    private $responseHandler;

    /**
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    private $checkoutTypeOnepage;

    /**
     * @var \Netresearch\OPS\Model\Payment\DirectLink[]
     */
    protected $testObjects = [];

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->paymentInfo = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', [], [], '', false, false);
        $this->checkoutSession = $this->getMock('\Magento\Checkout\Model\Session', [], [], '', false, false);
        $this->config = $this->getMock('\Netresearch\OPS\Model\Config', [], [], '', false, false);
        $this->order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $this->quote = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false, false);
        $this->quote->expects($this->any())->method('getPayment')->will($this->returnValue($this->paymentInfo));
        $this->quoteHelper = $this->getMock('\Netresearch\OPS\Helper\Quote', [], [], '', false, false);
        $this->quoteHelper->expects($this->any())->method('getQuote')->will($this->returnValue($this->quote));
        $this->validator = $this->getMock(
            '\Netresearch\OPS\Model\Validator\Payment\DirectDebit',
            [],
            [],
            '',
            false,
            false
        );
        $this->validatorFactory = $this->getMock(
            '\Netresearch\OPS\Model\Validator\Payment\DirectDebitFactory',
            [],
            [],
            '',
            false,
            false
        );
        $this->validatorFactory->expects($this->any())->method('create')->will($this->returnValue($this->validator));
        $this->validatorParameter = $this->getMock(
            '\Netresearch\OPS\Model\Validator\Parameter\Validator',
            [],
            [],
            '',
            false,
            false
        );
        $this->validatorParameterFactory = $this->getMock(
            '\Netresearch\OPS\Model\Validator\Parameter\Factory',
            [],
            [],
            '',
            false,
            false
        );
        $this->validatorParameterFactory->expects($this->any())
            ->method('getValidatorFor')
            ->will($this->returnValue($this->validatorParameter));
        $this->validatorParameterFactoryFactory
            = $this->getMock('\Netresearch\OPS\Model\Validator\Parameter\FactoryFactory', [], [], '', false, false);
        $this->validatorParameterFactoryFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->validatorParameterFactory));
        $this->directDebitHelper = $this->getMock('\Netresearch\OPS\Helper\DirectDebit', [], [], '', false, false);
        $this->dataHelper = $this->getMock('\Netresearch\OPS\Helper\Data', [], [], '', false, false);
        $this->directLinkHelper = $this->getMock('\Netresearch\OPS\Helper\Directlink', [], [], '', false, false);
        $this->responseHandler = $this->getMock(
            '\Netresearch\OPS\Model\Response\Handler',
            [],
            [],
            '',
            false,
            false
        );
        $this->paymentHelper = $this->getMock('\Netresearch\OPS\Helper\Payment', [], [], '', false, false);
        $this->checkoutTypeOnepage = $this->getMock('\Magento\Checkout\Model\Type\Onepage', [], [], '', false, false);
        $this->model = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Payment\DirectDebit',
            [
                'checkoutSession' => $this->checkoutSession,
                'oPSConfig' => $this->config,
                'oPSQuoteHelper' => $this->quoteHelper,
                'oPSValidatorPaymentDirectDebitFactory' => $this->validatorFactory,
                'oPSValidatorParameterFactoryFactory' => $this->validatorParameterFactoryFactory,
                'oPSDirectDebitHelper' => $this->directDebitHelper,
                'oPSDirectlinkHelper' => $this->directLinkHelper,
                'oPSResponseHandler' => $this->responseHandler,
                'oPSPaymentHelper' => $this->paymentHelper,
                'checkoutTypeOnepage' => $this->checkoutTypeOnepage
            ]
        );
        $this->model->setInfoInstance($this->paymentInfo);
        $this->testObjects[] = $this->model;
    }

    public function testGetConfigPaymentActionReturnsMageAuthorizeWithOrderIdAsMerchRef()
    {
        $this->config->expects($this->any())
            ->method('getInlineOrderReference')
            ->will($this->returnValue(\Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_ORDER_ID));
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $this->config->expects($this->any())->method('getInlinePaymentCcTypes')->will($this->returnValue(['VISA']));
        foreach ($this->testObjects as $testObject) {
            $expected = $testObject instanceof DirectDebit ?
                AbstractMethod::ACTION_AUTHORIZE_CAPTURE : AbstractMethod::ACTION_AUTHORIZE;
            $this->assertEquals(
                $expected,
                $testObject->getConfigPaymentAction()
            );
        }
    }

    public function testGetConfigPaymentActionReturnsAuthorizeStringWithQuoteIdAsMerchRef()
    {
        $this->config->expects($this->any())
            ->method('getInlineOrderReference')
            ->will($this->returnValue(\Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_QUOTE_ID));
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        foreach ($this->testObjects as $testObject) {
            $expected = $testObject instanceof DirectDebit ?
                AbstractMethod::ACTION_AUTHORIZE_CAPTURE : AbstractMethod::ACTION_AUTHORIZE;
            $this->assertEquals(
                $expected,
                $testObject->getConfigPaymentAction()
            );
        }
    }

    public function testGetConfigPaymentActionReturnsMageAuthorizeCaptureWithOrderIdAsMerchRef()
    {
        $this->config->expects($this->any())
            ->method('getInlineOrderReference')
            ->will($this->returnValue(\Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_ORDER_ID));
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE));
        $this->config->expects($this->any())->method('getInlinePaymentCcTypes')->will($this->returnValue(['VISA']));
        foreach ($this->testObjects as $testObject) {
            $this->assertEquals(
                \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE,
                $testObject->getConfigPaymentAction()
            );
        }
    }

    public function testGetConfigPaymentActionReturnsAuthorizeCaptureStringForDirectSaleWithQuoteIdAsMerchRef()
    {
        $this->config->expects($this->any())
            ->method('getInlineOrderReference')
            ->will($this->returnValue(\Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_QUOTE_ID));
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE));
        foreach ($this->testObjects as $testObject) {
            $this->assertEquals(
                \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE,
                $testObject->getConfigPaymentAction()
            );
        }
    }

    public function testIsInitializeNeededReturnsFalse()
    {
        $this->config->expects($this->any())
            ->method('getInlineOrderReference')
            ->will($this->returnValue(\Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_ORDER_ID));
        foreach ($this->testObjects as $testObject) {
            $this->assertFalse($testObject->isInitializeNeeded());
        }
    }

    public function testAuthorize()
    {
        $this->paymentInfo->expects($this->any())
            ->method('getAdditionalInformation')
            ->will($this->returnValueMap([['ops_direct_debit_request_params', []]]));
        $this->model->authorize($this->paymentInfo, 100);
    }

    public function testAuthorizeWithInlineCc()
    {
        $this->paymentInfo = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', null, [], '', false, false);
        /** @var \Netresearch\OPS\Model\Payment\DirectDebit $model */
        $model = $this->getMock(
            '\Netresearch\OPS\Model\Payment\DirectDebit',
            [
                'hasBrandAliasInterfaceSupport',
                'getConfigPaymentAction',
                'getQuoteHelper',
                'confirmPayment',
                'registerDirectDebitPayment',
                'isInlinePayment'
            ],
            [],
            '',
            false,
            false
        );
        $model->expects($this->once())
            ->method('getConfigPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $model->expects($this->any())->method('getQuoteHelper')->will($this->returnValue($this->quoteHelper));
        $model->expects($this->once())->method('confirmPayment')->will($this->returnSelf());
        $model->expects($this->any())->method('registerDirectDebitPayment')->will($this->returnSelf());
        $model->expects($this->any())->method('isInlinePayment')->will($this->returnValue(true));
        $this->paymentInfo->setMethodInstance($model);
        $this->paymentInfo->setOrder($this->order);
        $model->authorize($this->paymentInfo, 100);
    }

    public function testConfirmPaymentWithResponse()
    {
        $this->markTestIncomplete('Using old DirectDebit behaviour');
        $requestParams = ['ORDERID' => '123'];
        $response = ['PAYID' => 4711, 'ORDERID' => '123', 'STATUS' => 5];
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', null, [], '', false, false);
        $payment->setAdditionalInformation('ops_direct_debit_request_params', []);
        $payment->setMethodInstance($this->model);
        $payment->setOrder($order);
        $this->model->setInfoInstance($payment);
        $this->validatorFactory->expects($this->any())->method('create')->will($this->returnValue($this->validator));
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $this->directDebitHelper->expects($this->once())
            ->method('getDirectLinkRequestParams')
            ->will($this->returnValue($requestParams));
        $this->directLinkHelper->expects($this->once())
            ->method('performDirectLinkRequest')
            ->will($this->returnValue($response));
        $this->validatorParameter->expects($this->any())->method('isValid')->will($this->returnValue(true));
        $this->responseHandler->expects($this->once())
            ->method('processResponse')
            ->with($response, $this->model, false)
            ->will($this->returnSelf());
        $this->model->authorize($payment, 100);
    }

    public function testConfirmPaymentWithInvalidResponse()
    {
        $this->markTestIncomplete('Using old DirectDebit behaviour');
        $requestParams = ['ORDERID' => '123'];
        $response = ['PAYID' => 4711, 'ORDERID' => '123', 'STATUS' => 5];
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', null, [], '', false, false);
        $payment->setAdditionalInformation('ops_direct_debit_request_params', []);
        $payment->setMethodInstance($this->model);
        $payment->setOrder($order);
        $this->model->setInfoInstance($payment);
        $this->validatorFactory->expects($this->any())->method('create')->will($this->returnValue($this->validator));
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $this->directDebitHelper->expects($this->once())
            ->method('getDirectLinkRequestParams')
            ->will($this->returnValue($requestParams));
        $this->directLinkHelper->expects($this->once())
            ->method('performDirectLinkRequest')
            ->will($this->returnValue($response));
        $this->validatorParameter->expects($this->any())->method('isValid')->will($this->returnValue(true));
        $this->responseHandler->expects($this->once())
            ->method('processResponse')
            ->with($response, $this->model, false)
            ->will($this->returnSelf());
        $this->model->authorize($payment, 100);
    }


    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testConfirmPaymentWithException()
    {
        $this->markTestIncomplete('using old DirectDebit behaviour');
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', null, [], '', false, false);
        $payment->setAdditionalInformation('ops_direct_debit_request_params', []);
        $payment->setMethodInstance($this->model);
        $payment->setOrder($order);
        $this->model->setInfoInstance($payment);
        $this->validatorFactory->expects($this->any())->method('create')->will($this->returnValue($this->validator));
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $this->validatorParameter->expects($this->any())->method('isValid')->will($this->returnValue(false));
        $this->validatorParameter->expects($this->any())->method('getMessages')->will($this->returnValue(['error']));
        $this->checkoutTypeOnepage->expects($this->once())
            ->method('getCheckout')
            ->will($this->returnValue(new \Magento\Framework\DataObject()));
        $this->model->authorize($payment, 100);
    }

    public function testCaptureDirectSaleDirectDebit()
    {
        $requestParams = ['ORDERID' => '123'];
        $response = [];
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', null, [], '', false, false);
        $payment->setAdditionalInformation('ops_direct_debit_request_params', []);
        $payment->setMethodInstance($this->model);
        $payment->setOrder($order);
        $this->model->setInfoInstance($payment);
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE));
        $this->paymentHelper->expects($this->any())->method('isInlinePayment')->will($this->returnValue(true));
        $this->validatorFactory->expects($this->any())->method('create')->will($this->returnValue($this->validator));
        $this->directDebitHelper->expects($this->once())
            ->method('getDirectLinkRequestParams')
            ->will($this->returnValue($requestParams));
        $this->directLinkHelper->expects($this->once())
            ->method('performDirectLinkRequest')
            ->will($this->returnValue($response));
        $this->validatorParameter->expects($this->any())->method('isValid')->will($this->returnValue(true));
        $this->responseHandler->expects($this->never())
            ->method('processResponse')
            ->with($response, $this->model, false)
            ->will($this->returnSelf());
        $this->paymentHelper->expects($this->once())
            ->method('handleUnknownStatus')
            ->with($order)
            ->will($this->returnSelf());
        $this->model->capture($payment, 100);
    }

    public function testCaptureDirectSaleDirectDebitInvoice()
    {
        $this->markTestIncomplete('using old directdebit behaviour');
        $requestParams = ['ORDERID' => '123'];
        $response = [];
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', null, [], '', false, false);
        $payment->setAdditionalInformation('ops_direct_debit_request_params', []);
        $payment->setAdditionalInformation('paymentId', 4711);
        $payment->setMethodInstance($this->model);
        $payment->setOrder($order);
        $this->model->setInfoInstance($payment);
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $this->paymentHelper->expects($this->any())->method('isInlinePayment')->will($this->returnValue(true));
        $this->validatorFactory->expects($this->any())->method('create')->will($this->returnValue($this->validator));
        $this->directDebitHelper->expects($this->once())
            ->method('getDirectLinkRequestParams')
            ->will($this->returnValue($requestParams));
        $this->directLinkHelper->expects($this->never())
            ->method('performDirectLinkRequest')
            ->will($this->returnValue($response));
        $this->validatorParameter->expects($this->any())->method('isValid')->will($this->returnValue(true));
        $this->responseHandler->expects($this->never())
            ->method('processResponse')
            ->with($response, $this->model, false)
            ->will($this->returnSelf());
        $this->paymentHelper->expects($this->never())
            ->method('handleUnknownStatus')
            ->with($order)
            ->will($this->returnSelf());
        $this->model->capture($payment, 0);
    }

    public function testCaptureDirectSaleCreditCard()
    {
        $ccHelper = $this->getMock('\Netresearch\OPS\Helper\Creditcard', [], [], '', false, false);
        /** @var \Netresearch\OPS\Model\Payment\Cc $model */
        $model = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Payment\Cc',
            [
                'checkoutSession' => $this->checkoutSession,
                'oPSConfig' => $this->config,
                'oPSQuoteHelper' => $this->quoteHelper,
                'oPSValidatorPaymentDirectDebitFactory' => $this->validatorFactory,
                'oPSValidatorParameterFactoryFactory' => $this->validatorParameterFactoryFactory,
                'oPSDirectDebitHelper' => $this->directDebitHelper,
                'oPSDirectlinkHelper' => $this->directLinkHelper,
                'oPSResponseHandler' => $this->responseHandler,
                'oPSPaymentHelper' => $this->paymentHelper,
                'checkoutTypeOnepage' => $this->checkoutTypeOnepage,
                'oPSCreditcardHelper' => $ccHelper
            ]
        );
        $requestParams = ['ORDERID' => '123'];
        $response = ['PAYID' => 4711, 'ORDERID' => '123', 'STATUS' => 5];
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', null, [], '', false, false);
        $payment->setAdditionalInformation('ops_direct_debit_request_params', []);
        $payment->setMethodInstance($this->model);
        $payment->setOrder($order);
        $model->setInfoInstance($payment);
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE));
        $this->paymentHelper->expects($this->any())->method('isInlinePayment')->will($this->returnValue(true));
        $this->validatorFactory->expects($this->any())->method('create')->will($this->returnValue($this->validator));
        $ccHelper->expects($this->once())->method('getDirectLinkRequestParams')->will($this->returnValue($requestParams));
        $this->directLinkHelper->expects($this->once())
            ->method('performDirectLinkRequest')
            ->will($this->returnValue($response));
        $this->validatorParameter->expects($this->any())->method('isValid')->will($this->returnValue(true));
        $this->responseHandler->expects($this->once())
            ->method('processResponse')
            ->with($response, $model, false)
            ->will($this->returnSelf());
        $this->paymentHelper->expects($this->never())
            ->method('handleUnknownStatus')
            ->with($order)
            ->will($this->returnSelf());
        $model->capture($payment, 100);
    }

    public function testCaptureDirectSaleCreditCardRedirect()
    {
        $ccHelper = $this->getMock('\Netresearch\OPS\Helper\Creditcard', [], [], '', false, false);
        /** @var \Netresearch\OPS\Model\Payment\Cc $model */
        $model = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Payment\Cc',
            [
                'checkoutSession' => $this->checkoutSession,
                'oPSConfig' => $this->config,
                'oPSQuoteHelper' => $this->quoteHelper,
                'oPSValidatorPaymentDirectDebitFactory' => $this->validatorFactory,
                'oPSValidatorParameterFactoryFactory' => $this->validatorParameterFactoryFactory,
                'oPSDirectDebitHelper' => $this->directDebitHelper,
                'oPSDirectlinkHelper' => $this->directLinkHelper,
                'oPSResponseHandler' => $this->responseHandler,
                'oPSPaymentHelper' => $this->paymentHelper,
                'checkoutTypeOnepage' => $this->checkoutTypeOnepage,
                'oPSCreditcardHelper' => $ccHelper
            ]
        );
        $requestParams = ['ORDERID' => '123'];
        $response = ['PAYID' => 4711, 'ORDERID' => '123', 'STATUS' => 5];
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', null, [], '', false, false);
        $payment->setAdditionalInformation('ops_direct_debit_request_params', []);
        $payment->setMethodInstance($this->model);
        $payment->setOrder($order);
        $model->setInfoInstance($payment);
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE));
        $this->paymentHelper->expects($this->any())->method('isInlinePayment')->will($this->returnValue(false));
        $this->validatorFactory->expects($this->any())->method('create')->will($this->returnValue($this->validator));
        $ccHelper->expects($this->never())->method('getDirectLinkRequestParams')->will($this->returnValue($requestParams));
        $this->directLinkHelper->expects($this->never())
            ->method('performDirectLinkRequest')
            ->will($this->returnValue($response));
        $this->validatorParameter->expects($this->any())->method('isValid')->will($this->returnValue(true));
        $this->responseHandler->expects($this->never())
            ->method('processResponse')
            ->with($response, $model, false)
            ->will($this->returnSelf());
        $this->paymentHelper->expects($this->never())
            ->method('handleUnknownStatus')
            ->with($order)
            ->will($this->returnSelf());
        $model->capture($payment, 100);

        return;
    }
}

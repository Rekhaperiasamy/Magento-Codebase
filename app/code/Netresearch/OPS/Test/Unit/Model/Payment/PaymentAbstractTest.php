<?php
namespace Netresearch\OPS\Test\Unit\Model\Payment;

class PaymentAbstractTest extends \PHPUnit_Framework_TestCase
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

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->paymentInfo = $this->getMock(
            '\Magento\Sales\Model\Order\Payment\Info',
            ['save'],
            [],
            '',
            false,
            false
        );
        $this->apiDirectlink = $this->getMock('\Netresearch\OPS\Model\Api\Directlink', [], [], '', false, false);
        $this->stringUtils = $this->objectManager->getObject('\Magento\Framework\Stdlib\StringUtils');
        $this->checkoutSession = $this->getMock('\Magento\Checkout\Model\Session', [], [], '', false, false);
        $this->customerSession = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false, false);
        $this->paymentRequestHelper = $this->getMock(
            '\Netresearch\OPS\Helper\Payment\Request',
            ['getMandatoryRequestFields', 'extractShipToParameters'],
            [],
            '',
            false,
            false
        );
        $this->config = $this->getMock('\Netresearch\OPS\Model\Config', [], [], '', false, false);
        $this->salesOrderFactory = $this->getMock('\Magento\Sales\Model\OrderFactory', [], [], '', false, false);
        $this->paymentHelper = $this->getMock('\Netresearch\OPS\Helper\Payment', [], [], '', false, false);
        $this->dataHelper = $this->getMock('\Netresearch\OPS\Helper\Data', [], [], '', false, false);
        $this->directlinkHelper = $this->getMock('\Netresearch\OPS\Helper\Directlink', [], [], '', false, false);
        $this->orderHelper = $this->getMock('\Netresearch\OPS\Helper\Order', [], [], '', false, false);
        $this->storeManager = $this->getMock('\Magento\Store\Model\StoreManager', [], [], '', false, false);
        $this->messageManager = $this->getMock('\Magento\Framework\Message\Manager', [], [], '', false, false);
        $this->statusUpdate = $this->getMock('\Netresearch\OPS\Model\Status\Update', [], [], '', false, false);
        $statusUpdateFactory = $this->getMock(
            '\Netresearch\OPS\Model\Status\UpdateFactory',
            [],
            [],
            '',
            false,
            false
        );
        $statusUpdateFactory->expects($this->any())->method('create')->will($this->returnValue($this->statusUpdate));
        $this->responseHandler = $this->getMock('\Netresearch\OPS\Model\Response\Handler', [], [], '', false, false);
        $this->model = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Payment\PaymentAbstract',
            [
                'stringUtils' => $this->stringUtils,
                'oPSApiDirectlink' => $this->apiDirectlink,
                'checkoutSession' => $this->checkoutSession,
                'oPSPaymentRequestHelper' => $this->paymentRequestHelper,
                'oPSConfig' => $this->config,
                'oPSPaymentHelper' => $this->paymentHelper,
                'oPSHelper' => $this->dataHelper,
                'salesOrderFactory' => $this->salesOrderFactory,
                'storeManager' => $this->storeManager,
                'oPSDirectlinkHelper' => $this->directlinkHelper,
                'messageManager' => $this->messageManager,
                'oPSStatusUpdateFactory' => $statusUpdateFactory,
                'oPSResponseHandler' => $this->responseHandler,
                'oPSOrderHelper' => $this->orderHelper,
                'customerSession' => $this->customerSession
            ]
        );
        $this->model->setInfoInstance($this->paymentInfo);
    }

    public function testCaptureWithZeroAmount()
    {
        $this->apiDirectlink->expects($this->never())->method('performRequest');
        $this->assertTrue($this->model->capture($this->paymentInfo, 0.00) instanceof
            \Netresearch\OPS\Model\Payment\PaymentAbstract);
    }

    public function testGetOrderDescriptionShorterThen100Chars()
    {
        $items = [
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'abc'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => true,
                'name' => 'def'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'ghi'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'Dubbelwerkende cilinder Boring ø70 Stang ø40 3/8'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => '0123456789012345678901234567890123456789012xxxxxx'
            ]),
        ];
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', ['getAllItems'], [], '', false);
        $order
            ->expects($this->any())
            ->method('getAllItems')
            ->will($this->returnValue($items));

        $result = $this->model->setEncoding('utf-8')->_getOrderDescription($order);
        $this->assertEquals(
            'abcghiDubbelwerkende cilinder Boring ø70 Stang ø40 3/80123456789012345678901234567890123456789012xxx',
            $result
        );
    }

    public function testGetOrderDescriptionLongerThen100Chars()
    {
        $items = [
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => '1bcdefghij abcdefghij abcdefghij abcdefghij abcdefghi1'
                //54 chars
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => '2bcdefghij abcdefghij abcdefghij abcdefghij abcdefghi2'
                //54 chars
            ])
        ];
        $order = $this->getMock('\Magento\Sales\Model\Order', ['getAllItems'], [], '', false);
        $order
            ->expects($this->any())
            ->method('getAllItems')
            ->will($this->returnValue($items));

        $result = $this->model->setEncoding('utf-8')->_getOrderDescription($order);
        $this->assertEquals(
            '1bcdefghij abcdefghij abcdefghij abcdefghij abcdefghi12bcdefghij abcdefghij abcdefghij abcdefghij ab',
            $result
        );
    }

    public function testGetOrderDescriptionLongerThen100CharsOneItem()
    {
        $items = [
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => '1bcdefghij abcdefghij abcdefghij abcdefghij abcdefghi1 '
                    . '2bcdefghij abcdefghij abcdefghij abcdefghij abcdefghi2'
            ])
        ];
        $order = $this->getMock('\Magento\Sales\Model\Order', ['getAllItems'], [], '', false);
        $order
            ->expects($this->any())
            ->method('getAllItems')
            ->will($this->returnValue($items));

        $result = $this->model->setEncoding('utf-8')->_getOrderDescription($order);
        $this->assertEquals(
            '1bcdefghij abcdefghij abcdefghij abcdefghij abcdefghi1 2bcdefghij abcdefghij abcdefghij abcdefghij a',
            $result
        );
    }

    public function testShouldReturnCorrectBrandAndPMValuesForBankTransfer()
    {
        $quote = new \Magento\Framework\DataObject(['payment' => $this->paymentInfo]);
        $this->checkoutSession->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $this->paymentInfo->expects($this->any())->method('getId')->will($this->returnValue('1'));
        /** @var \Netresearch\OPS\Model\Payment\BankTransfer $method */
        $method = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Payment\BankTransfer',
            [
                'stringUtils' => $this->stringUtils,
                'oPSApiDirectlink' => $this->apiDirectlink,
                'checkoutSession' => $this->checkoutSession
            ]
        );
        $method->setInfoInstance($this->paymentInfo);
        try {
            $method->assignData(new \Magento\Framework\DataObject(['additional_data' => ['country_id' => 'DE']]));
        } catch (\Exception $e) {
            if ('Cannot retrieve the payment information object instance.' != $e->getMessage()) {
                throw $e;
            }
        }
        $this->assertEquals(
            'Bank transfer DE',
            $method->getOpsBrand(null)
        );
        $this->assertEquals(
            'Bank transfer DE',
            $method->getOpsCode(null)
        );
    }

    public function testCanCancelManually()
    {
        $payment = new \Magento\Framework\DataObject();
        $order = new \Magento\Framework\DataObject(['payment' => $payment]);
        $info = ['status' => \Netresearch\OPS\Model\Status::INVALID_INCOMPLETE];
        $payment->setAdditionalInformation($info);
        //Check for successful can cancel (pending_payment and payment status 0)
        $this->assertTrue($this->model->canCancelManually($order));
        //Check for successful cancel (pending_payment and payment status null/not existing)
        $info = ['status' => null];
        $payment->setAdditionalInformation($info);
        $this->assertTrue($this->model->canCancelManually($order));
        //Check for denied can cancel (pending_payment and payment status 5)
        $info = ['status' => \Netresearch\OPS\Model\Status::AUTHORIZED];
        $payment->setAdditionalInformation($info);
        $this->assertFalse($this->model->canCancelManually($order));
        //Check for denied can cancel (processing and payment status 0)
        $info = ['status' => \Netresearch\OPS\Model\Status::INVALID_INCOMPLETE];
        $payment->setAdditionalInformation($info);
        $this->assertTrue($this->model->canCancelManually($order));
    }

    public function testGetMethodDependendFormFields()
    {
        $billingAddress = new \Magento\Framework\DataObject([
            'firstname' => 'test',
            'lastname' => 'test',
            'postcode' => '20704'
        ]);
        $shippingAddress = clone $billingAddress;
        $items = [
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'abc'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => true,
                'name' => 'def'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'ghi'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'Dubbelwerkende cilinder Boring ø70 Stang ø40 3/8'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => '0123456789012345678901234567890123456789012xxxxxx'
            ]),
        ];
        $orderPaymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $orderPaymentInstance->expects($this->any())->method('getOpsBrand')->will($this->returnValue('VISA'));
        $orderPayment = $this->getMock('Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $orderPayment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($orderPaymentInstance));
        $quote = new \Magento\Framework\DataObject();
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())->method('getPayment')->will($this->returnValue($orderPayment));
        $order->expects($this->any())->method('getAllItems')->will($this->returnValue($items));
        $order->expects($this->any())->method('getBillingAddress')->will($this->returnValue($billingAddress));
        $order->expects($this->any())->method('getShippingAddress')->will($this->returnValue($shippingAddress));
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $this->config->expects($this->any())->method('canSubmitExtraParameter')->will($this->returnValue(true));
        $this->dataHelper->expects($this->any())->method('getModuleVersionString')->will($this->returnValue('OGNM210'));
        $this->orderHelper->expects($this->any())->method('checkIfAddressesAreSame')->will($this->returnValue(1));
        $this->paymentRequestHelper->expects($this->any())
            ->method('getMandatoryRequestFields')
            ->will($this->returnValue(['ORDERID' => '1234']));
        $this->paymentRequestHelper->expects($this->any())
            ->method('getOwnerParams')
            ->will($this->returnValue(array_reverse($this->getOwnerParams())));
        $this->paymentRequestHelper->expects($this->any())->method('getTemplateParams')->will($this->returnValue([]));
        $this->paymentRequestHelper->expects($this->any())
            ->method('extractShipToParameters')
            ->will($this->returnValue(array_flip($this->getShippingParams())));
        $this->paymentRequestHelper->setConfig($this->config);
        $this->orderHelper->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $this->customerSession->expects($this->any())->method('isLoggedIn')->will($this->returnValue(true));
        $this->customerSession->expects($this->any())->method('getCustomerId')->will($this->returnValue(1));
        $formFields = $this->model->getFormFields($order, []);


        $this->assertTrue(array_key_exists('CN', $formFields));
        $this->assertTrue(array_key_exists('OWNERZIP', $formFields));
        $this->assertTrue(array_key_exists('OWNERCTY', $formFields));
        $this->assertTrue(array_key_exists('OWNERTOWN', $formFields));
        $this->assertTrue(array_key_exists('COM', $formFields));
        $this->assertTrue(array_key_exists('OWNERTELNO', $formFields));
        $this->assertTrue(array_key_exists('OWNERADDRESS', $formFields));
        $this->assertTrue(array_key_exists('BRAND', $formFields));
        $this->assertTrue(array_key_exists('ADDMATCH', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_POSTALCODE', $formFields));
        $this->assertTrue(array_key_exists('ECOM_BILLTO_POSTAL_POSTALCODE', $formFields));
        $this->assertTrue(array_key_exists('CUID', $formFields));
    }

    public function testGetFormFieldsEmptyWithNonExistingOrder()
    {
        $this->assertTrue(is_array($this->model->getFormFields(null, [])));
        $this->assertEquals(
            0,
            count($this->model->getFormFields(null, []))
        );
    }

    public function testGetFormFieldsWithEmptyOrderPassedButExistingOrder()
    {
        $billingAddress = new \Magento\Framework\DataObject([
            'firstname' => 'test',
            'lastname' => 'test',
            'postcode' => '20704'
        ]);
        $shippingAddress = clone $billingAddress;
        $items = [
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'abc'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => true,
                'name' => 'def'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'ghi'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'Dubbelwerkende cilinder Boring ø70 Stang ø40 3/8'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => '0123456789012345678901234567890123456789012xxxxxx'
            ]),
        ];
        $orderPaymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $orderPaymentInstance->expects($this->any())->method('getOpsBrand')->will($this->returnValue('VISA'));
        $orderPayment = $this->getMock('Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $orderPayment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($orderPaymentInstance));
        $quote = new \Magento\Framework\DataObject();
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())->method('getPayment')->will($this->returnValue($orderPayment));
        $order->expects($this->any())->method('getAllItems')->will($this->returnValue($items));
        $order->expects($this->any())->method('getBillingAddress')->will($this->returnValue($billingAddress));
        $order->expects($this->any())->method('getShippingAddress')->will($this->returnValue($shippingAddress));
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $this->config->expects($this->any())->method('canSubmitExtraParameter')->will($this->returnValue(true));
        $this->dataHelper->expects($this->any())->method('getModuleVersionString')->will($this->returnValue('OGNM210'));
        $this->orderHelper->expects($this->any())->method('checkIfAddressesAreSame')->will($this->returnValue(1));
        $this->paymentRequestHelper->expects($this->any())
            ->method('getMandatoryRequestFields')
            ->will($this->returnValue(['PSPID' => 10, 'ORDERID' => '1234']));
        $this->paymentRequestHelper->expects($this->any())
            ->method('getOwnerParams')
            ->will($this->returnValue(array_reverse($this->getOwnerParams())));
        $this->paymentRequestHelper->expects($this->any())->method('getTemplateParams')->will($this->returnValue([]));
        $this->paymentRequestHelper->expects($this->any())
            ->method('extractShipToParameters')
            ->will($this->returnValue(array_flip($this->getShippingParams())));
        $this->paymentRequestHelper->setConfig($this->config);
        $this->orderHelper->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $this->customerSession->expects($this->any())->method('isLoggedIn')->will($this->returnValue(true));
        $this->customerSession->expects($this->any())->method('getCustomerId')->will($this->returnValue(1));
        $formFields = $this->model->getFormFields($order, []);
        $this->assertArrayHasKey('PSPID', $formFields);
        $this->assertArrayHasKey('SHASIGN', $formFields);
    }

    public function testGetFormFields()
    {
        $billingAddress = new \Magento\Framework\DataObject([
            'firstname' => 'test',
            'lastname' => 'test',
            'postcode' => '20704'
        ]);
        $shippingAddress = clone $billingAddress;
        $items = [
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'abc'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => true,
                'name' => 'def'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'ghi'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'Dubbelwerkende cilinder Boring ø70 Stang ø40 3/8'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => '0123456789012345678901234567890123456789012xxxxxx'
            ]),
        ];
        $orderPaymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $orderPaymentInstance->expects($this->any())->method('getOpsBrand')->will($this->returnValue('VISA'));
        $orderPayment = $this->getMock('Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $orderPayment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($orderPaymentInstance));
        $quote = new \Magento\Framework\DataObject();
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())->method('getPayment')->will($this->returnValue($orderPayment));
        $order->expects($this->any())->method('getAllItems')->will($this->returnValue($items));
        $order->expects($this->any())->method('getBillingAddress')->will($this->returnValue($billingAddress));
        $order->expects($this->any())->method('getShippingAddress')->will($this->returnValue($shippingAddress));
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $this->config->expects($this->any())->method('canSubmitExtraParameter')->will($this->returnValue(true));
        $this->dataHelper->expects($this->any())->method('getModuleVersionString')->will($this->returnValue('OGNM210'));
        $this->orderHelper->expects($this->any())->method('checkIfAddressesAreSame')->will($this->returnValue(1));
        $this->paymentRequestHelper->expects($this->any())
            ->method('getMandatoryRequestFields')
            ->will($this->returnValue(['PSPID' => 'NRMAGENTO','ORDERID' => '1234']));
        $this->paymentRequestHelper->expects($this->any())
            ->method('getOwnerParams')
            ->will($this->returnValue(array_reverse($this->getOwnerParams())));
        $this->paymentRequestHelper->expects($this->any())->method('getTemplateParams')->will($this->returnValue([]));
        $this->paymentRequestHelper->expects($this->any())
            ->method('extractShipToParameters')
            ->will($this->returnValue(array_flip($this->getShippingParams())));
        $this->paymentRequestHelper->setConfig($this->config);
        $this->paymentHelper->expects($this->any())
            ->method('shaCrypt')
            ->will($this->returnValue('2d9f92d6f3955847ab2db427be75fe7eb0cde045'));
        $this->orderHelper->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $this->customerSession->expects($this->any())->method('isLoggedIn')->will($this->returnValue(true));
        $this->customerSession->expects($this->any())->method('getCustomerId')->will($this->returnValue(1));
        $formFields = $this->model->getFormFields($order, []);
        $this->assertArrayHasKey('PSPID', $formFields);
        $this->assertArrayHasKey('SHASIGN', $formFields);
        $this->assertArrayHasKey('ACCEPTURL', $formFields);
        $this->assertArrayHasKey('DECLINEURL', $formFields);
        $this->assertArrayHasKey('EXCEPTIONURL', $formFields);
        $this->assertArrayHasKey('CANCELURL', $formFields);
        $this->assertEquals('NRMAGENTO', $formFields['PSPID']);
        $this->assertEquals('2d9f92d6f3955847ab2db427be75fe7eb0cde045', $formFields['SHASIGN']);
    }

    public function testGetFormFieldsWithFormDependendFormFields()
    {
        $billingAddress = new \Magento\Framework\DataObject([
            'firstname' => 'test',
            'lastname' => 'test',
            'postcode' => '20704'
        ]);
        $shippingAddress = clone $billingAddress;
        $items = [
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'abc'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => true,
                'name' => 'def'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'ghi'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'Dubbelwerkende cilinder Boring ø70 Stang ø40 3/8'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => '0123456789012345678901234567890123456789012xxxxxx'
            ]),
        ];
        $orderPaymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $orderPaymentInstance->expects($this->any())->method('getOpsBrand')->will($this->returnValue('VISA'));
        $orderPayment = $this->getMock('Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $orderPayment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($orderPaymentInstance));
        $quote = new \Magento\Framework\DataObject();
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())->method('getPayment')->will($this->returnValue($orderPayment));
        $order->expects($this->any())->method('getAllItems')->will($this->returnValue($items));
        $order->expects($this->any())->method('getBillingAddress')->will($this->returnValue($billingAddress));
        $order->expects($this->any())->method('getShippingAddress')->will($this->returnValue($shippingAddress));
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $this->config->expects($this->any())->method('canSubmitExtraParameter')->will($this->returnValue(true));
        $this->dataHelper->expects($this->any())->method('getModuleVersionString')->will($this->returnValue('OGNM210'));
        $this->orderHelper->expects($this->any())->method('checkIfAddressesAreSame')->will($this->returnValue(1));
        $this->paymentRequestHelper->expects($this->any())
            ->method('getMandatoryRequestFields')
            ->will($this->returnValue(['PSPID' => 'NRMAGENTO', 'foo' => 'bla', 'ORDERID' => '1234']));
        $this->paymentRequestHelper->expects($this->any())
            ->method('getOwnerParams')
            ->will($this->returnValue(array_reverse($this->getOwnerParams())));
        $this->paymentRequestHelper->expects($this->any())->method('getTemplateParams')->will($this->returnValue([]));
        $this->paymentRequestHelper->expects($this->any())
            ->method('extractShipToParameters')
            ->will($this->returnValue(array_flip($this->getShippingParams())));
        $this->paymentRequestHelper->setConfig($this->config);
        $this->paymentHelper->expects($this->any())
            ->method('shaCrypt')
            ->will($this->returnValue('2d9f92d6f3955847ab2db427be75fe7eb0cde045'));
        $this->orderHelper->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $this->customerSession->expects($this->any())->method('isLoggedIn')->will($this->returnValue(true));
        $this->customerSession->expects($this->any())->method('getCustomerId')->will($this->returnValue(1));
        $formFields = $this->model->getFormFields($order, []);
        $this->assertArrayHasKey('PSPID', $formFields);
        $this->assertArrayHasKey('SHASIGN', $formFields);
        $this->assertArrayHasKey('foo', $formFields);
        $this->assertEquals('NRMAGENTO', $formFields['PSPID']);
        $this->assertEquals(
            '2d9f92d6f3955847ab2db427be75fe7eb0cde045',
            $formFields['SHASIGN']
        );
        $this->assertEquals('bla', $formFields['foo']);
    }

    public function testGetFormFieldsWithStoreId()
    {
        $billingAddress = new \Magento\Framework\DataObject([
            'firstname' => 'test',
            'lastname' => 'test',
            'postcode' => '20704'
        ]);
        $shippingAddress = clone $billingAddress;
        $items = [
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'abc'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => true,
                'name' => 'def'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'ghi'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => 'Dubbelwerkende cilinder Boring ø70 Stang ø40 3/8'
            ]),
            new \Magento\Framework\DataObject([
                'parent_item' => false,
                'name' => '0123456789012345678901234567890123456789012xxxxxx'
            ]),
        ];
        $orderPaymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $orderPaymentInstance->expects($this->any())->method('getOpsBrand')->will($this->returnValue('VISA'));
        $orderPayment = $this->getMock('Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $orderPayment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($orderPaymentInstance));
        $quote = new \Magento\Framework\DataObject();
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())->method('getPayment')->will($this->returnValue($orderPayment));
        $order->expects($this->any())->method('getAllItems')->will($this->returnValue($items));
        $order->expects($this->any())->method('getBillingAddress')->will($this->returnValue($billingAddress));
        $order->expects($this->any())->method('getShippingAddress')->will($this->returnValue($shippingAddress));
        $this->config->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $this->config->expects($this->any())->method('canSubmitExtraParameter')->will($this->returnValue(true));
        $this->dataHelper->expects($this->any())->method('getModuleVersionString')->will($this->returnValue('OGNM210'));
        $this->orderHelper->expects($this->any())->method('checkIfAddressesAreSame')->will($this->returnValue(1));
        $this->paymentRequestHelper->expects($this->any())
            ->method('getMandatoryRequestFields')
            ->will($this->returnValue(['PSPID' => 'NRMAGENTO5', 'foo' => 'bla', 'ORDERID' =>'12213']));
        $this->paymentRequestHelper->expects($this->any())
            ->method('getOwnerParams')
            ->will($this->returnValue(array_reverse($this->getOwnerParams())));
        $this->paymentRequestHelper->expects($this->any())->method('getTemplateParams')->will($this->returnValue([]));
        $this->paymentRequestHelper->expects($this->any())
            ->method('extractShipToParameters')
            ->will($this->returnValue(array_flip($this->getShippingParams())));
        $this->paymentRequestHelper->setConfig($this->config);
        $this->paymentHelper->expects($this->any())
            ->method('shaCrypt')
            ->will($this->returnValue('0f119cdea2f8ddc0c852bab4765f12d3913982fc'));
        $this->orderHelper->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $this->customerSession->expects($this->any())->method('isLoggedIn')->will($this->returnValue(true));
        $this->customerSession->expects($this->any())->method('getCustomerId')->will($this->returnValue(1));

        $formFields = $this->model->getFormFields($order, []);

        $this->assertArrayHasKey('PSPID', $formFields);
        $this->assertArrayHasKey('SHASIGN', $formFields);
        $this->assertEquals('NRMAGENTO5', $formFields['PSPID']);
        $this->assertEquals(
            '0f119cdea2f8ddc0c852bab4765f12d3913982fc',
            $formFields['SHASIGN']
        );
    }


    public function testVoidWithExistingVoidTransactionLeadsToRedirect()
    {
        $order = $this->getMock('\Magento\Sales\Model\Order', ['load'], [], '', false, false);
        $order->expects($this->any())->method('load')->will($this->returnSelf());
        $this->salesOrderFactory->expects($this->any())->method('create')->will($this->returnValue($order));
        $this->paymentHelper->expects($this->any())
            ->method('getBaseGrandTotalFromSalesObject')
            ->will($this->returnValue(100));
        $this->dataHelper->expects($this->any())->method('getAmount')->will($this->returnCallback(function ($v) {
            return $v * 100;
        }));
        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue(new \Magento\Framework\DataObject(['base_currency_code' => 'EUR'])));
        $this->directlinkHelper->expects($this->any())
            ->method('checkExistingTransact')
            ->with(\Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_VOID_TRANSACTION_TYPE, 11)
            ->will($this->returnValue(true));
        $this->paymentInfo->setAdditionalInformation('status', 5);
        $this->paymentInfo->setOrder(new \Magento\Framework\DataObject(['id' => 11]));
        $this->paymentInfo->setBaseAmountPaidOnline(0);
        $this->messageManager->expects($this->once())
            ->method('addError')
            ->with(__('You already sent a void request. Please wait until the void request will be acknowledged.'))
            ->will($this->returnSelf());
        $this->model->void($this->paymentInfo);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Fake Request failed
     */
    public function testVoidFailsWhenRequestThrowsException()
    {
        $order = $this->getMock('\Magento\Sales\Model\Order', ['load'], [], '', false, false);
        $order->expects($this->any())->method('load')->will($this->returnSelf());
        $order->setId(11);
        $this->salesOrderFactory->expects($this->any())->method('create')->will($this->returnValue($order));
        $this->paymentHelper->expects($this->any())
            ->method('getBaseGrandTotalFromSalesObject')
            ->will($this->returnValue(100));
        $this->dataHelper->expects($this->any())->method('getAmount')->will($this->returnCallback(function ($v) {
            return $v * 100;
        }));
        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue(new \Magento\Framework\DataObject(['base_currency_code' => 'EUR'])));
        $this->directlinkHelper->expects($this->any())
            ->method('checkExistingTransact')
            ->with(\Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_VOID_TRANSACTION_TYPE, 11)
            ->will($this->returnValue(false));
        $this->paymentInfo->setAdditionalInformation('status', 5);
        $this->paymentInfo->setOrder($order);
        $this->paymentInfo->setBaseAmountPaidOnline(0);
        $exception = new \Exception('Fake Request failed');
        $this->apiDirectlink->expects($this->any())->method('performRequest')->will($this->throwException($exception));
        $this->statusUpdate->expects($this->once())->method('updateStatusFor')->with($order)->will($this->returnSelf());
        $this->dataHelper->expects($this->once())->method('log')->will($this->returnSelf());
        $this->model->void($this->paymentInfo);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Process Responce failed
     */
    public function testVoidFailsWhenStatusIsUnknown()
    {
        $order = $this->getMock('\Magento\Sales\Model\Order', ['load'], [], '', false, false);
        $order->expects($this->any())->method('load')->will($this->returnSelf());
        $order->setId(11);
        $this->salesOrderFactory->expects($this->any())->method('create')->will($this->returnValue($order));
        $this->paymentHelper->expects($this->any())
            ->method('getBaseGrandTotalFromSalesObject')
            ->will($this->returnValue(100));
        $this->dataHelper->expects($this->any())->method('getAmount')->will($this->returnCallback(function ($v) {
            return $v * 100;
        }));
        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue(new \Magento\Framework\DataObject(['base_currency_code' => 'EUR'])));
        $this->directlinkHelper->expects($this->any())
            ->method('checkExistingTransact')
            ->with(\Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_VOID_TRANSACTION_TYPE, 11)
            ->will($this->returnValue(false));
        $this->paymentInfo->setAdditionalInformation('status', 5);
        $this->paymentInfo->setOrder($order);
        $this->paymentInfo->setBaseAmountPaidOnline(0);
        $this->apiDirectlink->expects($this->any())->method('performRequest')->will($this->returnValue([
            'STATUS' => 666
        ]));
        $this->responseHandler->expects($this->once())
            ->method('processResponse')
            ->will($this->throwException(new \Exception('Process Responce failed')));
        $this->statusUpdate->expects($this->once())->method('updateStatusFor')->with($order)->will($this->returnSelf());
        $this->dataHelper->expects($this->once())->method('log')->will($this->returnSelf());
        $this->model->void($this->paymentInfo);
    }

    public function testVoidWithStatusVoidWaiting()
    {
        $order = $this->getMock('\Magento\Sales\Model\Order', ['load'], [], '', false, false);
        $order->expects($this->any())->method('load')->will($this->returnSelf());
        $order->setId(11);
        $this->salesOrderFactory->expects($this->any())->method('create')->will($this->returnValue($order));
        $this->paymentHelper->expects($this->any())
            ->method('getBaseGrandTotalFromSalesObject')
            ->will($this->returnValue(100));
        $this->dataHelper->expects($this->any())->method('getAmount')->will($this->returnCallback(function ($v) {
            return $v * 100;
        }));
        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue(new \Magento\Framework\DataObject(['base_currency_code' => 'EUR'])));
        $this->directlinkHelper->expects($this->any())
            ->method('checkExistingTransact')
            ->with(\Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_VOID_TRANSACTION_TYPE, 11)
            ->will($this->returnValue(false));
        $this->paymentInfo->setAdditionalInformation('status', 5);
        $this->paymentInfo->setOrder($order);
        $this->paymentInfo->setBaseAmountPaidOnline(0);
        $result = [
            'STATUS' => \Netresearch\OPS\Model\Status::DELETION_WAITING,
            'PAYID' => '4711',
            'PAYIDSUB' => '0815'
        ];
        $this->apiDirectlink->expects($this->any())->method('performRequest')->will($this->returnValue($result));
        $this->responseHandler->expects($this->once())
            ->method('processResponse')
            ->with($result, $this->model, false)
            ->will($this->returnSelf());
        $this->model->void($this->paymentInfo);
    }

    public function testVoidWithStatusVoidAccepted()
    {
        $order = $this->getMock('\Magento\Sales\Model\Order', ['load'], [], '', false, false);
        $order->expects($this->any())->method('load')->will($this->returnSelf());
        $order->setId(11);
        $this->salesOrderFactory->expects($this->any())->method('create')->will($this->returnValue($order));
        $this->paymentHelper->expects($this->any())
            ->method('getBaseGrandTotalFromSalesObject')
            ->will($this->returnValue(100));
        $this->dataHelper->expects($this->any())->method('getAmount')->will($this->returnCallback(function ($v) {
            return $v * 100;
        }));
        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue(new \Magento\Framework\DataObject(['base_currency_code' => 'EUR'])));
        $this->directlinkHelper->expects($this->any())
            ->method('checkExistingTransact')
            ->with(\Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_VOID_TRANSACTION_TYPE, 11)
            ->will($this->returnValue(false));
        $this->paymentInfo->setAdditionalInformation('status', 5);
        $this->paymentInfo->setOrder($order);
        $this->paymentInfo->setBaseAmountPaidOnline(0);
        $result = [
            'STATUS' => \Netresearch\OPS\Model\Status::AUTHORIZED_AND_CANCELLED,
            'PAYID' => '4711',
            'PAYIDSUB' => '0815'
        ];
        $this->apiDirectlink->expects($this->any())->method('performRequest')->will($this->returnValue($result));
        $this->responseHandler->expects($this->once())
            ->method('processResponse')
            ->with($result, $this->model, false)
            ->will($this->returnSelf());
        $this->model->void($this->paymentInfo);
    }

    public function testGetOpsHtmlAnswer()
    {
        $quoteId = 42;
        $orderId = 22;
        $orderIncrementId = '10000022';
        $this->paymentInfo = $this->getMock('\Magento\Payment\Model\Info', ['save'], [], '', false, false);
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['loadByAttribute', 'getPayment'],
            [],
            '',
            false,
            false
        );
        $order->setId($orderId);
        $order->expects($this->any())->method('loadByAttribute')->will($this->returnSelf());
        $order->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue(new \Magento\Framework\DataObject(['additional_information' => ['HTML_ANSWER' => 'HTML']])));
        $this->salesOrderFactory->expects($this->any())->method('create')->will($this->returnValue($order));
        $quote = new \Magento\Framework\DataObject();
        $quote->setId($quoteId);
        $this->checkoutSession->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $this->checkoutSession->expects($this->any())
            ->method('getLastRealOrderId')
            ->will($this->returnValue($orderIncrementId));
        $this->paymentInfo->setAdditionalInformation('HTML_ANSWER', 'HTML');
        $this->assertEquals('HTML', $this->model->getOpsHtmlAnswer());
        $quote->setId(null);
        $this->assertEquals('HTML', $this->model->getOpsHtmlAnswer());
        $this->assertEquals('HTML', $this->model->getOpsHtmlAnswer($this->paymentInfo));
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage The payment review action is unavailable.
     */
    public function testAcceptPaymentNotSupportedState()
    {
        $this->paymentInfo->setAdditionalInformation('status', 99);
        $this->model->acceptPayment($this->paymentInfo);
    }


    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage The order can not be accepted via Magento. For the actual status of the payment check the Ingenico ePayments backend.
     */
    public function testAcceptPaymentSupportedState()
    {
        $this->paymentInfo->setAdditionalInformation('status', 57);
        $this->model->acceptPayment($this->paymentInfo);
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage The payment review action is unavailable.
     */
    public function testDenyPaymentNotSupportedState()
    {
        $this->paymentInfo->setAdditionalInformation('status', 99);
        $this->model->denyPayment($this->paymentInfo);
    }

    public function testDenyPaymentSupportedState()
    {
        $this->paymentInfo->setAdditionalInformation('status', 57);
        $this->assertTrue($this->model->denyPayment($this->paymentInfo));
    }

    public function testCanReviewPaymentFalse()
    {
        $this->paymentInfo->setAdditionalInformation('status', 5);
        $this->assertFalse($this->model->canReviewPayment($this->paymentInfo));
    }

    public function testCanReviewPaymentTrue()
    {
        $this->paymentInfo->setAdditionalInformation('status', 57);
        $this->assertTrue($this->model->canReviewPayment($this->paymentInfo));
    }

    protected function getOwnerParams()
    {
        return $ownerParams = [
            'OWNERADDRESS',
            'OWNERTOWN',
            'OWNERZIP',
            'OWNERTELNO',
            'OWNERCTY',
            'ADDMATCH',
            'ECOM_BILLTO_POSTAL_POSTALCODE',
        ];
    }

    protected function getShippingParams()
    {
        $paramValues = [
            'ECOM_SHIPTO_POSTAL_NAME_FIRST',
            'ECOM_SHIPTO_POSTAL_NAME_LAST',
            'ECOM_SHIPTO_POSTAL_STREET_LINE1',
            'ECOM_SHIPTO_POSTAL_STREET_LINE2',
            'ECOM_SHIPTO_POSTAL_COUNTRYCODE',
            'ECOM_SHIPTO_POSTAL_CITY',
            'ECOM_SHIPTO_POSTAL_POSTALCODE'
        ];

        return $paramValues;
    }
}

<?php
namespace Netresearch\OPS\Test\Unit\Model\Payment\Kwixo;

class KwixoAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\Kwixo\KwixoAbstract
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
     * @var \Netresearch\OPS\Helper\Order\Refund
     */
    private $orderRefundHelper;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \Magento\Framework\App\Config
     */
    private $scopeConfig;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $salesOrderFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $catalogProductFactory;

    /**
     * @var \Netresearch\OPS\Model\Kwixo\Category\MappingFactory
     */
    private $kwixoCategoryMappingFactory;

    /**
     * @var \Netresearch\OPS\Model\Kwixo\Shipping\Setting
     */
    protected $kwixoShippingSetting;

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
            [
                                                         'getMandatoryRequestFields',
                                                         'extractShipToParameters',
                                                         'getOwnerParams'
                                                     ],
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
        $this->orderRefundHelper    = $this->getMock('\Netresearch\OPS\Helper\Order\Refund', [], [], '', false, false);
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
        $this->responseHandler       = $this->getMock(
            '\Netresearch\OPS\Model\Response\Handler',
            [],
            [],
            '',
            false,
            false
        );
        $this->registry              = new \Magento\Framework\Registry();
        $this->kwixoShippingSetting  = $this->objectManager->getObject('\Netresearch\OPS\Model\Kwixo\Shipping\Setting');
        $kwixoShippingSettingFactory = $this->getMock(
            '\Netresearch\OPS\Model\Kwixo\Shipping\SettingFactory',
            [],
            [],
            '',
            false,
            false
        );
        $kwixoShippingSettingFactory->expects($this->any())
                                    ->method('create')
                                    ->will($this->returnValue($this->kwixoShippingSetting));
        $this->catalogProductFactory       = $this->getMock(
            '\Magento\Catalog\Model\ProductFactory',
            [],
            [],
            '',
            false,
            false
        );
        $this->eavConfig                   = $this->getMock('\Magento\Eav\Model\Config', [], [], '', false, false);
        $this->kwixoCategoryMappingFactory = $this->getMock(
            '\Netresearch\OPS\Model\Kwixo\Category\MappingFactory',
            [],
            [],
            '',
            false,
            false
        );
        $this->scopeConfig                 = $this->getMock('\Magento\Framework\App\Config', [], [], '', false, false);
        $this->model
                                           = $this->objectManager->getObject(
                                               '\Netresearch\OPS\Model\Payment\Kwixo\KwixoAbstract',
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
                                                                                 'oPSBackendOperationParameterFactory' => $backendOperationParameterFactory,
                                                                                 'oPSOrderRefundHelper'                => $this->orderRefundHelper,
                                                                                 'oPSKwixoShippingSettingFactory'      => $kwixoShippingSettingFactory,
                                                                                 'eavConfig'                           => $this->eavConfig,
                                                                                 'catalogProductFactory'               => $this->catalogProductFactory,
                                                                                 'oPSKwixoCategoryMappingFactory'      => $this->kwixoCategoryMappingFactory,
                                                                                 'scopeConfig'                         => $this->scopeConfig
                                                                             ]
                                           );
        $this->model->setInfoInstance($this->paymentInfo);
    }

    public function testGetMethodDependendFormFields()
    {
        $billingAddress       = new \Magento\Framework\DataObject([
                                                                      'firstname' => 'test',
                                                                      'lastname'  => 'test',
                                                                      'postcode'  => '20704',
                                                                      'street1'   => 'test str. 287'
                                                                  ]);
        $items                = [];
        $orderPaymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $orderPaymentInstance->expects($this->any())->method('getOpsBrand')->will($this->returnValue('VISA'));
        $orderPayment = $this->getMock('Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $orderPayment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($orderPaymentInstance));
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())->method('getBillingAddress')->will($this->returnValue($billingAddress));
        $order->expects($this->any())->method('getShippingAddress')->will($this->returnValue(false));
        $order->expects($this->any())->method('getPayment')->will($this->returnValue($orderPayment));
        $order->expects($this->any())->method('getAllItems')->will($this->returnValue($items));
        $order->expects($this->any())->method('getShippingCarrier')->will($this->returnValue(1));
        $quote = new \Magento\Framework\DataObject();
        $this->dataHelper->expects($this->any())->method('getModuleVersionString')->will($this->returnValue('OGNM210'));
        $this->orderHelper->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $this->orderHelper->expects($this->any())->method('checkIfAddressesAreSame')->will($this->returnValue(1));
        $this->paymentRequestHelper->expects($this->any())
                                   ->method('getOwnerParams')
                                   ->will($this->returnValue(array_flip($this->getOwnerParams())));
        $this->config->expects($this->any())
                     ->method('getPaymentAction')
                     ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $this->config->expects($this->any())->method('canSubmitExtraParameter')->will($this->returnValue(true));
        $this->checkoutSession->expects($this->any())->method('getQuote')->will($this->returnValue($order));
        $this->customerSession->expects($this->any())->method('isLoggedIn')->will($this->returnValue(true));
        $this->customerSession->expects($this->any())->method('getCustomerId')->will($this->returnValue(1));
        $this->kwixoShippingSetting->setKwixoShippingMethodSpeed(5);
        $this->eavConfig->expects($this->any())
                        ->method('getAttribute')
                        ->will($this->returnValue(new \Magento\Framework\DataObject([
                                                                                        'source' => new \Magento\Framework\DataObject(['option_text' => 'Male'])
                                                                                    ])));
        $formFields = $this->model->getMethodDependendFormFields($order);
        $this->assertTrue(array_key_exists('CN', $formFields));
        $this->assertTrue(array_key_exists('OWNERZIP', $formFields));
        $this->assertTrue(array_key_exists('OWNERCTY', $formFields));
        $this->assertTrue(array_key_exists('OWNERTOWN', $formFields));
        $this->assertTrue(array_key_exists('COM', $formFields));
        $this->assertTrue(array_key_exists('OWNERTELNO', $formFields));
        $this->assertTrue(array_key_exists('OWNERADDRESS', $formFields));
        $this->assertTrue(array_key_exists('BRAND', $formFields));
        $this->assertTrue(array_key_exists('ADDMATCH', $formFields));
        $this->assertTrue(array_key_exists('ECOM_BILLTO_POSTAL_POSTALCODE', $formFields));
        $this->assertTrue(array_key_exists('CUID', $formFields));
        $this->assertTrue(array_key_exists('ECOM_ESTIMATEDELIVERYDATE', $formFields));
        $this->assertTrue(array_key_exists('RNPOFFERT', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPMETHODTYPE', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPMETHODSPEED', $formFields));
        $this->assertTrue(array_key_exists('ORDERID', $formFields));
    }

    public function testGetMethodDependendFormFieldsWithShipmentDetails()
    {
        $billingAddress       = new \Magento\Framework\DataObject([
                                                                      'firstname' => 'test',
                                                                      'lastname'  => 'test',
                                                                      'postcode'  => '20704',
                                                                      'street1'   => 'test str. 287'
                                                                  ]);
        $items                = [];
        $orderPaymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $orderPaymentInstance->expects($this->any())->method('getOpsBrand')->will($this->returnValue('VISA'));
        $orderPayment = $this->getMock('Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $orderPayment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($orderPaymentInstance));
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())->method('getBillingAddress')->will($this->returnValue($billingAddress));
        $order->expects($this->any())->method('getShippingAddress')->will($this->returnValue(false));
        $order->expects($this->any())->method('getPayment')->will($this->returnValue($orderPayment));
        $order->expects($this->any())->method('getAllItems')->will($this->returnValue($items));
        $order->expects($this->any())->method('getShippingCarrier')->will($this->returnValue(1));
        $quote = new \Magento\Framework\DataObject();
        $this->dataHelper->expects($this->any())->method('getModuleVersionString')->will($this->returnValue('OGNM210'));
        $this->orderHelper->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $this->orderHelper->expects($this->any())->method('checkIfAddressesAreSame')->will($this->returnValue(1));
        $this->paymentRequestHelper->expects($this->any())
                                   ->method('getOwnerParams')
                                   ->will($this->returnValue(array_flip($this->getOwnerParams())));
        $this->config->expects($this->any())
                     ->method('getPaymentAction')
                     ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $this->config->expects($this->any())->method('canSubmitExtraParameter')->will($this->returnValue(true));
        $this->checkoutSession->expects($this->any())->method('getQuote')->will($this->returnValue($order));
        $this->customerSession->expects($this->any())->method('isLoggedIn')->will($this->returnValue(true));
        $this->customerSession->expects($this->any())->method('getCustomerId')->will($this->returnValue(1));
        $this->kwixoShippingSetting->setKwixoShippingMethodSpeed(5);
        $this->kwixoShippingSetting->setKwixoShippingDetails('shipping method details');
        $this->eavConfig->expects($this->any())
                        ->method('getAttribute')
                        ->will($this->returnValue(new \Magento\Framework\DataObject([
                                                                                        'source' => new \Magento\Framework\DataObject(['option_text' => 'Male'])
                                                                                    ])));
        $formFields = $this->model->getMethodDependendFormFields($order);
        $this->assertArrayHasKey('ECOM_SHIPMETHODDETAILS', $formFields);
        $this->assertEquals(
            'shipping method details',
            $formFields['ECOM_SHIPMETHODDETAILS']
        );
    }

    public function testGetMethodDependendFormFieldsWithShipmentDetailsFromAddress()
    {
        $billingAddress       = new \Magento\Framework\DataObject([
                                                                      'firstname' => 'test',
                                                                      'lastname'  => 'test',
                                                                      'postcode'  => '20704',
                                                                      'street1'   => 'test str. 287'
                                                                  ]);
        $items                = [];
        $orderPaymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $orderPaymentInstance->expects($this->any())->method('getOpsBrand')->will($this->returnValue('VISA'));
        $orderPayment = $this->getMock('Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $orderPayment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($orderPaymentInstance));
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())->method('getBillingAddress')->will($this->returnValue($billingAddress));
        $order->expects($this->any())->method('getShippingAddress')->will($this->returnValue(false));
        $order->expects($this->any())->method('getPayment')->will($this->returnValue($orderPayment));
        $order->expects($this->any())->method('getAllItems')->will($this->returnValue($items));
        $order->expects($this->any())->method('getShippingCarrier')->will($this->returnValue(1));
        $quote = new \Magento\Framework\DataObject();
        $this->dataHelper->expects($this->any())->method('getModuleVersionString')->will($this->returnValue('OGNM210'));
        $this->orderHelper->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $this->orderHelper->expects($this->any())->method('checkIfAddressesAreSame')->will($this->returnValue(1));
        $this->paymentRequestHelper->expects($this->any())
                                   ->method('getOwnerParams')
                                   ->will($this->returnValue(array_flip($this->getOwnerParams())));
        $this->config->expects($this->any())
                     ->method('getPaymentAction')
                     ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $this->config->expects($this->any())->method('canSubmitExtraParameter')->will($this->returnValue(true));
        $this->checkoutSession->expects($this->any())->method('getQuote')->will($this->returnValue($order));
        $this->customerSession->expects($this->any())->method('isLoggedIn')->will($this->returnValue(true));
        $this->customerSession->expects($this->any())->method('getCustomerId')->will($this->returnValue(1));
        $this->kwixoShippingSetting->setKwixoShippingMethodSpeed(5);
        $this->kwixoShippingSetting->setKwixoShippingDetails('shipping method details');
        $this->eavConfig->expects($this->any())
                        ->method('getAttribute')
                        ->will($this->returnValue(new \Magento\Framework\DataObject([
                                                                                        'source' => new \Magento\Framework\DataObject(['option_text' => 'Male'])
                                                                                    ])));
        $formFields = $this->model->getMethodDependendFormFields($order);
        $this->assertArrayHasKey('ECOM_SHIPMETHODDETAILS', $formFields);
    }

    public function testGetMethodDependendFormFieldsCheckItemProductCateg()
    {
        $billingAddress = new \Magento\Framework\DataObject([
                                                                'firstname' => 'test',
                                                                'lastname'  => 'test',
                                                                'postcode'  => '20704',
                                                                'street1'   => 'test str. 287'
                                                            ]);
        $item1          = $this->objectManager->getObject('\Magento\Sales\Model\Order\Item');
        $item1->addData([
                            'parent_item' => false,
                            'name'        => 'abc'
                        ]);
        $item2 = $this->objectManager->getObject('\Magento\Sales\Model\Order\Item');
        $item2->addData([
                            'parent_item' => false,
                            'name'        => 'abcdef'
                        ]);
        $items                = [
            $item1,
            $item2
        ];
        $orderPaymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $orderPaymentInstance->expects($this->any())->method('getOpsBrand')->will($this->returnValue('VISA'));
        $orderPayment = $this->getMock('Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $orderPayment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($orderPaymentInstance));
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())->method('getBillingAddress')->will($this->returnValue($billingAddress));
        $order->expects($this->any())->method('getShippingAddress')->will($this->returnValue(false));
        $order->expects($this->any())->method('getPayment')->will($this->returnValue($orderPayment));
        $order->expects($this->any())->method('getAllItems')->will($this->returnValue($items));
        $order->expects($this->any())->method('getShippingCarrier')->will($this->returnValue(1));
        $quote = new \Magento\Framework\DataObject();
        $this->dataHelper->expects($this->any())->method('getModuleVersionString')->will($this->returnValue('OGNM210'));
        $this->orderHelper->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $this->orderHelper->expects($this->any())->method('checkIfAddressesAreSame')->will($this->returnValue(1));
        $this->paymentRequestHelper->expects($this->any())
                                   ->method('getOwnerParams')
                                   ->will($this->returnValue(array_flip($this->getOwnerParams())));
        $this->config->expects($this->any())
                     ->method('getPaymentAction')
                     ->will($this->returnValue(\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE));
        $this->config->expects($this->any())->method('canSubmitExtraParameter')->will($this->returnValue(true));
        $this->checkoutSession->expects($this->any())->method('getQuote')->will($this->returnValue($order));
        $this->customerSession->expects($this->any())->method('isLoggedIn')->will($this->returnValue(true));
        $this->customerSession->expects($this->any())->method('getCustomerId')->will($this->returnValue(1));
        $this->kwixoShippingSetting->setKwixoShippingMethodSpeed(5);
        $this->kwixoShippingSetting->setKwixoShippingDetails('shipping method details');
        $this->eavConfig->expects($this->any())
                        ->method('getAttribute')
                        ->will($this->returnValue(new \Magento\Framework\DataObject([
                                                                                        'source' => new \Magento\Framework\DataObject(['option_text' => 'Male'])
                                                                                    ])));
        $product = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false, false);
        $product->expects($this->any())->method('load')->will($this->returnSelf());
        $product->expects($this->any())->method('getCategoryIds')->will($this->returnValue([1]));
        $this->catalogProductFactory->expects($this->any())->method('create')->will($this->returnValue($product));
        $categoryMaping = $this->getMock('\Netresearch\OPS\Model\Kwixo\Category\Mapping', [], [], '', false, false);
        $categoryMaping->expects($this->any())->method('loadByCategoryId')->will($this->returnSelf());
        $categoryMaping->expects($this->any())->method('getKwixoCategoryId')->will($this->returnValue('test'));
        $categoryMaping->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->kwixoCategoryMappingFactory->expects($this->any())
                                          ->method('create')
                                          ->will($this->returnValue($categoryMaping));
        $formFields = $this->model->getMethodDependendFormFields($order);
        $this->assertTrue(array_key_exists('ITEMFDMPRODUCTCATEG1', $formFields));
        $this->assertTrue(array_key_exists('ITEMFDMPRODUCTCATEG2', $formFields));
    }

    public function testGetKwixoShipToParams()
    {
        $billingAddress  = new \Magento\Framework\DataObject([
                                                                 'firstname' => 'test',
                                                                 'lastname'  => 'test',
                                                                 'postcode'  => '20704',
                                                                 'street_1'  => 'test str. 287',
                                                                 'telephone' => '000-00-00',
                                                                 'prefix'    => 'dd'
                                                             ]);
        $shippingAddress = clone $billingAddress;
        $order           = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())->method('getBillingAddress')->will($this->returnValue($billingAddress));
        $order->expects($this->any())->method('getShippingAddress')->will($this->returnValue($shippingAddress));
        $this->kwixoShippingSetting->setKwixoShippingType(4);
        $formFields = $this->model->getKwixoShipToParams($order);
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_NAME_FIRST', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_NAME_LAST', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_NAME_PREFIX', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_TELECOM_PHONE_NUMBER', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_STREET_LINE1', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_STREET_NUMBER', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_POSTALCODE', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_CITY', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_COUNTRYCODE', $formFields));
        $shippingAddress->setStreet(['An der Tabaksmühle 3a', 'Etage 4']);
        $shippingAddress->setStreetLine([1 => 'An der Tabaksmühle 3a', 2 => 'Etage 4']);
        $formFields = $this->model->getKwixoShipToParams($order);
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_STREET_LINE2', $formFields));
        $this->assertEquals('Etage 4', $formFields['ECOM_SHIPTO_POSTAL_STREET_LINE2']);
        $shippingAddress->setCompany('My great company');
        $formFields = $this->model->getKwixoShipToParams($order);
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_COMPANY', $formFields));
        $this->assertEquals('My great company', $formFields['ECOM_SHIPTO_COMPANY']);
        $shippingAddress->setFax('4711');
        $formFields = $this->model->getKwixoShipToParams($order);
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_TELECOM_FAX_NUMBER', $formFields));
        $this->assertEquals('4711', $formFields['ECOM_SHIPTO_TELECOM_FAX_NUMBER']);
        $shippingAddress->setAddressType('shipping2');
        $formFields = $this->model->getKwixoShipToParams($order);
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_NAME_FIRST', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_NAME_LAST', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_NAME_PREFIX', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_TELECOM_PHONE_NUMBER', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_STREET_LINE1', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_STREET_NUMBER', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_POSTALCODE', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_CITY', $formFields));
        $this->assertTrue(array_key_exists('ECOM_SHIPTO_POSTAL_COUNTRYCODE', $formFields));
    }

    public function testGetKwixoBillToParams()
    {
        $billingAddress  = new \Magento\Framework\DataObject([
                                                                 'firstname' => 'test',
                                                                 'lastname'  => 'test',
                                                                 'postcode'  => '20704',
                                                                 'street_1'  => 'test str. 287',
                                                                 'telephone' => '000-00-00',
                                                                 'prefix'    => 'dd'
                                                             ]);
        $shippingAddress = clone $billingAddress;
        $order           = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())->method('getBillingAddress')->will($this->returnValue($billingAddress));
        $order->expects($this->any())->method('getShippingAddress')->will($this->returnValue($shippingAddress));
        $this->kwixoShippingSetting->setKwixoShippingType(4);
        $formFields = $this->model->getKwixoBillToParams($order);
        $this->assertTrue(array_key_exists('ECOM_BILLTO_POSTAL_NAME_FIRST', $formFields));
        $this->assertTrue(array_key_exists('ECOM_BILLTO_POSTAL_NAME_LAST', $formFields));
        $this->assertTrue(array_key_exists('ECOM_BILLTO_POSTAL_STREET_NUMBER', $formFields));
        $billingAddress->setStreetLine([1 => 'An der Tabaksmühle 3a', 2 => 'Etage 4']);
        $formFields = $this->model->getKwixoBillToParams($order);
        $this->assertTrue(array_key_exists('OWNERADDRESS2', $formFields));
        $this->assertEquals('Etage 4', $formFields['OWNERADDRESS2']);
    }

    public function testGetRnpFee()
    {
        $this->scopeConfig->expects($this->once())
                          ->method('getValue')
                          ->with('payment/ops_kwixoCredit/rnp_fee', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 1)
                          ->will($this->returnValue(1));
        $this->assertEquals(1, $this->model->getRnpFee('ops_kwixoCredit', 1));
    }

    public function testGetShippingMethodType()
    {
        $storeId = 1;
        $path    = 'payment/ops_kwixoCredit/ecom_shipMethodType';
        $this->assertEquals(
            \Netresearch\OPS\Model\Source\Kwixo\ShipMethodType::DOWNLOAD,
            $this->model->getShippingMethodType('ops_kwixoCredit', $storeId, true)
        );
        $this->scopeConfig->expects($this->once())
                          ->method('getValue')
                          ->with($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)
                          ->will($this->returnValue(\Netresearch\OPS\Model\Source\Kwixo\ShipMethodType::PICK_UP_AT_MERCHANT));
        $this->assertEquals(
            \Netresearch\OPS\Model\Source\Kwixo\ShipMethodType::PICK_UP_AT_MERCHANT,
            $this->model->getShippingMethodType('ops_kwixoCredit', $storeId)
        );
        $this->kwixoShippingSetting->setKwixoShippingType(\Netresearch\OPS\Model\Source\Kwixo\ShipMethodType::COLLECTION_POINT);
        $this->assertEquals(
            \Netresearch\OPS\Model\Source\Kwixo\ShipMethodType::COLLECTION_POINT,
            $this->model->getShippingMethodType('ops_kwixoCredit', $storeId)
        );
    }

    public function testGetShippingMethodSpeed()
    {
        $storeId = 1;
        $path    = 'payment/ops_kwixoCredit/ecom_shipMethodSpeed';
        $speed   = 10;
        $this->scopeConfig->expects($this->once())
                          ->method('getValue')
                          ->with($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)
                          ->will($this->returnValue($speed));
        $this->assertEquals($speed, $this->model->getShippingMethodSpeed('ops_kwixoCredit', $storeId));
        $this->kwixoShippingSetting->setKwixoShippingMethodSpeed($speed);
        $this->assertEquals($speed, $this->model->getShippingMethodSpeed('ops_kwixoCredit', $storeId));
    }

    public function testGetItemFmdProductCateg()
    {
        $path    = 'payment/ops_kwixoCredit/product_categories';
        $value   = implode(',', ['Cat1', 'Cat2', 'Cat3']);
        $storeId = 1;
        $this->scopeConfig->expects($this->once())
                          ->method('getValue')
                          ->with($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)
                          ->will($this->returnValue($value));
        $this->assertTrue(in_array('Cat1', $this->model->getItemFmdProductCateg('ops_kwixoCredit', $storeId)));
    }

    public function testGetShippingMethodDetails()
    {
        $path    = 'payment/ops_kwixoCredit/shiping_method_details';
        $value   = 'Shipping details';
        $storeId = 1;
        $this->scopeConfig->expects($this->once())
                          ->method('getValue')
                          ->with($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)
                          ->will($this->returnValue($value));
        $this->assertEquals($value, $this->model->getShippingMethodDetails('ops_kwixoCredit', $storeId));
    }

    public function testGetQuestion()
    {
        $this->assertEquals(
            __('Please make sure that the displayed data is correct.'),
            $this->model->getQuestion(new \Magento\Framework\DataObject, [])
        );
    }

    public function testGetQuestionedFormFields()
    {
        $storeId = 1;
        $order   = $this->getMock('\Magento\Sales\Model\Order', ['save'], [], '', false, false);
        $order->setStoreId($storeId);
        $fields = $this->model->getQuestionedFormFields($order, []);
        $this->assertTrue(in_array('OWNERADDRESS', $fields));
        $this->assertTrue(in_array('ECOM_BILLTO_POSTAL_STREET_NUMBER', $fields));
    }

    public function testGetQuestionedFormFieldsForAddrFields()
    {
        $storeId = 1;
        $order   = $this->getMock('\Magento\Sales\Model\Order', ['save'], [], '', false, false);
        $order->setStoreId($storeId);
        $this->kwixoShippingSetting->setKwixoShippingType(4);
        $fields = $this->model->getQuestionedFormFields($order, []);
        $this->assertTrue(in_array('OWNERADDRESS', $fields));
        $this->assertTrue(in_array('ECOM_BILLTO_POSTAL_STREET_NUMBER', $fields));
        $this->assertTrue(in_array('ECOM_SHIPTO_POSTAL_STREET_NUMBER', $fields));
        $this->assertTrue(in_array('ECOM_SHIPTO_TELECOM_PHONE_NUMBER', $fields));
    }

    public function testSplitHouseNumber()
    {
        $addressData = $this->model->splitHouseNumber('44,rue Parmentier');
        $this->assertEquals('44', $addressData['housenumber']);
        $this->assertEquals('rue Parmentier', $addressData['street']);
        $addressData = $this->model->splitHouseNumber('55, rue du Faubourg-Saint-Honoré');
        $this->assertEquals('55', $addressData['housenumber']);
    }

    public function testGetItemParams()
    {
        $item1 = $this->objectManager->getObject('\Magento\Sales\Model\Order\Item');
        $item1->addData([
                            'parent_item'         => false,
                            'name'                => 'abc',
                            'item_id'             => 1,
                            'base_price_incl_tax' => 10,
                            'qty_ordered'         => 2,
                            'base_tax_amount'     => 5
                        ]);
        $item2 = $this->objectManager->getObject('\Magento\Sales\Model\Order\Item');
        $item2->addData([
                            'parent_item'         => false,
                            'name'                => 'abcdef',
                            'item_id'             => 2,
                            'base_price_incl_tax' => 20,
                            'qty_ordered'         => 5,
                            'base_tax_amount'     => 5
                        ]);
        $items = [
            $item1,
            $item2
        ];
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())->method('getAllItems')->will($this->returnValue($items));
        $product = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false, false);
        $product->expects($this->any())->method('load')->will($this->returnSelf());
        $product->expects($this->any())->method('getCategoryIds')->will($this->returnValue([1]));
        $this->catalogProductFactory->expects($this->any())->method('create')->will($this->returnValue($product));
        $categoryMaping = $this->getMock('\Netresearch\OPS\Model\Kwixo\Category\Mapping', [], [], '', false, false);
        $categoryMaping->expects($this->any())->method('loadByCategoryId')->will($this->returnSelf());
        $categoryMaping->expects($this->any())->method('getKwixoCategoryId')->will($this->returnValue('test'));
        $categoryMaping->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->kwixoCategoryMappingFactory->expects($this->any())
                                          ->method('create')
                                          ->will($this->returnValue($categoryMaping));
        $formFields = $this->model->getItemParams($order);
        $orderItems = $order->getAllItems();
        $i          = 1;
        foreach ($orderItems as $orderItem) {
            $this->assertTrue(array_key_exists('ITEMID' . $i, $formFields));
            $this->assertEquals($orderItem->getItemId(), $formFields['ITEMID' . $i]);
            $this->assertTrue(array_key_exists('ITEMNAME' . $i, $formFields));
            $this->assertEquals($orderItem->getName(), $formFields['ITEMNAME' . $i]);
            $this->assertTrue(array_key_exists('ITEMPRICE' . $i, $formFields));
            $this->assertEquals($orderItem->getBasePriceInclTax(), $formFields['ITEMPRICE' . $i]);
            $this->assertTrue(array_key_exists('ITEMQUANT' . $i, $formFields));
            $this->assertEquals($orderItem->getQtyOrdered(), $formFields['ITEMQUANT' . $i]);
            $this->assertTrue(array_key_exists('ITEMVAT' . $i, $formFields));
            $this->assertEquals($orderItem->getBaseTaxAmount(), $formFields['ITEMVAT' . $i]);
            $i++;
        }
        $item1 = $this->objectManager->getObject('\Magento\Sales\Model\Order\Item');
        $item1->setParentItemId(1);
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())->method('getAllItems')->will($this->returnValue([$item1]));
        $formFields = $this->model->getItemParams($order);
        $this->assertArrayNotHasKey('ITEMID0', $formFields);
    }

    private function getOwnerParams()
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
}

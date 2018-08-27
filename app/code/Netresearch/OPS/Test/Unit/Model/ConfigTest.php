<?php
namespace Netresearch\OPS\Test\Unit\Model;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $model;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $modelMock;

    /**
     * @var \Magento\Framework\App\Config
     */
    private $config;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var \Magento\Backend\Model\UrlInterfaceFactory
     */
    private $backendUrlInterfaceFactory;

    /**
     * @var \Magento\Backend\Model\Url
     */
    private $backendUrl;

    /**
     * @var \Magento\Framework\Url
     */
    protected $urlBuilder;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->config        = $this->getMock('\Magento\Framework\App\Config', [], [], '', false, false);
        $this->store         = $this->getMock('\Magento\Store\Model\Store', [], [], '', false, false);
        $this->storeManager  = $this->getMock('\Magento\Store\Model\StoreManager', [], [], '', false, false);
        $this->storeManager->expects($this->any())->method('getStore')->will($this->returnValue($this->store));
        $this->backendUrl                 = $this->getMock('\Magento\Backend\Model\Url', [], [], '', false, false);
        $this->backendUrlInterfaceFactory = $this->getMock(
            '\Magento\Backend\Model\UrlInterfaceFactory',
            [],
            [],
            '',
            false,
            false
        );
        $this->backendUrlInterfaceFactory->expects($this->any())
                                         ->method('create')
                                         ->will($this->returnValue($this->backendUrl));
        $this->urlBuilder = $this->getMock('\Magento\Framework\Url', [], [], '', false, false);
        $this->model      = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Config',
            [
                                                                'scopeConfig'                => $this->config,
                                                                'storeManager'               => $this->storeManager,
                                                                'backendUrlInterfaceFactory' => $this->backendUrlInterfaceFactory,
                                                                'urlBuilder'                 => $this->urlBuilder
                                                            ]
        );
        $this->modelMock  = $this->getMock('\Netresearch\OPS\Model\Config', [], [], '', false, false);
    }

    public function testType()
    {
        $this->assertInstanceOf('\Netresearch\OPS\Model\Config', $this->model);
    }

    public function testGetIntersolveBrands()
    {
        $path    = 'payment/ops_interSolve/brands';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = 1;
        $value   = [
            ['brand' => '1234', 'value' => '1234'],
            ['brand' => '5678', 'value' => '5678'],
            ['brand' => '9012', 'value' => '9012'],
        ];
        $this->config->expects($this->exactly(2))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue(serialize($value)));
        $this->assertTrue(is_array($this->model->getIntersolveBrands($storeId)));
        $this->assertEquals(
            $value,
            $this->model->getIntersolveBrands($storeId)
        );
    }

    public function testGetInlinePaymentCcTypes()
    {
        $code            = 'ops_cc';
        $pathRedirectAll = 'payment/' . $code . '/redirect_all';
        $pathSpecific    = 'payment/' . $code . '/inline_types';
        $scope           = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $value           = ['foo', 'bar'];
        $this->config->expects($this->at(0))
                     ->method('getValue')
                     ->with($pathRedirectAll, $scope)
                     ->will($this->returnValue(1));
        $this->config->expects($this->at(1))
                     ->method('getValue')
                     ->with($pathRedirectAll, $scope)
                     ->will($this->returnValue(0));
        $this->config->expects($this->at(2))
                     ->method('getValue')
                     ->with($pathSpecific, $scope)
                     ->will($this->returnValue(implode(',', $value)));
        $this->assertEquals([], $this->model->getInlinePaymentCcTypes($code));
        $this->assertEquals($value, $this->model->getInlinePaymentCcTypes($code));
    }

    public function testGetGenerateHashUrl()
    {
        $storeId      = 1;
        $isSecure     = true;
        $isAdmin      = false;
        $routeBackend = 'adminhtml/alias/generatehash';
        $route        = \Netresearch\OPS\Model\Config::OPS_CONTROLLER_ROUTE_ALIAS . 'generatehash';
        $params       = [
            '_secure' => $isSecure,
            '_nosid'  => true,
            '_scope'  => $storeId
        ];
        $result       = 'foo';
        $this->store->expects($this->any())->method('isCurrentlySecure')->will($this->returnValue($isSecure));
        $this->urlBuilder->expects($this->any())
                         ->method('getUrl')
                         ->with($route, $params)
                         ->will($this->returnValue($result));
        $this->assertEquals($result, $this->model->getGenerateHashUrl($storeId, $isAdmin));
        $isAdmin             = true;
        $params['_nosecret'] = true;
        $this->backendUrl->expects($this->any())
                         ->method('getUrl')
                         ->with($routeBackend, $params)
                         ->will($this->returnValue($result));
        $this->assertEquals($result, $this->model->getGenerateHashUrl($storeId, $isAdmin));
    }

    public function testGetAliasAcceptUrl()
    {
        $storeId      = 1;
        $isSecure     = true;
        $isAdmin      = false;
        $routeBackend = 'adminhtml/alias/accept';
        $route        = \Netresearch\OPS\Model\Config::OPS_CONTROLLER_ROUTE_ALIAS . 'accept';
        $params       = [
            '_secure' => $isSecure,
            '_nosid'  => true,
            '_scope'  => $storeId
        ];
        $result       = 'foo';
        $this->store->expects($this->any())->method('isCurrentlySecure')->will($this->returnValue($isSecure));
        $this->urlBuilder->expects($this->any())
                         ->method('getUrl')
                         ->with($route, $params)
                         ->will($this->returnValue($result));
        $this->assertEquals($result, $this->model->getAliasAcceptUrl($storeId, $isAdmin));
        $isAdmin             = true;
        $params['_nosecret'] = true;
        $this->backendUrl->expects($this->any())
                         ->method('getUrl')
                         ->with($routeBackend, $params)
                         ->will($this->returnValue($result));
        $this->assertEquals($result, $this->model->getAliasAcceptUrl($storeId, $isAdmin));
    }

    public function testGetAliasExceptionUrl()
    {
        $storeId      = 1;
        $isSecure     = true;
        $isAdmin      = false;
        $routeBackend = 'adminhtml/alias/exception';
        $route        = \Netresearch\OPS\Model\Config::OPS_CONTROLLER_ROUTE_ALIAS . 'exception';
        $params       = [
            '_secure' => $isSecure,
            '_nosid'  => true,
            '_scope'  => $storeId
        ];
        $result       = 'foo';
        $this->store->expects($this->any())->method('isCurrentlySecure')->will($this->returnValue($isSecure));
        $this->urlBuilder->expects($this->any())
                         ->method('getUrl')
                         ->with($route, $params)
                         ->will($this->returnValue($result));
        $this->assertEquals($result, $this->model->getAliasExceptionUrl($storeId, $isAdmin));
        $isAdmin             = true;
        $params['_nosecret'] = true;
        $this->backendUrl->expects($this->any())
                         ->method('getUrl')
                         ->with($routeBackend, $params)
                         ->will($this->returnValue($result));
        $this->assertEquals($result, $this->model->getAliasExceptionUrl($storeId, $isAdmin));
    }

    public function testGetCcSaveAliasUrl()
    {
        $storeId      = 1;
        $isSecure     = true;
        $isAdmin      = false;
        $routeBackend = 'ops/admin/saveAlias';
        $route        = 'ops/alias/save';
        $params       = [
            '_secure' => $isSecure,
            '_scope'  => $storeId
        ];
        $result       = 'foo';
        $this->store->expects($this->any())->method('isCurrentlySecure')->will($this->returnValue($isSecure));
        $this->urlBuilder->expects($this->any())
                         ->method('getUrl')
                         ->with($route, $params)
                         ->will($this->returnValue($result));
        $this->assertEquals($result, $this->model->getCcSaveAliasUrl($storeId, $isAdmin));
        $isAdmin = true;
        $this->backendUrl->expects($this->any())
                         ->method('getUrl')
                         ->with($routeBackend, $params)
                         ->will($this->returnValue($result));
        $this->assertEquals($result, $this->model->getCcSaveAliasUrl($storeId, $isAdmin));
    }

    public function testIsAliasInfoBlockEnabled()
    {
        $path  = 'payment/ops_cc/show_alias_manager_info_for_guests';
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $value = 1;
        $this->config->expects($this->once())->method('getValue')->with($path, $scope)->will($this->returnValue($value));
        $this->assertTrue($this->model->isAliasInfoBlockEnabled());
    }

    public function testGetOrderReference()
    {
        $path    = \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH . 'redirectOrderReference';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = 1;
        $this->config->expects($this->exactly(2))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->onConsecutiveCalls(
                         \Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_ORDER_ID,
                         \Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_QUOTE_ID
                     ));
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_ORDER_ID,
            $this->model->getOrderReference($storeId)
        );
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_QUOTE_ID,
            $this->model->getOrderReference($storeId)
        );
    }

    public function testGetShowQuoteIdInOrderGrid()
    {
        $path    = \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH . 'showQuoteIdInOrderGrid';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = 1;
        $this->config->expects($this->exactly(2))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->onConsecutiveCalls(1, 0));
        $this->assertEquals(1, $this->model->getShowQuoteIdInOrderGrid($storeId));
        $this->assertEquals(0, $this->model->getShowQuoteIdInOrderGrid($storeId));
    }

    public function testIsAliasManagerEnabled()
    {
        $path    = 'payment/ops_cc/active_alias';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = 1;
        $this->config->expects($this->exactly(2))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->onConsecutiveCalls(0, 1));
        $this->assertFalse($this->model->isAliasManagerEnabled('ops_cc', $storeId));
        $this->assertTrue($this->model->isAliasManagerEnabled('ops_cc', $storeId));
    }


    public function testGetAcceptRedirectLocation()
    {
        $this->assertEquals(
            \Netresearch\OPS\Model\Config::OPS_CONTROLLER_ROUTE_PAYMENT . 'accept',
            $this->model->getAcceptRedirectRoute()
        );
    }

    public function testGetCancelRedirectLocation()
    {
        $this->assertEquals(
            \Netresearch\OPS\Model\Config::OPS_CONTROLLER_ROUTE_PAYMENT . 'cancel',
            $this->model->getCancelRedirectRoute()
        );
    }

    public function testGetDeclineRedirectLocation()
    {
        $this->assertEquals(
            \Netresearch\OPS\Model\Config::OPS_CONTROLLER_ROUTE_PAYMENT . 'decline',
            $this->model->getDeclineRedirectRoute()
        );
    }

    public function testGetExceptionRedirectLocation()
    {
        $this->assertEquals(
            \Netresearch\OPS\Model\Config::OPS_CONTROLLER_ROUTE_PAYMENT . 'exception',
            $this->model->getExceptionRedirectRoute()
        );
    }

    public function testGetMethodsRequiringAdditionalParametersFor()
    {
        $path    = \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH . 'additional_params_required/capture';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = null;
        $value   = ['foo' => 'bar'];
        $this->config->expects($this->once())
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->assertEquals($value, $this->model->getMethodsRequiringAdditionalParametersFor('capture'));
    }

    public function testGetIdealIssuers()
    {
        $path  = 'payment/ops_iDeal/issuer';
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $value = ['foo' => 'bar'];
        $this->config->expects($this->once())->method('getValue')->with($path, $scope)->will($this->returnValue($value));
        $this->assertEquals($value, $this->model->getIDealIssuers());
    }

    public function testCanSubmitExtraParameters()
    {
        $path    = 'payment_services/ops/submitExtraParameters';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = 1;
        $this->config->expects($this->exactly(2))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->onConsecutiveCalls(0, 1));
        $this->assertFalse($this->model->canSubmitExtraParameter($storeId));
        $this->assertTrue($this->model->canSubmitExtraParameter($storeId));
    }

    public function testGetParameterLengths()
    {
        $path    = \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH . 'paramLength';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = null;
        $value   = $this->validFieldLengths();
        $this->config->expects($this->once())
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->assertEquals($this->validFieldLengths(), $this->model->getParameterLengths());
    }

    protected function validFieldLengths()
    {
        return [
            'ECOM_SHIPTO_POSTAL_NAME_FIRST'    => 50,
            'ECOM_SHIPTO_POSTAL_NAME_LAST'     => 50,
            'ECOM_SHIPTO_POSTAL_STREET_LINE1'  => 35,
            'ECOM_SHIPTO_POSTAL_STREET_LINE2'  => 35,
            'ECOM_SHIPTO_POSTAL_STREET_LINE3'  => 35,
            'ECOM_SHIPTO_POSTAL_COUNTRYCODE'   => 2,
            'ECOM_SHIPTO_POSTAL_COUNTY'        => 25,
            'ECOM_SHIPTO_POSTAL_POSTALCODE'    => 10,
            'ECOM_SHIPTO_POSTAL_CITY'          => 25,
            'ECOM_SHIPTO_POSTAL_STREET_NUMBER' => 10,
            'CN'                               => 35,
            'OWNERZIP'                         => 10,
            'OWNERCTY'                         => 2,
            'OWNERTOWN'                        => 40,
            'OWNERTELNO'                       => 30,
            'OWNERADDRESS'                     => 35,
            'ECOM_BILLTO_POSTAL_POSTALCODE'    => 10,
        ];
    }

    public function testGetInlineOrderReference()
    {
        $path    = 'payment_services/ops/inlineOrderReference';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = 1;
        $this->config->expects($this->exactly(2))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->onConsecutiveCalls(
                         \Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_ORDER_ID,
                         \Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_QUOTE_ID
                     ));
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_ORDER_ID,
            $this->model->getInlineOrderReference($storeId)
        );
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_QUOTE_ID,
            $this->model->getInlineOrderReference($storeId)
        );
    }

    public function testGetFrontendGatewayPath()
    {
        $path    = 'payment_services/ops/mode';
        $path2   = 'payment_services/ops/frontend_gateway';
        $path3   = 'payment_services/ops/url/base_prod';
        $path4   = 'payment_services/ops/url/frontend_gateway';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = null;
        $value   = 'foo';
        $value2  = 'bar';
        $this->config->expects($this->at(0))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue(\Netresearch\OPS\Model\Source\Mode::CUSTOM));
        $this->config->expects($this->at(1))
                     ->method('getValue')
                     ->with($path2, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->config->expects($this->at(3))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue(\Netresearch\OPS\Model\Source\Mode::PROD));
        $this->config->expects($this->at(4))
                     ->method('getValue')
                     ->with($path3, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->config->expects($this->at(5))
                     ->method('getValue')
                     ->with($path4, $scope, $storeId)
                     ->will($this->returnValue($value2));
        $this->assertEquals($value, $this->model->getFrontendGatewayPath($storeId));
        $this->assertEquals($value . $value2, $this->model->getFrontendGatewayPath($storeId));
    }

    public function testGetDirectLinkGatewayPath()
    {
        $path    = 'payment_services/ops/mode';
        $path2   = 'payment_services/ops/directlink_gateway';
        $path3   = 'payment_services/ops/url/base_prod';
        $path4   = 'payment_services/ops/url/directlink_gateway';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = null;
        $value   = 'foo';
        $value2  = 'bar';
        $this->config->expects($this->at(0))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue(\Netresearch\OPS\Model\Source\Mode::CUSTOM));
        $this->config->expects($this->at(1))
                     ->method('getValue')
                     ->with($path2, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->config->expects($this->at(3))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue(\Netresearch\OPS\Model\Source\Mode::PROD));
        $this->config->expects($this->at(4))
                     ->method('getValue')
                     ->with($path3, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->config->expects($this->at(5))
                     ->method('getValue')
                     ->with($path4, $scope, $storeId)
                     ->will($this->returnValue($value2));
        $this->assertEquals($value, $this->model->getDirectLinkGatewayPath($storeId));
        $this->assertEquals($value . $value2, $this->model->getDirectLinkGatewayPath($storeId));
    }

    public function testGetDirectLinkGatewayOrderPath()
    {
        $path    = 'payment_services/ops/mode';
        $path2   = 'payment_services/ops/directlink_gateway_order';
        $path3   = 'payment_services/ops/url/base_prod';
        $path4   = 'payment_services/ops/url/directlink_gateway_order';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = null;
        $value   = 'foo';
        $value2  = 'bar';
        $this->config->expects($this->at(0))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue(\Netresearch\OPS\Model\Source\Mode::CUSTOM));
        $this->config->expects($this->at(1))
                     ->method('getValue')
                     ->with($path2, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->config->expects($this->at(3))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue(\Netresearch\OPS\Model\Source\Mode::PROD));
        $this->config->expects($this->at(4))
                     ->method('getValue')
                     ->with($path3, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->config->expects($this->at(5))
                     ->method('getValue')
                     ->with($path4, $scope, $storeId)
                     ->will($this->returnValue($value2));
        $this->assertEquals($value, $this->model->getDirectLinkGatewayOrderPath($storeId));
        $this->assertEquals($value . $value2, $this->model->getDirectLinkGatewayOrderPath($storeId));
    }

    public function testGetAliasGatewayUrl()
    {
        $path    = 'payment_services/ops/mode';
        $path2   = 'payment_services/ops/ops_alias_gateway';
        $path3   = 'payment_services/ops/ops_alias_gateway_test';
        $path4   = 'payment_services/ops/url/base_prod';
        $path5   = 'payment_services/ops/url/ops_alias_gateway';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = null;
        $value   = 'foo';
        $value2  = 'bar';
        $this->config->expects($this->at(0))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue(\Netresearch\OPS\Model\Source\Mode::CUSTOM));
        $this->config->expects($this->at(1))
                     ->method('getValue')
                     ->with($path2, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->config->expects($this->at(2))
                     ->method('getValue')
                     ->with($path3, $scope, $storeId)
                     ->will($this->returnValue(''));
        $this->config->expects($this->at(4))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue(\Netresearch\OPS\Model\Source\Mode::PROD));
        $this->config->expects($this->at(5))
                     ->method('getValue')
                     ->with($path4, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->config->expects($this->at(6))
                     ->method('getValue')
                     ->with($path5, $scope, $storeId)
                     ->will($this->returnValue('ncol/prod/' . $value2));
        $this->config->expects($this->at(7))
                     ->method('getValue')
                     ->with($path3, $scope, $storeId)
                     ->will($this->returnValue('a'));
        $this->config->expects($this->at(9))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue(\Netresearch\OPS\Model\Source\Mode::PROD));
        $this->assertEquals($value, $this->model->getAliasGatewayUrl($storeId));
        $this->assertEquals($value . $value2, $this->model->getAliasGatewayUrl($storeId));
    }

    public function testGetDirectLinkMaintenanceApiPath()
    {
        $path    = 'payment_services/ops/mode';
        $path2   = 'payment_services/ops/directlink_maintenance_api';
        $path3   = 'payment_services/ops/url/base_prod';
        $path4   = 'payment_services/ops/url/directlink_maintenance_api';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = null;
        $value   = 'foo';
        $value2  = 'bar';
        $this->config->expects($this->at(0))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue(\Netresearch\OPS\Model\Source\Mode::CUSTOM));
        $this->config->expects($this->at(1))
                     ->method('getValue')
                     ->with($path2, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->config->expects($this->at(3))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue(\Netresearch\OPS\Model\Source\Mode::PROD));
        $this->config->expects($this->at(4))
                     ->method('getValue')
                     ->with($path3, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->config->expects($this->at(5))
                     ->method('getValue')
                     ->with($path4, $scope, $storeId)
                     ->will($this->returnValue($value2));
        $this->assertEquals($value, $this->model->getDirectLinkMaintenanceApiPath($storeId));
        $this->assertEquals($value . $value2, $this->model->getDirectLinkMaintenanceApiPath($storeId));
    }

    public function testGetMode()
    {
        $path    = \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH . 'mode';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = 1;
        $value   = \Netresearch\OPS\Model\Source\Mode::CUSTOM;
        $this->config->expects($this->once())
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->assertEquals($value, $this->model->getMode($storeId));
    }

    public function testGetResendPaymentInfoTemplate()
    {
        $path    = \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH . 'resendPaymentInfo_template';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = 1;
        $value   = 'ayment_services_ops_resendPaymentInfo_template';
        $this->config->expects($this->once())
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->assertEquals($value, $this->model->getResendPaymentInfoTemplate($storeId));
    }

    public function testGetResendPaymentInfoIdentity()
    {
        $path    = \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH . 'resendPaymentInfo_identity';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = 1;
        $value   = 'sales';
        $this->config->expects($this->once())
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->assertEquals($value, $this->model->getResendPaymentInfoIdentity($storeId));
    }

    public function testGetOpsBaseUrl()
    {
        $path    = 'payment_services/ops/mode';
        $path2   = 'payment_services/ops/url/base_prod';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = null;
        $value   = 'foo';
        $this->config->expects($this->at(0))
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue(\Netresearch\OPS\Model\Source\Mode::PROD));
        $this->config->expects($this->at(1))
                     ->method('getValue')
                     ->with($path2, $scope, $storeId)
                     ->will($this->returnValue($value));
        $this->assertEquals($value, $this->model->getOpsBaseUrl($storeId));
    }

    public function testGetAllRecurringCcTypes()
    {
        $path  = 'payment/ops_recurring_cc/availableTypes';
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $value = ['American Express', 'Diners Club', 'MaestroUK', 'MasterCard', 'VISA', 'JCB'];
        $this->config->expects($this->once())
                     ->method('getValue')
                     ->with($path, $scope)
                     ->will($this->returnValue(implode(',', $value)));
        $this->assertEquals($value, $this->model->getAllRecurringCcTypes());
    }

    public function testGetAcceptedRecurringCcTypes()
    {
        $path  = 'payment/ops_recurring_cc/acceptedTypes';
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $value = ['American Express', 'Diners Club', 'MaestroUK', 'MasterCard', 'VISA', 'JCB'];
        $this->config->expects($this->once())
                     ->method('getValue')
                     ->with($path, $scope)
                     ->will($this->returnValue(implode(',', $value)));
        $this->assertEquals($value, $this->model->getAcceptedRecurringCcTypes());
    }

    public function testGetDeviceFingerPrinting()
    {
        $path    = \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH . 'device_fingerprinting';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = 1;
        $value   = 1;
        $this->config->expects($this->once())
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue($value));
        $res = $this->model->getDeviceFingerPrinting($storeId);
        $this->assertTrue(is_bool($res));
        $this->assertTrue($res);
    }

    public function testGetTransActionTimeout()
    {
        $path    = \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH . 'ops_rtimeout';
        $scope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = 1;
        $value   = '40';
        $this->config->expects($this->once())
                     ->method('getValue')
                     ->with($path, $scope, $storeId)
                     ->will($this->returnValue($value));
        $res = $this->model->getTransActionTimeout($storeId);
        $this->assertTrue(is_int($res));
        $this->assertEquals($value, $res);
    }
}

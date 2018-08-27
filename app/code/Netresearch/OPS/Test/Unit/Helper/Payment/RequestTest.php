<?php
namespace Netresearch\OPS\Test\Unit\Helper\Payment;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Helper\Payment\Request
     */
    private $helper;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Quote\Model\Quote\Address
     */
    private $shippingAddress;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager   = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->shippingAddress = $this->getMock('\Magento\Quote\Model\Quote\Address', [], [], '', false, false);
        $this->shippingAddress->expects($this->any())->method('getCity')->will($this->returnValue('Leipzig'));
        $this->shippingAddress->expects($this->any())->method('getPostcode')->will($this->returnValue('04229'));
        $this->shippingAddress->expects($this->any())->method('getFirstname')->will($this->returnValue('Hans'));
        $this->shippingAddress->expects($this->any())->method('getLastname')->will($this->returnValue('Wurst'));
        $this->shippingAddress->expects($this->any())->method('getStreetLine')->will($this->returnCallback(function () {
            $arg = func_get_arg(0);
            if ($arg == 1) {
                return ['Nonnenstrasse 11d'];
            } else {
                return [''];
            }
        }));
        $this->quote = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false, false);
        $this->quote->expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($this->shippingAddress));

        $this->config = $this->getMock('\Netresearch\OPS\Model\Config', [], [], '', false, false);
        $this->config->expects($this->any())->method('getInlineOrderReference')->will($this->returnValue('orderId'));
        $this->config->expects($this->any())->method('getOrderReference')->will($this->returnValue('orderId'));
        $this->config->expects($this->any())->method('getCreditDebitSplit')->will($this->returnValue(true));
        $region = $this->getMock('\Magento\Directory\Model\Region', [], [], '', false, false);
        $regionFactory = $this->getMock('\Magento\Directory\Model\RegionFactory', [], [], '', false, false);
        $regionFactory->expects($this->any())->method('create')->will($this->returnValue($region));
        $taxCalculation = $this->getMock('Magento\Tax\Model\Calculation', [], [], '', false, false);
        $taxCalculation->expects($this->any())
            ->method('getRateRequest')
            ->will($this->returnValue(new \Magento\Framework\DataObject()));
        $taxCalculation->expects($this->any())->method('getRate')->will($this->returnValue(0));
        $taxCalculationFactory = $this->getMock('Magento\Tax\Model\CalculationFactory', [], [], '', false, false);
        $taxCalculationFactory->expects($this->any())->method('create')->will($this->returnValue($taxCalculation));

        $this->helper = $this->objectManager->getObject(
            '\Netresearch\OPS\Helper\Payment\Request',
            [
                'regionFactory'         => $regionFactory,
                'taxCalculationFactory' => $taxCalculationFactory,
            ]
        );
        $this->helper->setConfig($this->config);
    }

    public function testExtractShipToParameters()
    {
        $this->helper->getConfig()
                     ->expects($this->any())
                     ->method('canSubmitExtraParameter')
                     ->will($this->returnValue(true));

        $this->shippingAddress
            ->expects($this->any())
            ->method('getCountry')
            ->will($this->returnValue('DE'));

        $this->shippingAddress
            ->expects($this->any())
            ->method('getRegion')
            ->will($this->returnValue(''));

        $this->shippingAddress
            ->expects($this->any())
            ->method('getStreet')
            ->will($this->returnValue(['Nonnenstrasse 11d']));

        $params = $this->helper->extractShipToParameters($this->shippingAddress, $this->quote);
        $this->assertTrue(is_array($params));

        foreach ($this->getShipToArrayKeys() as $key) {
            $this->assertArrayHasKey($key, $params);
        }

        $this->assertEquals('Hans', $params['ECOM_SHIPTO_POSTAL_NAME_FIRST']);
        $this->assertEquals('Wurst', $params['ECOM_SHIPTO_POSTAL_NAME_LAST']);
        $this->assertEquals('Nonnenstrasse', $params['ECOM_SHIPTO_POSTAL_STREET_LINE1']);
        $this->assertEquals('11d', $params['ECOM_SHIPTO_POSTAL_STREET_NUMBER']);
        $this->assertEquals('', $params['ECOM_SHIPTO_POSTAL_STREET_LINE2']);
        $this->assertEquals('DE', $params['ECOM_SHIPTO_POSTAL_COUNTRYCODE']);
        $this->assertEquals('Leipzig', $params['ECOM_SHIPTO_POSTAL_CITY']);
        $this->assertEquals('04229', $params['ECOM_SHIPTO_POSTAL_POSTALCODE']);
    }

    public function testGetIsoRegionCodeWithIsoRegionCode()
    {
        $this->shippingAddress->expects($this->any())->method('getCountryId')->will($this->returnValue('DE'));
        $this->shippingAddress->expects($this->any())->method('getRegion')->will($this->returnValue('SN'));
        $this->assertEquals('SN', $this->helper->getIsoRegionCode($this->shippingAddress));
    }

    public function testGetIsoRegionCodeWithIsoRegionCodeContainingTheCountryCode()
    {
        $this->shippingAddress->expects($this->any())->method('getCountryId')->will($this->returnValue('ES'));
        $this->shippingAddress->expects($this->any())->method('getRegion')->will($this->returnValue('ES-AB'));
        $this->assertEquals('AB', $this->helper->getIsoRegionCode($this->shippingAddress));
    }

    public function testGetIsoRegionCodeWithGermanMageRegionCode()
    {
        $this->shippingAddress = new \Magento\Framework\DataObject();
        ;
        $this->shippingAddress->setRegion('SAS');
        $this->shippingAddress->setCountryId('DE');
        $this->assertEquals('SN', $this->helper->getIsoRegionCode($this->shippingAddress));
        $this->shippingAddress->setRegion('NDS');
        $this->assertEquals('NI', $this->helper->getIsoRegionCode($this->shippingAddress));
        $this->shippingAddress->setRegion('THE');
        $this->assertEquals('TH', $this->helper->getIsoRegionCode($this->shippingAddress));
    }

    public function testGetIsoRegionCodeWithAustrianMageRegionCode()
    {
        $this->shippingAddress = new \Magento\Framework\DataObject();
        $this->shippingAddress->setRegion('WI');
        $this->shippingAddress->setCountryId('AT');
        $this->assertEquals('9', $this->helper->getIsoRegionCode($this->shippingAddress));
        $this->shippingAddress->setRegion('NO');
        $this->assertEquals('3', $this->helper->getIsoRegionCode($this->shippingAddress));
        $this->shippingAddress->setRegion('VB');
        $this->assertEquals('8', $this->helper->getIsoRegionCode($this->shippingAddress));
    }

    public function testGetIsoRegionCodeWithSpanishMageRegionCode()
    {
        $this->shippingAddress = new \Magento\Framework\DataObject();
        $this->shippingAddress->setRegion('A Coruсa');
        $this->shippingAddress->setCountryId('ES');
        $this->assertEquals('C', $this->helper->getIsoRegionCode($this->shippingAddress));
        $this->shippingAddress->setRegion('Barcelona');
        $this->assertEquals('B', $this->helper->getIsoRegionCode($this->shippingAddress));
        $this->shippingAddress->setRegion('Madrid');
        $this->assertEquals('M', $this->helper->getIsoRegionCode($this->shippingAddress));
    }

    public function testGetIsoRegionCodeWithFinnishMageRegionCode()
    {
        $this->shippingAddress = new \Magento\Framework\DataObject();
        $this->shippingAddress->setRegion('Lappi');
        $this->shippingAddress->setCountryId('FI');
        $this->assertEquals('10', $this->helper->getIsoRegionCode($this->shippingAddress));
        $this->shippingAddress->setRegion('Etelä-Savo');
        $this->assertEquals('04', $this->helper->getIsoRegionCode($this->shippingAddress));
        $this->shippingAddress->setRegion('Itä-Uusimaa');
        $this->assertEquals('19', $this->helper->getIsoRegionCode($this->shippingAddress));
    }

    public function testGetIsoRegionCodeWithLatvianMageRegionCode()
    {
        $this->shippingAddress = new \Magento\Framework\DataObject();
        $this->shippingAddress->setRegion('Ādažu novads');
        $this->shippingAddress->setCountryId('LV');
        $this->assertEquals('LV', $this->helper->getIsoRegionCode($this->shippingAddress));
        $this->shippingAddress->setRegion('Engures novads');
        $this->assertEquals('029', $this->helper->getIsoRegionCode($this->shippingAddress));
        $this->shippingAddress->setRegion('Viļakas novads');
        $this->assertEquals('108', $this->helper->getIsoRegionCode($this->shippingAddress));
    }

    public function testGetIsoRegionCodeWithUnknownRegionCode()
    {
        $this->shippingAddress = new \Magento\Framework\DataObject();
        $this->shippingAddress->setRegion('DEFG');
        $this->shippingAddress->setCountryId('AB');
        $this->assertEquals('AB', $this->helper->getIsoRegionCode($this->shippingAddress));
        $this->shippingAddress->setRegion('DEF');
        $this->assertEquals('DEF', $this->helper->getIsoRegionCode($this->shippingAddress));
        $this->shippingAddress->setRegion('DF');
        $this->assertEquals('DF', $this->helper->getIsoRegionCode($this->shippingAddress));
    }

    public function testGetTemplateParamsIframeMode()
    {
        $this->config->expects($this->any())
                     ->method('getConfigData')
                     ->will($this->returnValue(\Netresearch\OPS\Model\Payment\PaymentAbstract::TEMPLATE_OPS_IFRAME));
        $params = $this->helper->getTemplateParams();
        $this->assertArrayHasKey('PARAMPLUS', $params);
        $this->assertEquals('IFRAME=1', $params['PARAMPLUS']);
        $this->assertArrayNotHasKey('TITLE', $params);
        $this->assertArrayNotHasKey('TP', $params);
    }

    public function testGetTemplateParamsNoMode()
    {
        $this->config->expects($this->any())->method('getConfigData')->will($this->returnValue(null));
        $params = $this->helper->getTemplateParams();
        $this->assertArrayNotHasKey('PARAMPLUS', $params);
        $this->assertArrayNotHasKey('TITLE', $params);
        $this->assertArrayNotHasKey('TP', $params);
    }

    public function testGetTemplateParamsRedirectMode()
    {
        $this->config->expects($this->any())
                     ->method('getConfigData')
                     ->will($this->returnValue(\Netresearch\OPS\Model\Payment\PaymentAbstract::TEMPLATE_OPS_REDIRECT));
        $params = $this->helper->getTemplateParams();
        $this->assertArrayNotHasKey('PARAMPLUS', $params);
        $this->assertArrayHasKey('TITLE', $params);
        $this->assertArrayNotHasKey('TP', $params);
    }

    public function testExtractOrderItemParametersWithAllItems()
    {
        // setup one item
        $item = new \Magento\Framework\DataObject();
        $item->setId(1);
        $item->setItemId(1);
        $item->setName('Item');
        $item->setBasePriceInclTax(10.00);
        $item->setQtyOrdered(1);
        $item->setTaxPercent(19.00);
        $item->setProductType(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', ['getAllItems', 'getStore'], [], '', false, false);
        $order->expects($this->any())->method('getAllItems')->will($this->returnValue([$item]));
        $order->expects($this->any())->method('getStore')->will($this->returnValue(1));
        // prepare discount item
        $order->setBaseDiscountAmount(1.00);
        $order->setCouponRuleName('DISCOUNT');
        //prepare shipping Item
        $order->setShippingDescription('SHIPPING');
        $order->setBaseShippingInclTax(5.00);
        $order->setIsVirtual(0);
        $formFields = $this->helper->extractOrderItemParameters($order);
        $this->assertArrayHasKey('ITEMID1', $formFields);
        $this->assertArrayHasKey('ITEMID2', $formFields);
        $this->assertArrayHasKey('ITEMID3', $formFields);
    }

    public function testExtractOrderItemParametersWithNoItems()
    {
//        $sessionMock = $this->getModelMockBuilder('customer/session')
//            ->disableOriginalConstructor()
//            ->setMethods(null)
//            ->getMock();
//        $this->replaceByMock('singleton', 'customer/session', $sessionMock);
        // setup one item
        $item = new \Magento\Framework\DataObject();
        $item->setId(1);
        $item->setItemId(1);
        $item->setName('Item');
        $item->setBasePriceInclTax(10.00);
        $item->setQtyOrdered(1);
        $item->setTaxPercent(19.00);
        $item->setProductType(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', ['getAllItems', 'getStore'], [], '', false, false);
        $order->expects($this->any())->method('getAllItems')->will($this->returnValue([$item]));
        $order->expects($this->any())->method('getStore')->will($this->returnValue(1));
        // prepare discount item
        $order->setBaseDiscountAmount(0.00);
        //prepare shipping Item
        $order->setIsVirtual(true);
        $formFields = $this->helper->extractOrderItemParameters($order);
        $this->assertArrayNotHasKey('ITEMID1', $formFields);
        $this->assertArrayNotHasKey('ITEMID2', $formFields);
        $this->assertArrayNotHasKey('ITEMID3', $formFields);
    }

    protected function getShipToArrayKeys()
    {
        return [
            'ECOM_SHIPTO_POSTAL_NAME_FIRST',
            'ECOM_SHIPTO_POSTAL_NAME_LAST',
            'ECOM_SHIPTO_POSTAL_STREET_LINE1',
            'ECOM_SHIPTO_POSTAL_STREET_LINE2',
            'ECOM_SHIPTO_POSTAL_COUNTRYCODE',
            'ECOM_SHIPTO_POSTAL_CITY',
            'ECOM_SHIPTO_POSTAL_POSTALCODE',
            'ECOM_SHIPTO_POSTAL_STATE',
        ];
    }
}

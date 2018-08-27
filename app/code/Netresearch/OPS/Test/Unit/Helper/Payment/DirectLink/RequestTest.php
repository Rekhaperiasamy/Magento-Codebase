<?php

namespace Netresearch\OPS\Test\Unit\Helper\Payment\DirectLink;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Helper\Creditcard
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

    public function setUp()
    {
        parent::setUp();

        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $this->order->expects($this->any())
                    ->method('getId')
                    ->will($this->returnValue(11));
        $this->order->expects($this->any())
                    ->method('getBaseGrandTotal')
                    ->will($this->returnValue(119.00));
        $this->order->expects($this->any())
            ->method('getBillingAddress')
            ->will($this->returnValue($this->getMock('Magento\Customer\Model\Address\AbstractAddress', [], [], '', false, false)));
        $this->order->expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($this->getMock('Magento\Customer\Model\Address\AbstractAddress', [], [], '', false, false)));

        $this->payment = new \Magento\Framework\DataObject();
        $this->payment->setOrder($this->order);
        $this->payment->setMethodInstance($this->getMock('\Netresearch\OPS\Model\Payment\DirectLink', [], [], '', false, false));

        $this->quote = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false, false);
        $this->quote->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($this->payment));

        $this->config = $this->getMock('\Netresearch\OPS\Model\Config', [], [], '', false, false);
        $this->config->expects($this->any())
                     ->method('getInlineOrderReference')
                     ->will($this->returnValue('orderId'));
        $this->config->expects($this->any())
                     ->method('getOrderReference')
                     ->will($this->returnValue('orderId'));
        $this->config->expects($this->any())
                     ->method('getCreditDebitSplit')
                     ->will($this->returnValue(true));

        $orderHelper = $this->getMock('\Netresearch\OPS\Helper\Order', [], [], '', false, false);
        $orderHelper->expects($this->any())
            ->method('getOpsOrderId')
            ->will($this->returnValue(10000011));
        $orderHelper->expects($this->any())
                    ->method('checkIfAddressesAreSame')
                    ->will($this->returnValue(false));

        $alias = $this->objectManager->getObject('\Netresearch\OPS\Model\Alias');
        $opsAliasfactory = $this->getMock('\Netresearch\OPS\Model\AliasFactory', [], [], '', false, false);
        $opsAliasfactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($alias));

        $this->helper = $this->objectManager->getObject(
            'Netresearch\OPS\Helper\Creditcard',
            [
                'oPSAliasFactory' =>  $opsAliasfactory
            ]
        );
        $this->helper->setConfig($this->config);
        $this->helper->setOrderHelper($orderHelper);
        $this->helper->setRequestHelper($this->getMock('\Netresearch\OPS\Helper\Payment\Request', [], [], '', false, false));
    }

    public function testGetBaseParams()
    {
        $this->config->expects($this->any())
                     ->method('canSubmitExtraParameter')
                     ->will($this->returnValue(false));
        $this->helper->getRequestHelper()->expects($this->any())
                      ->method('getOwnerParams')
                      ->will($this->returnValue([]));

        $paymentMock = $this->getMock('\Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $orderMock
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($paymentMock));

        $params = $this->helper->getDirectLinkRequestParams($this->quote, $orderMock);

        foreach ($this->getOwnerParams() as $ownerParam) {
            $this->assertArrayNotHasKey($ownerParam, $params);
        }
        foreach ($this->getShippingParams() as $shippingParam) {
            $this->assertArrayNotHasKey($shippingParam, $params);
        }
        $this->assertArrayHasKey('RTIMEOUT', $params);
    }

    public function testGetExtraParams()
    {
        $this->config->expects($this->any())
                     ->method('canSubmitExtraParameter')
                     ->will($this->returnValue(true));
        $this->helper->getRequestHelper()->expects($this->any())
                      ->method('getOwnerParams')
                      ->will($this->returnValue(
                          [
                              'OWNERADDRESS'                  => 'billing_street',
                              'OWNERTOWN'                     => 'billing_city',
                              'OWNERZIP'                      => 'billing_postcode',
                              'OWNERTELNO'                    => 'billing_phone',
                              'OWNERCTY'                      => 'billing_country',
                              'ECOM_BILLTO_POSTAL_POSTALCODE' => 'billing_postcode'
                          ]
                      ));
        $this->helper->getRequestHelper()->expects($this->any())
                     ->method('extractShipToParameters')
                     ->will($this->returnValue(
                         [
                             'ECOM_SHIPTO_POSTAL_CITY' => 'shipping_city',
                             'ECOM_SHIPTO_POSTAL_POSTALCODE' => 'shipping_postcode',
                             'ECOM_SHIPTO_POSTAL_STATE' => 'shipping_state',
                             'ECOM_SHIPTO_POSTAL_COUNTRYCODE' => 'shipping_countrycode',
                             'ECOM_SHIPTO_POSTAL_NAME_FIRST' => 'shipping_firstname',
                             'ECOM_SHIPTO_POSTAL_NAME_LAST' => 'shipping_lastname',
                             'ECOM_SHIPTO_POSTAL_STREET_LINE1' => 'shipping_street1',
                             'ECOM_SHIPTO_POSTAL_STREET_LINE2' => 'shipping_street2'
                         ]
                     ));

        $paymentMock = $this->getMock('\Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $orderMock
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($paymentMock));


        $params = $this->helper->getDirectLinkRequestParams($this->quote, $orderMock);
        foreach ($this->getOwnerParams() as $ownerParam) {
            $this->assertArrayHasKey($ownerParam, $params);
        }
        foreach ($this->getShippingParams() as $shippingParam) {
            $this->assertArrayHasKey($shippingParam, $params);
        }
        $this->assertArrayHasKey('RTIMEOUT', $params);
    }

    protected function getOwnerParams()
    {
        return $ownerParams = [
            'OWNERADDRESS',
            'OWNERTOWN',
            'OWNERZIP',
            'OWNERTELNO',
            'OWNERCTY',
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

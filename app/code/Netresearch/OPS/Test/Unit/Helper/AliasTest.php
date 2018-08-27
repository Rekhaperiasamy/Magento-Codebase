<?php

namespace Netresearch\OPS\Test\Unit\Helper;

/**
 * Description of AliasTest
 *
 * @author sebastian
 */
class AliasTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Helper\Alias
     */
    private $helper;

    /**
     * @var \Netresearch\OPS\Model\AliasFactory
     */
    private $aliasFactory;

    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    private $paymentHelper;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->aliasFactory = $this->getMock('\Netresearch\OPS\Model\AliasFactory', [], [], '', false, false);
        $this->paymentHelper = $this->getMock('\Netresearch\OPS\Helper\Payment', [], [], '', false, false);
        $this->paymentHelper->expects($this->any())
                      ->method('getCryptMethod')
                      ->will($this->returnValue('sha1'));
        $this->helper = $this->objectManager->getObject(
            'Netresearch\OPS\Helper\Alias',
            [
                                                            'oPSPaymentHelper' => $this->paymentHelper,
                                                            'oPSAliasFactory'  => $this->aliasFactory
                                                        ]
        );
    }

    public function testGetAliasWithoutAdditionalInformation()
    {
        /** @var \Netresearch\OPS\Helper\Alias $aliasHelperMock */
        $aliasHelperMock = $this->getMock('\Netresearch\OPS\Helper\Alias', ['isAdminSession'], [], '', false, false);
        $aliasHelperMock->expects($this->any())
            ->method('isAdminSession')
            ->will($this->returnValue(false));

        $payment = $this->getMock('\Magento\Quote\Model\Quote\Payment', [], [], '', false, false);
        $payment->expects($this->exactly(2))
            ->method('getAdditionalInformation')
            ->with('alias')
            ->will($this->returnValue(null));

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false, false);
        $quote->expects($this->exactly(2))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $this->assertTrue(
            strlen($aliasHelperMock->getAlias($quote)) <= 16
        );
        $this->assertTrue(
            strpos($aliasHelperMock->getAlias($quote), "99") != false
        );
    }

    public function testGetAliasWithAdditionalInformation()
    {
        /** @var \Netresearch\OPS\Helper\Alias $aliasHelperMock */
        $aliasHelperMock = $this->getMock('\Netresearch\OPS\Helper\Alias', ['isAdminSession'], [], '', false, false);
        $aliasHelperMock->expects($this->any())
            ->method('isAdminSession')
            ->will($this->returnValue(false));

        $payment = $this->getMock('\Magento\Quote\Model\Quote\Payment', [], [], '', false, false);
        $payment->expects($this->once())
            ->method('getAdditionalInformation')
            ->with('alias')
            ->will($this->returnValue('testAlias'));

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false, false);
        $quote->expects($this->once())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $this->assertEquals(
            'testAlias',
            $aliasHelperMock->getAlias($quote)
        );
    }

    public function testGetOpsCode()
    {
        $this->assertEquals(null, $this->objectManager->getObject('Netresearch\OPS\Helper\Alias')->getOpsCode());
    }

    public function testGetOpsBrand()
    {
        $this->assertEquals(null, $this->objectManager->getObject('Netresearch\OPS\Helper\Alias')->getOpsBrand());
    }


    public function testSaveNewAliasFromOrder()
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $paymentMock = $this->getMock('\Magento\Sales\Model\Order\Payment', ['getMethod'], [], '', false, false);
        $paymentMock->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('CC'));

        /** @var \Magento\Sales\Model\Order $order */
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $orderMock->expects($this->any())
            ->method('getBillingAddress')
            ->will($this->returnValue($this->getAddressData()));

        $orderMock->expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($this->getAddressData()));

        $orderMock->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($paymentMock));


        /** @var \Netresearch\OPS\Helper\Order $orderHelperMock */
        $orderHelperMock = $this->getMock(
            'Netresearch\OPS\Helper\Order',
            ['getOrder', 'generateAddressHash'],
            [],
            '',
            false,
            false
        );

        $orderHelperMock->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($orderMock));

        $orderHelperMock->expects($this->any())
            ->method('generateAddressHash')
            ->will($this->returnValue('123'));

        /** @var \Netresearch\OPS\Model\Alias $alias */
        $alias = $this->objectManager->getObject('\Netresearch\OPS\Model\Alias');

        $aliasFactory = $this->getMock('\Netresearch\OPS\Model\AliasFactory', [], [], '', false, false);
        $aliasFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($alias));

        $helper = $this->objectManager->getObject(
            'Netresearch\OPS\Helper\Alias',
            [
                'orderHelper' => $orderHelperMock,
                'oPSAliasFactory' => $aliasFactory
            ]
        );

        $aliasData = [];
        $aliasData['customer_id']               = 1;
        $aliasData['alias']                     = 123;
        $aliasData['ed']           = 12;
        $aliasData['billing_address_hash']      = 123;
        $aliasData['shipping_address_hash']     = 123;
        $aliasData['brand']                     = 'VISA';
        $aliasData['payment_method']            = 'CC';
        $aliasData['cardno']   = 12345;
        $aliasData['state']                     = \Netresearch\OPS\Model\Alias\State::PENDING;
        $aliasData['store_id']                  = 1;
        $aliasData['cn']               = 'Max Muster';
        $aliasData['orderid']                   = 1;


        $alias = $helper->saveAlias($aliasData);

        $this->assertEquals(123, $alias->getData('alias'));
        $this->assertEquals(12, $alias->getData('expiration_date'));
        $this->assertEquals(123, $alias->getData('billing_address_hash'));
        $this->assertEquals(123, $alias->getData('shipping_address_hash'));
        $this->assertEquals('VISA', $alias->getData('brand'));
        $this->assertEquals('CC', $alias->getData('payment_method'));
        $this->assertEquals('12345', $alias->getData('pseudo_account_or_cc_no'));
        $this->assertEquals('active', $alias->getData('state'));
        $this->assertEquals('Max Muster', $alias->getData('card_holder'));
    }

    public function testSaveAliasIfCustomerIsNotLoggedIn()
    {
        $quote = $this->getMock('\Magento\Quote\Model\Quote', ['load', 'getPayment', 'getCheckoutMethod'], [], '', false, false);
        $quote->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());
        $quote->expects($this->once())
            ->method('getPayment')
            ->will($this->returnValue(new \Magento\Framework\DataObject()));
        $quote->expects($this->once())
            ->method('getCheckoutMethod')
            ->will($this->returnValue(\Magento\Checkout\Model\Type\Onepage::METHOD_GUEST));
        $quoteFactory = $this->getMock('Magento\Quote\Model\QuoteFactory', [], [], '', false, false);
        $quoteFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($quote));
        /** @var \Netresearch\OPS\Helper\Alias $helper */
        $helper = $this->objectManager->getObject(
            'Netresearch\OPS\Helper\Alias',
            [
                'quoteQuoteFactory' => $quoteFactory
            ]
        );
        $this->assertEquals(
            null,
            $helper->saveAlias([
                'Alias_OrderId' => 4711,
                'StorePermanently' => 'N'
            ])
        );
    }

    public function testSaveAliasIfCustomerIsLoggedIn()
    {
        $payment = $this->getMock('Magento\Quote\Model\Quote\Payment', [], [], '', false, false);
        $payment->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('CreditCard'));

        $customer = new \Magento\Framework\DataObject();
        $customer->setId(1);

        $billing = $this->getMock('Magento\Customer\Model\Address\AbstractAddress', [], [], '', false, false);
        $shipping = $this->getMock('Magento\Customer\Model\Address\AbstractAddress', [], [], '', false, false);

        $quote = $this->getMock(
            '\Magento\Quote\Model\Quote',
            [
                'load',
                'getPayment',
                'getCheckoutMethod',
                'getCustomer',
                'getBillingAddress',
                'getShippingAddress',
                'getStoreId',
                'getIsVirtual'
            ],
            [],
            '',
            false,
            false
        );
        $quote->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());
        $quote->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));
        $quote->expects($this->once())
            ->method('getCheckoutMethod')
            ->will($this->returnValue(\Magento\Checkout\Model\Type\Onepage::METHOD_CUSTOMER));
        $quote->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));
        $quote->expects($this->any())
            ->method('getBillingAddress')
            ->will($this->returnValue($billing));
        $quote->expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($shipping));
        $quote->expects($this->any())
            ->method('getStoreId')
            ->will($this->returnValue(1));
        $quote->expects($this->any())
              ->method('getIsVirtual')
              ->will($this->returnValue(false));

        $quoteFactory = $this->getMock('Magento\Quote\Model\QuoteFactory', [], [], '', false, false);
        $quoteFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($quote));

        $paymentHelper = $this->getMock('Netresearch\OPS\Helper\Payment', [], [], '', false, false);
        $paymentHelper->expects($this->any())
            ->method('getCryptMethod')
            ->will($this->returnValue('sha1'));

        $aliasData['Alias_OrderId'] = 4711;
        $aliasData['Alias_AliasId'] = 4711;
        $aliasData['Card_Brand'] = 'Visa';
        $aliasData['Card_CardNumber'] = 'xxxx0815';
        $aliasData['Card_ExpiryDate'] = '1212';
        $aliasData['Card_CardHolderName'] = 'Foo Baar';
        $aliasData['Alias_StorePermanently'] = 'Y';

        $alias = $this->getMock(
            'Netresearch\OPS\Model\Alias',
            [
                'save',
                'load'
            ],
            [],
            '',
            false,
            false
        );


        $alias->expects($this->once())->method('save')->will($this->returnSelf());
        $alias->expects($this->once())->method('load')->will($this->returnSelf());

        $aliasFactory = $this->getMock('Netresearch\OPS\Model\AliasFactory', ['create'], [], '', false, false);
        $aliasFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($alias));

        /** @var \Netresearch\OPS\Helper\Alias $helper */
        $helper = $this->objectManager->getObject(
            'Netresearch\OPS\Helper\Alias',
            [
                'quoteQuoteFactory' => $quoteFactory,
                'oPSPaymentHelper' => $paymentHelper,
                'oPSAliasFactory' => $aliasFactory
            ]
        );

        $this->assertEquals(
            $alias,
            $helper->saveAlias($aliasData)
        );
    }

    public function testGetAliasesForCustomer()
    {
        /** @var \Magento\Customer\Model\Address $address */
        $address = $this->getMock('\Magento\Customer\Model\Address', ['save', 'load'], [], '', false, false);
        $address->setData($this->getAddressData()->getData());
        $quote = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false, false);
        $quote->expects($this->once())->method('getShippingAddress')->will($this->returnValue($address));
        $quote->expects($this->once())->method('getBillingAddress')->will($this->returnValue($address));
        $alias = $this->getMock('\Netresearch\OPS\Model\Alias', [], [], '', false, false);
        $alias->expects($this->once())->method('getAliasesForCustomer')->will($this->returnSelf());
        $this->aliasFactory->expects($this->once())->method('create')->will($this->returnValue($alias));
        $this->helper->getAliasesForCustomer(
            null,
            'ops_cc',
            $quote
        );
    }

    protected function getAddressData()
    {
        $address = new \Magento\Framework\DataObject();
        $address->setFirstname('foo');
        $address->setLastname('bert');
        $address->setStreet1('bla street 1');
        $address->setZipcode('4711');
        $address->setCity('Cologne');
        $address->setCountry_id(1);
        return $address;
    }

    public function testFormatAliasCardNo()
    {
        $cardNo = 'xxxxxxxxxxxx1111';
        $cardType = 'VISA';
        $this->assertEquals(
            'XXXX XXXX XXXX 1111',
            $this->helper->formatAliasCardNo($cardType, $cardNo)
        );

        $cardNo = 'xxxxxxxxxxxx9999';
        $cardType = 'MasterCard';
        $this->assertEquals(
            'XXXX XXXX XXXX 9999',
            $this->helper->formatAliasCardNo($cardType, $cardNo)
        );

        $cardNo = '3750-xxxxxx-03';
        $cardType = 'american express';
        $this->assertEquals(
            '3750 XXXXXX 03',
            $this->helper->formatAliasCardNo($cardType, $cardNo)
        );

        $cardNo = '3750-xxxxxx-03';
        $cardType = 'DINERS CLUB';
        $this->assertEquals(
            '3750 XXXXXX 03',
            $this->helper->formatAliasCardNo($cardType, $cardNo)
        );


        $cardNo = '675941-XXXXXXXX-08';
        $cardType = 'MaestroUK';
        $this->assertEquals(
            '675941 XXXXXXXX 08',
            $this->helper->formatAliasCardNo($cardType, $cardNo)
        );

        $cardNo = '675956-XXXXXXXX-54';
        $cardType = 'MaestroUK';
        $this->assertEquals(
            '675956 XXXXXXXX 54',
            $this->helper->formatAliasCardNo($cardType, $cardNo)
        );

        $cardNo = '564182-XXXXXXXX-69';
        $cardType = 'MaestroUK';
        $this->assertEquals(
            '564182 XXXXXXXX 69',
            $this->helper->formatAliasCardNo($cardType, $cardNo)
        );

        $cardNo = '3750-xxxxxx-03';
        $cardType = 'PostFinance Card';
        $this->assertEquals(
            '3750-XXXXXX-03',
            $this->helper->formatAliasCardNo($cardType, $cardNo)
        );
    }
}

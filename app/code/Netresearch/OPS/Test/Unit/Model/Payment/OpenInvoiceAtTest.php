<?php
namespace Netresearch\OPS\Test\Unit\Model\Payment;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch/OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OpenInvoiceAtTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\OpenInvoiceAt
     */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model         = $this->objectManager->getObject('\Netresearch\OPS\Model\Payment\OpenInvoiceAt');
    }

    /**
     * assure that openInvoiceAT can not capture partial, because invoice is always created on feedback in this case
     */
    public function testCanCapturePartial()
    {
        $this->assertFalse($this->model->canCapturePartial());
    }

    public function testIsAvailableNoQuoteGiven()
    {
        $quote = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false, false);
        $this->assertFalse($this->model->isAvailable($quote));
    }

    public function testIsAvailableNoDiscountAllowed()
    {
        $this->model = $this->getMock(
            '\Netresearch\OPS\Model\Payment\OpenInvoiceDe',
            ['isAvailableForDiscountedCarts'],
            [],
            '',
            false,
            false
        );
        $this->model->expects($this->any())->method('isAvailableForDiscountedCarts')->will($this->returnValue(false));
        $quote = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false, false);
        $quote->setSubtotal(5);
        $quote->setSubtotalWithDiscount(10);
        $this->assertFalse($this->model->isAvailable($quote));
    }

    public function testIsAvailableNoGender()
    {
        $this->model = $this->getMock(
            '\Netresearch\OPS\Model\Payment\OpenInvoiceDe',
            ['isAvailableForDiscountedCarts'],
            [],
            '',
            false,
            false
        );
        $quote       = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false, false);
        $quote->setSubtotal(10);
        $quote->setSubtotalWithDiscount(10);
        $this->assertFalse($this->model->isAvailable($quote));
    }

    public function testGetMethodDependendFormFields()
    {

        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['getMethodInstance'], [], '', false, false);
        $payment->expects($this->any())
            ->method('getMethodInstance')
            ->will($this->returnValue($this->model));


        $billingAddress = $this->getMock('\Magento\Sales\Model\Order\Address', [], [], '', false, false);
        $billingAddress
            ->expects($this->any())
            ->method('getStreet')
            ->will($this->returnValue(['Klarna-Straße 1/2/3']));

        $requestMock = $this->getMock('\Netresearch\OPS\Helper\Payment\Request', [], [], '', false, false);
        $requestMock
            ->expects($this->any())
            ->method('extractShipToParameters')
            ->will($this->returnValue(['ECOM_SHIPTO_POSTAL_STREET_LINE1' => 'Klarna-Straße 1/2/3']));

        $billToParams = [
            'ECOM_BILLTO_POSTAL_STREET_LINE1'  => 'Klarna-Straße',
            'ECOM_BILLTO_POSTAL_STREET_NUMBER' => '1/2/3'
        ];

        $requestMock
            ->expects($this->any())
            ->method('extractBillToParameters')
            ->will($this->returnValue($billToParams));

        $eavConfigMock = $this->getMock(
            '\Magento\Eav\Model\Config',
            ['getAttribute','getSource', 'getOptionText'],
            [],
            '',
            false,
            false
        );

        $eavConfigMock
            ->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnValue($eavConfigMock));

        $eavConfigMock
            ->expects($this->any())
            ->method('getSource')
            ->will($this->returnValue($eavConfigMock));


        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['getBillingAddress', 'getPayment', 'getCustomerDob', 'getCustomerGender', 'getShippingAddress'],
            [],
            '',
            false,
            false
        );

        $order->expects($this->any())
            ->method('getBillingAddress')
            ->will($this->returnValue($billingAddress));

        $order->expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($billingAddress));

        $order->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $order->expects($this->any())
            ->method('getCustomerDob')
            ->will($this->returnValue('01/10/1970'));

        $order->expects($this->any())
            ->method('getCustomerGender')
            ->will($this->returnValue(1));

        $model = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Payment\OpenInvoiceAt',
            [
                'eavConfig' => $eavConfigMock,
                'oPSPaymentRequestHelper' => $requestMock
            ]
        );

        $params = $model->getMethodDependendFormFields($order);
        $this->assertEquals(' ', $params['ECOM_BILLTO_POSTAL_STREET_NUMBER']);
        $this->assertEquals('Klarna-Straße 1/2/3', $params['OWNERADDRESS']);
    }
}

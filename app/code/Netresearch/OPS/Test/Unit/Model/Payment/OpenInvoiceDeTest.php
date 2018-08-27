<?php
namespace Netresearch\OPS\Test\Unit\Model\Payment;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch/OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OpenInvoiceDeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\OpenInvoiceDe
     */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model         = $this->objectManager->getObject('\Netresearch\OPS\Model\Payment\OpenInvoiceDe');
    }

    /**
     * assure that openInvoiceDe can not capture partial, because invoice is always created on feedback in this case
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
        $quote = $this->getMock('\Magento\Quote\Model\Quote', null, [], '', false, false);
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
        $this->model->expects($this->any())->method('isAvailableForDiscountedCarts')->will($this->returnValue(true));
        $quote = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false, false);
        $quote->setSubtotal(10);
        $quote->setSubtotalWithDiscount(10);
        $this->assertFalse($this->model->isAvailable($quote));
    }

    /**
     * @test
     */
    public function canUseForCountry()
    {
        $sessionMock = $this->getMockBuilder(\Magento\Checkout\Model\Session::class)
            ->disableOriginalConstructor()
            ->setMethods(['getQuote'])
            ->getMock();

        $quoteMock = $this->getMockBuilder(\Magento\Checkout\Model\Session::class)
            ->disableOriginalConstructor()
            ->setMethods(['getShippingAddress'])
            ->getMock();

        $shippingAddressMock = $this->getMockBuilder(\Magento\Quote\Model\Quote\Address::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCountry'])
            ->getMock();

        $shippingAddressMock
            ->expects($this->once())
            ->method('getCountry')
            ->will($this->returnValue('DE'));

        $quoteMock
            ->expects($this->exactly(2))
            ->method('getShippingAddress')
            ->will($this->returnValue($shippingAddressMock));

        $sessionMock
            ->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue($quoteMock));

        $this->model = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Payment\OpenInvoiceDe',
            [
                'checkoutSession' => $sessionMock
            ]
        );

        $result = $this->model->canUseForCountry('FR');

        $this->assertEquals(true, $result);
    }
}

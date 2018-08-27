<?php

namespace Netresearch\OPS\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

class RetryPaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Helper\Order | \PHPUnit_Framework_MockObject_MockObject
     */
    private $orderHelper;

    /**
     * @var \Magento\Checkout\Model\Session | \PHPUnit_Framework_MockObject_MockObject
     */
    private $checkoutSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteRepository;

    /** @var   \Netresearch\OPS\Model\RetryPayment  $model*/
    private $model;


    public function setUp()
    {
        $this->objectManager    = new ObjectManager($this);
        $this->orderHelper      = $this->getMock(\Netresearch\OPS\Helper\Order::class, [], [], '', false);
        $this->quoteRepository  = $this->getMock(\Magento\Quote\Api\CartRepositoryInterface::class, [], [], '', false);
        $this->checkoutSession  = $this->getMock(
            \Magento\Checkout\Model\Session::class,
            [
              'getOrderOnRetry',
              'replaceQuote',
              'setLastOrderId',
              'setLastRealOrderId',
              'setLastQuoteId',
              'setLastSuccessQuoteId',
              'setRedirectUrl',
              'setPaymentRetryFlow'
            ],
            [],
            '',
            false
        );


        $this->model = $this->objectManager->getObject(
            \Netresearch\OPS\Model\Rest\RetryPayment::class,
            [
                'orderHelper'      => $this->orderHelper,
                'checkoutSession'  => $this->checkoutSession,
                'quoteRepository'  => $this->quoteRepository,
            ]
        );
    }

    /**
     * @test
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testUpdatePaymentInformationNoOrder()
    {
        $this->checkoutSession
            ->expects($this->any())
            ->method('getOrderOnRetry')
            ->will($this->returnValue(false));

        $quotePaymentMock = $this->getMock('Magento\Quote\Model\Quote\Payment', [], [], '', false, false);

        $this->model->updatePaymentInformation('123', $quotePaymentMock, null);
    }

    /**
     * @test
     */
    public function testUpdatePaymentInformation()
    {
        $this->checkoutSession
            ->expects($this->any())
            ->method('getOrderOnRetry')
            ->will($this->returnValue(true));

        $this->assertTrue($this->checkoutSession->getOrderOnRetry());

        $quotePaymentMock = $this->getMock('Magento\Quote\Model\Quote\Payment', [], [], '', false, false);
        $quotePaymentMock
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(['method' => 'CC']));


        $quoteMock = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false, false);
        $quoteMock
            ->expects($this->once())
            ->method('getPayment')
            ->will($this->returnValue($quotePaymentMock));

        $this->quoteRepository
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($quoteMock));

        $orderMock = $this->getMock('Magento\Sales\Model\Order', [], [], '', false, false);

        $this->orderHelper
            ->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue($orderMock));

        $methodInstanceMock = $this->getMock('Netresearch\OPS\Model\Payment\CC', [], [], '', false, false);
        $methodInstanceMock
            ->expects($this->once())
            ->method('assignData')
            ->will($this->returnValue($methodInstanceMock));

        $orderPaymentMock = $this->getMock('\Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $orderPaymentMock
            ->expects($this->any())
            ->method('getMethodInstance')
            ->will($this->returnValue($methodInstanceMock));

        $orderPaymentMock
            ->expects($this->once())
            ->method('setMethod')
            ->will($this->returnValue($orderPaymentMock));

        $orderMock
            ->expects($this->once())
            ->method('getPayment')
            ->will($this->returnValue($orderPaymentMock));


        $this->checkoutSession
            ->expects($this->once())
            ->method('setPaymentRetryFlow');

        $this->model->updatePaymentInformation('123', $quotePaymentMock, null);
    }
}

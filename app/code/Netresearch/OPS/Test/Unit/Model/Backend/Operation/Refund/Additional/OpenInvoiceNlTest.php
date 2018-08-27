<?php
namespace Netresearch\OPS\Test\Unit\Model\Backend\Operation\Refund\Additional;

use Magento\Framework\DataObject;

class OpenInvoiceNlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Backend\Operation\Refund\Additional\OpenInvoiceNl
     */
    private $model;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Netresearch\OPS\Helper\Payment\Request
     */
    private $paymentRequestHelper;

    private $testInvoice;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->dataHelper = $this->getMock(
            \Netresearch\OPS\Helper\Data::class,
            [],
            [],
            '',
            false,
            false
        );
        $this->dataHelper->expects($this->any())
            ->method('getAmount')
            ->will(
                $this->returnCallback(
                    function ($v) {
                        return $v * 100;
                    }
                )
            );
        $this->paymentRequestHelper = $this->getMock(
            '\Netresearch\OPS\Helper\Payment\Request',
            [],
            [],
            '',
            false,
            false
        );
        $items = $this->getItemMocks();
        $invoice = $this->getInvoiceMock($items);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock(\Magento\Sales\Model\Order::class, null, [], '', false, false);
        $order->setShippingDescription('SuperFunShipping');
        $order->setId(1);

        $payment = $this->getMock(\Magento\Sales\Model\Order\Payment::class, null, [], '', false, false);
        $payment->setMethod('ops_openInvoiceNl');

        $order->setPayment($payment);
        $invoice->setOrder($order);
        $this->testInvoice = $invoice;

        $refundHelperMock = $this->mockRefundHelper(
            [
                'items'               => $items,
                'shipping_amount'     => 0,
                'adjustment_positive' => 0,
                'adjustment_negative' => 0
            ]
        );
        $this->model = $this->objectManager->getObject(
            \Netresearch\OPS\Model\Backend\Operation\Refund\Additional\OpenInvoiceNl::class,
            [
                'oPSHelper'                => $this->dataHelper,
                'oPSPaymentRequestHelper'  => $this->paymentRequestHelper,
                'salesOrderInvoiceFactory' => $this->mockInvoiceFactory($invoice),
                'oPSOrderRefundHelper'     => $refundHelperMock
            ]
        );
    }

    public function testExtractWithoutShippingAndAdjustments()
    {
        $result = $this->model->extractAdditionalParams($this->testInvoice);
        // refunded item
        $this->assertTrue(is_array($result));
        $this->assertTrue(0 < count($result));
        $this->assertArrayHasKey('ITEMID1', $result);
        $this->assertEquals(1, $result['ITEMID1']);
        $this->assertArrayHasKey('ITEMNAME1', $result);
        $this->assertEquals('Item 1', $result['ITEMNAME1']);
        $this->assertArrayHasKey('ITEMPRICE1', $result);
        $this->assertEquals(4299, $result['ITEMPRICE1']);
        $this->assertArrayHasKey('ITEMVATCODE1', $result);
        $this->assertEquals('19%', $result['ITEMVATCODE1']);
        $this->assertArrayHasKey('TAXINCLUDED1', $result);
        $this->assertEquals(1, $result['TAXINCLUDED1']);
        $this->assertArrayHasKey('ITEMQUANT1', $result);
        $this->assertEquals(2, $result['ITEMQUANT1']);
        // 'ignored item'
        $this->assertArrayHasKey('ITEMID2', $result);
        $this->assertEquals(2, $result['ITEMID2']);
        $this->assertArrayHasKey('ITEMNAME2', $result);
        $this->assertEquals('Item 2', $result['ITEMNAME2']);
        $this->assertArrayHasKey('ITEMPRICE2', $result);
        $this->assertEquals(1999, $result['ITEMPRICE2']);
        $this->assertArrayHasKey('ITEMVATCODE2', $result);
        $this->assertEquals('7%', $result['ITEMVATCODE2']);
        $this->assertArrayHasKey('TAXINCLUDED2', $result);
        $this->assertEquals(1, $result['TAXINCLUDED2']);
        $this->assertArrayHasKey('ITEMQUANT2', $result);
        $this->assertEquals(2, $result['ITEMQUANT2']);
    }

    private function mockRefundHelper($params)
    {
        $helperMock = $this->getMock(
            \Netresearch\OPS\Helper\Order\Refund::class,
            ['getCreditMemoFromRequest', 'createRefundTransaction'],
            [],
            '',
            false,
            false
        );
        $helperMock->expects($this->any())
            ->method('getCreditMemoFromRequest')
            ->will($this->returnValue($params));

        return $helperMock;
    }

    private function mockInvoiceFactory($invoice)
    {
        $mock = $this->getMock(
            \Magento\Sales\Model\Order\InvoiceFactory::class,
            ['create'],
            [],
            '',
            false,
            false
        );
        $mock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($invoice));

        return $mock;
    }

    /**
     * @param $items
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getInvoiceMock($items)
    {
        $invoice = $this->getMock(
            \Magento\Sales\Model\Order\Invoice::class,
            ['getItemsCollection', 'load'],
            [],
            '',
            false,
            false
        );
        $invoice->expects($this->any())
            ->method('getItemsCollection')->will($this->returnValue($items));
        $invoice->expects($this->any())
            ->method('load')->will($this->returnSelf());
        // add shipping and discount
        $invoice->setBaseShippingInclTax(10.00);

        return $invoice;
    }

    /**
     * @return array
     */
    private function getItemMocks()
    {
        $items = [];
        // add first item to invoice
        $item = new DataObject();
        $orderItem = new DataObject();
        $orderItem->setId(1);
        $orderItem->setQtyOrdered(2);
        $item->setOrderItemId(1);
        $item->setOrderItem($orderItem);
        $item->setName('Item 1');
        $item->setBasePriceInclTax(42.99);
        $item->setQty(2);
        $item->setTaxPercent(19);
        $items[1] = $item;
        // add second item to invoice
        $orderItem = new DataObject();
        $orderItem->setId(2);
        $orderItem->setQtyOrdered(2);
        $item = new DataObject();
        $item->setOrderItemId(2);
        $item->setOrderItem($orderItem);
        $item->setName('Item 2');
        $item->setBasePriceInclTax(19.99);
        $item->setQty(2);
        $item->setTaxPercent(7);
        $items[2] = $item;

        return $items;
    }
}

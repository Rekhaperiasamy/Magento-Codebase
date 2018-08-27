<?php

namespace Netresearch\OPS\Test\Unit\Helper;

use Magento\Framework\Api\Search\SearchCriteria;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Netresearch\OPS\Helper\Order as OrderHelper;
use Netresearch\OPS\Model\Config;

class OrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $devPrefix = '';

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->devPrefix = 'DEV';
    }

    /**
     * @test
     */
    public function testGetOpsOrderIdFromOrderWithOrderIdAsOrderReference()
    {
        $orderRef = '123';
        $configMock = $this->getMock(Config::class, [], [], '', false, false);
        $configMock->expects($this->any())
            ->method('getConfigData')
            ->with('devprefix')
            ->will($this->returnValue($this->devPrefix));

        $configMock->expects($this->any())
            ->method('getOrderReference')
            ->will($this->returnValue('orderId'));

        $salesObject = $this->objectManager->getObject(
            Order::class,
            ['data' => [
                'increment_id' => $orderRef,
            ]]
        );

        /** @var OrderHelper $helper */
        $helper = $this->objectManager->getObject(
            OrderHelper::class,
            ['config' => $configMock]
        );

        $result = $helper->getOpsOrderId($salesObject);
        $this->assertEquals($this->devPrefix . '#' . $orderRef, $result);
    }

    /**
     * @test
     */
    public function testGetOpsOrderIdFromQuoteWithOrderIdAsOrderReference()
    {
        $orderRef = '123';
        $configMock = $this->getMock(Config::class, [], [], '', false, false);
        $configMock->expects($this->any())
            ->method('getConfigData')
            ->with('devprefix')
            ->will($this->returnValue($this->devPrefix));

        $configMock->expects($this->any())
            ->method('getOrderReference')
            ->will($this->returnValue('orderId'));

        $salesObject = $this->getMockBuilder(Quote::class)
            ->setMethods(['getStoreId', 'reserveOrderId', 'getReservedOrderId'])
            ->disableOriginalConstructor()
            ->getMock();

        $salesObject
            ->expects($this->once())
            ->method('getReservedOrderId')
            ->will($this->returnValue($orderRef));

        /** @var OrderHelper $helper */
        $helper = $this->objectManager->getObject(
            OrderHelper::class,
            ['config' => $configMock]
        );

        $result = $helper->getOpsOrderId($salesObject);
        $this->assertEquals($this->devPrefix . '#' . $orderRef, $result);
    }

    /**
     * @test
     */
    public function testGetOpsOrderIdFromQuoteWithNoOrderReference()
    {
        $orderRef = '123';
        $configMock = $this->getMock(Config::class, [], [], '', false, false);
        $configMock->expects($this->any())
            ->method('getConfigData')
            ->with('devprefix')
            ->will($this->returnValue($this->devPrefix));

        $salesObject = $this->getMockBuilder(Quote::class)
            ->setMethods(['getStoreId', 'reserveOrderId', 'getReservedOrderId'])
            ->disableOriginalConstructor()
            ->getMock();

        $salesObject->expects($this->once())
            ->method('getReservedOrderId')
            ->will($this->returnValue($orderRef));

        /** @var OrderHelper $helper */
        $helper = $this->objectManager->getObject(
            OrderHelper::class,
            ['config' => $configMock]
        );

        $result = $helper->getOpsOrderId($salesObject);
        $this->assertEquals($this->devPrefix . $orderRef, $result);
    }

    /**
     * @test
     */
    public function testGetOpsOrderIdFromOrderWithNoOrderReference()
    {
        $orderRef = '123';
        $configMock = $this->getMock(Config::class, [], [], '', false, false);
        $configMock->expects($this->any())
            ->method('getConfigData')
            ->with('devprefix')
            ->will($this->returnValue($this->devPrefix));

        $salesObject = $this->objectManager->getObject(
            Order::class,
            ['data' => [
                'increment_id' => $orderRef,
            ]]
        );

        /** @var OrderHelper $helper */
        $helper = $this->objectManager->getObject(
            OrderHelper::class,
            ['config' => $configMock]
        );

        $result = $helper->getOpsOrderId($salesObject);
        $this->assertEquals($this->devPrefix . $orderRef, $result);
    }

    /**
     * @test
     */
    public function testGetOpsOrderIdFromQuoteWithQuoteIdAsOrderReference()
    {
        $orderRef = '123';
        $configMock = $this->getMock(Config::class, [], [], '', false, false);
        $configMock->expects($this->any())
            ->method('getConfigData')
            ->with('devprefix')
            ->will($this->returnValue($this->devPrefix));


        $salesObject = $this->getMockBuilder(Quote::class)
            ->setMethods(['getStoreId', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();

        $salesObject
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($orderRef));

        /** @var OrderHelper $helper */
        $helper = $this->objectManager->getObject(
            OrderHelper::class,
            ['config' => $configMock]
        );

        $result = $helper->getOpsOrderId($salesObject, false);
        $this->assertEquals($this->devPrefix . $orderRef, $result);
    }

    /**
     * @test
     */
    public function testGetOpsOrderIdFromOrderWithQuoteIdAsOrderReference()
    {
        $orderRef = '123';
        $configMock = $this->getMock(Config::class, [], [], '', false, false);
        $configMock->expects($this->any())
            ->method('getConfigData')
            ->with('devprefix')
            ->will($this->returnValue($this->devPrefix));

        $salesObject = $this->getMockBuilder(Order::class)
            ->setMethods(['getStoreId', 'getQuoteId'])
            ->disableOriginalConstructor()
            ->getMock();

        $salesObject
            ->expects($this->once())
            ->method('getQuoteId')
            ->will($this->returnValue($orderRef));

        /** @var OrderHelper $helper */
        $helper = $this->objectManager->getObject(
            OrderHelper::class,
            ['config' => $configMock]
        );

        $result = $helper->getOpsOrderId($salesObject, false);
        $this->assertEquals($this->devPrefix . $orderRef, $result);
    }

    /**
     * @test
     */
    public function orderIsFoundByIncrementId()
    {
        $opsOrderId = 'test123';
        $orderId = '123';

        $configMock = $this->getMock(Config::class, [], [], '', false, false);
        $configMock->expects($this->any())
            ->method('getConfigData')
            ->with('devprefix')
            ->will($this->returnValue('test'));

        // when filtered for increment id, collection returns this result:
        $collectionOrder = $this->objectManager->getObject(
            Order::class,
            ['data' => [
                'id' => $orderId
            ]]
        );
        $collectionOrders = [$collectionOrder];

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\ResourceModel\Order\Collection $orderCollectionMock */
        $orderCollectionMock = $this->getMockBuilder(\Magento\Sales\Model\ResourceModel\Order\Collection::class)
            ->setMethods(['load', 'getSize'])
            ->disableOriginalConstructor()
            ->getMock();
        $orderCollectionMock
            ->expects($this->once())
            ->method('getSize')
            ->willReturn(count($collectionOrders));
        $orderCollectionMock->setItems($collectionOrders);

        $searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods(['addFilter', 'create'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaBuilderMock
            ->expects($this->once())
            ->method('addFilter')
            ->willReturnSelf();
        $searchCriteriaBuilderMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->objectManager->getObject(SearchCriteria::class));

        $searchCriteriaBuilderFactoryMock = $this->getMockBuilder(SearchCriteriaBuilderFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaBuilderFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaBuilderMock);

        $orderRepositoryMock = $this->getMockBuilder(OrderRepository::class)
            ->setMethods(['getList'])
            ->disableOriginalConstructor()
            ->getMock();
        $orderRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->willReturn($orderCollectionMock);

        /** @var OrderHelper $helper */
        $helper = $this->objectManager->getObject(
            OrderHelper::class,
            [
                'config'                       => $configMock,
                'orderRepository'              => $orderRepositoryMock,
                'searchCriteriaBuilderFactory' => $searchCriteriaBuilderFactoryMock,
            ]
        );

        /** @var Order $order */
        $order = $helper->getOrder($opsOrderId);
        $this->assertSame($collectionOrder, $order);
    }

    /**
     * @test
     */
    public function orderIsFoundWithQuoteId()
    {
        $opsOrderId = 'test123';
        $orderId = '123';

        $configMock = $this->getMock(Config::class, [], [], '', false, false);
        $configMock->expects($this->any())
            ->method('getConfigData')
            ->with('devprefix')
            ->will($this->returnValue('test'));

        // when filtered for increment id, collection returns no results
        $collectionOrders = [];
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\ResourceModel\Order\Collection $emptyCollectionMock */
        $emptyCollectionMock = $this->getMockBuilder(\Magento\Sales\Model\ResourceModel\Order\Collection::class)
            ->setMethods(['load', 'getSize'])
            ->disableOriginalConstructor()
            ->getMock();
        $emptyCollectionMock
            ->expects($this->once())
            ->method('getSize')
            ->willReturn(count($collectionOrders));

        // when filtered for quote id, collection returns this result:
        $joinedCollectionOrder = $this->objectManager->getObject(
            Order::class,
            ['data' => [
                'id' => $orderId,
            ]]
        );
        $joinedCollectionOrders = [$joinedCollectionOrder];
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\ResourceModel\Order\Collection $joinedCollectionMock */
        $joinedCollectionMock = $this->getMockBuilder(\Magento\Sales\Model\ResourceModel\Order\Collection::class)
            ->setMethods(['load', 'join', 'addFieldToFilter', 'addOrder', 'getTable'])
            ->disableOriginalConstructor()
            ->getMock();
        $joinedCollectionMock
            ->expects($this->once())
            ->method('join')
            ->willReturnSelf();
        $joinedCollectionMock
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->willReturnSelf();
        $joinedCollectionMock
            ->expects($this->once())
            ->method('addOrder')
            ->willReturnSelf();
        $joinedCollectionMock->setItems($joinedCollectionOrders);

        $searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods(['addFilter', 'create'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaBuilderMock
            ->expects($this->exactly(2))
            ->method('addFilter')
            ->willReturnSelf();
        $searchCriteriaBuilderMock
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturn($this->objectManager->getObject(SearchCriteria::class));

        $searchCriteriaBuilderFactoryMock = $this->getMockBuilder(SearchCriteriaBuilderFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaBuilderFactoryMock
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturn($searchCriteriaBuilderMock);

        $orderRepositoryMock = $this->getMockBuilder(OrderRepository::class)
            ->setMethods(['getList'])
            ->disableOriginalConstructor()
            ->getMock();
        $orderRepositoryMock
            ->expects($this->exactly(2))
            ->method('getList')
            ->willReturnOnConsecutiveCalls($emptyCollectionMock, $joinedCollectionMock);

        /** @var OrderHelper $helper */
        $helper = $this->objectManager->getObject(
            OrderHelper::class,
            [
                'config'                       => $configMock,
                'orderRepository'              => $orderRepositoryMock,
                'searchCriteriaBuilderFactory' => $searchCriteriaBuilderFactoryMock,
            ]
        );

        /** @var Order $order */
        $order = $helper->getOrder($opsOrderId);
        $this->assertSame($joinedCollectionOrder, $order);
    }

    public function testGetOrder()
    {

        $opsOrderId = 'test123';
        $configMock = $this->getMock(Config::class, [], [], '', false, false);
        $configMock->expects($this->any())
            ->method('getConfigData')
            ->with('devprefix')
            ->will($this->returnValue('test'));


        $searchCriteriaBuilderMock = $this->getMock(
            \Magento\Framework\Api\SearchCriteriaBuilder::class,
            [],
            [],
            '',
            false,
            false
        );
        $searchCriteriaBuilderMock
            ->expects($this->once())
            ->method('addFilter')
            ->willReturnSelf();

        $searchCriteriaMock = $this->getMock(
            SearchCriteria::class,
            [],
            [],
            '',
            false,
            false
        );
        $searchCriteriaBuilderMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($searchCriteriaMock));

        $searchCriteriaBuilderFactoryMock = $this->getMock(
            SearchCriteriaBuilderFactory::class,
            [],
            [],
            '',
            false,
            false
        );

        $searchCriteriaBuilderFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($searchCriteriaBuilderMock));

        $orderRepositoryMock = $this->getMock(
            OrderRepository::class,
            ['getList'],
            [],
            '',
            false,
            false
        );
        /** @var Order $order */
        $order = $this->objectManager->getObject(Order::class);
        $order->setId('123');
        $orderCollectionMock = $this->objectManager->getCollectionMock(
            \Magento\Sales\Model\ResourceModel\Order\Collection::class,
            []
        );

        $orderCollectionMock
            ->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(1));
        $orderCollectionMock
            ->expects($this->once())
            ->method('getFirstItem')
            ->will($this->returnValue($order));

        $orderRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->will($this->returnValue($orderCollectionMock));

        /** @var OrderHelper $helper */
        $helper = $this->objectManager->getObject(
            \Netresearch\OPS\Helper\Order::class,
            [
                'searchCriteriaBuilderFactory' => $searchCriteriaBuilderFactoryMock,
                'orderRepository'              => $orderRepositoryMock,
                'config'                       => $configMock
            ]
        );

        $result = $helper->getOrder($opsOrderId);
        $this->assertTrue($result instanceof Order);
        $this->assertEquals('123', $result->getId());
    }

    public function testGetQuote()
    {
        $quote = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false, false);
        $quote->expects($this->once())
            ->method('load')
            ->with(11)
            ->will($this->returnSelf());

        $quoteFactory = $this->getMock('\Magento\Quote\Model\QuoteFactory', [], [], '', false, false);
        $quoteFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($quote));

        /** @var OrderHelper $helper */
        $helper = $this->objectManager
            ->getObject(
                '\Netresearch\OPS\Helper\Order',
                [
                    'quoteQuoteFactory' => $quoteFactory
                ]
            );

        $this->assertEquals($quote, $helper->getQuote(11));
    }

    public function testCheckIfAddressAreSameWithSameAddressData()
    {
        /** @var \Magento\Customer\Model\Address $address */
        $address = $this->objectManager->getObject('\Magento\Sales\Model\Order\Address');
        $address->setStreet('foo');

        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())
            ->method('getBillingAddress')
            ->will($this->returnValue($address));
        $order->expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($address));

        $paymentHelper = $this->getMock('\Netresearch\OPS\Helper\Payment', ['getCryptMethod'], [], '', false, false);
        $paymentHelper->expects($this->any())
            ->method('getCryptMethod')
            ->will($this->returnValue('SHA1'));

        /** @var OrderHelper $helper */
        $helper = $this->objectManager
            ->getObject(
                '\Netresearch\OPS\Helper\Order',
                [
                    'oPSPaymentHelper' => $paymentHelper
                ]
            );

        $this->assertTrue((bool)$helper->checkIfAddressesAreSame($order));
    }

    public function testCheckIfAddressAreNotSameWithDifferentAddressData()
    {
        /** @var \Magento\Customer\Model\Address $address */
        $address = $this->objectManager->getObject('\Magento\Sales\Model\Order\Address');
        $address->setStreet('foo');

        $shippingAddress = $this->objectManager->getObject('\Magento\Sales\Model\Order\Address');
        $shippingAddress->setStreet('fooBar');


        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())
            ->method('getBillingAddress')
            ->will($this->returnValue($address));
        $order->expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($shippingAddress));

        $paymentHelper = $this->getMock('\Netresearch\OPS\Helper\Payment', ['getCryptMethod'], [], '', false, false);
        $paymentHelper->expects($this->any())
            ->method('getCryptMethod')
            ->will($this->returnValue('SHA1'));

        /** @var OrderHelper $helper */
        $helper = $this->objectManager
            ->getObject(
                '\Netresearch\OPS\Helper\Order',
                [
                    'oPSPaymentHelper' => $paymentHelper
                ]
            );

        $this->assertFalse((bool)$helper->checkIfAddressesAreSame($order));
    }
}

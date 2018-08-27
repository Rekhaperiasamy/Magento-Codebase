<?php

namespace Netresearch\OPS\Test\Unit\Model\Rest;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

class AliasTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Helper\Alias | \PHPUnit_Framework_MockObject_MockObject
     */
    private $aliasHelper;

    /**
     * @var \Magento\Checkout\Model\Session | \PHPUnit_Framework_MockObject_MockObject
     */
    private $checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilder;

    /** @var   \Netresearch\OPS\Model\Rest\Alias $model */
    private $model;


    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->aliasHelper = $this->getMockBuilder(\Netresearch\OPS\Helper\Alias::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutSession = $this->getMockBuilder(\Magento\Checkout\Model\Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderRepository = $this->getMockBuilder(\Magento\Sales\Model\OrderRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->searchCriteriaBuilder = $this->getMockBuilder(\Magento\Framework\Api\SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $this->objectManager->getObject(
            \Netresearch\OPS\Model\Rest\Alias::class,
            [
                'aliasHelper' => $this->aliasHelper,
                'checkoutSession' => $this->checkoutSession,
                'orderRepository' => $this->orderRepository,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilder
            ]
        );
    }

    /**
     * @test
     */
    public function getListIsEmpty()
    {
        $customer = $this->objectManager->getObject(\Magento\Customer\Model\Customer::class);
        $aliasCollection = $this->objectManager->getCollectionMock(\Netresearch\OPS\Model\ResourceModel\Alias\Collection::class, []);

        /** @var \Magento\Quote\Model\Quote $quote */
        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomer'])
            ->getMock();

        $quoteMock
            ->expects($this->once())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->checkoutSession
            ->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue($quoteMock));

        $this->aliasHelper
            ->expects($this->once())
            ->method('getAliasesForCustomer')
            ->will($this->returnValue($aliasCollection));

        $result = $this->model->getList('ops_cc');
        $this->assertTrue(0 == count($result));
    }

    /**
     * @test
     */
    public function getList()
    {
        $customer = $this->objectManager->getObject(\Magento\Customer\Model\Customer::class);

        /** @var \Netresearch\OPS\Model\Alias $alias */
        $alias = $this->objectManager->getObject(\Netresearch\OPS\Model\Alias::class);

        /** @var \Netresearch\OPS\Model\ResourceModel\Alias\Collection $aliasCollection */

        $alias->setData(
            [
                'brand' => 'Visa',
                'id' => 12,
                'alias' => 'foo',
                'card_holder' => 'Max Muster',
                'pseudo_account_or_cc_no' => '62361263',
                'expiration_date' => '1212'
            ]
        );

        $aliasCollection[] = $alias;

        /** @var \Magento\Quote\Model\Quote $quote */
        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomer'])
            ->getMock();

        $quoteMock
            ->expects($this->once())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->checkoutSession
            ->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue($quoteMock));

        $this->aliasHelper
            ->expects($this->once())
            ->method('getAliasesForCustomer')
            ->will($this->returnValue($aliasCollection));

        $result = $this->model->getList('ops_cc');

        $this->assertTrue(1 >= count($result));
    }

    /**
     * @test
     */
    public function getListForRetryPageEmpty()
    {
        $aliasCollection = $this->objectManager->getCollectionMock(\Netresearch\OPS\Model\ResourceModel\Alias\Collection::class, []);

        /** @var \PHPUnit_Framework_MockObject_MockObject $orderMock */
        $orderMock = $this->getMockBuilder(\Magento\Sales\Model\Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomer', 'getShippingAddress', 'getBillingAddress'])
            ->getMock();

        $this->searchCriteriaBuilder
            ->expects($this->once())
            ->method('addFilter')
            ->will($this->returnSelf());

        $this->searchCriteriaBuilder
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new SearchCriteria()));

        $this->orderRepository
            ->expects($this->once())
            ->method('getList')
            ->will($this->returnValue(new DataObject(['items' => [$orderMock]])));

        $this->aliasHelper
            ->expects($this->once())
            ->method('getAliasesForAddresses')
            ->will($this->returnValue($aliasCollection));

        $result = $this->model->getListForRetryPage();
        $this->assertTrue(0 == count($result));
    }

    /**
     * @test
     */
    public function getListForRetryPage()
    {

        /** @var \Netresearch\OPS\Model\Alias $alias */
        $alias = $this->objectManager->getObject(\Netresearch\OPS\Model\Alias::class);

        /** @var \Netresearch\OPS\Model\ResourceModel\Alias\Collection $aliasCollection */

        $alias->setData(
            [
                'brand' => 'Visa',
                'id' => 12,
                'alias' => 'foo',
                'card_holder' => 'Max Muster',
                'pseudo_account_or_cc_no' => '62361263',
                'expiration_date' => '1212'
            ]
        );

        $aliasCollection[] = $alias;


        /** @var \PHPUnit_Framework_MockObject_MockObject $orderMock */
        $orderMock = $this->getMockBuilder(\Magento\Sales\Model\Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomer', 'getShippingAddress', 'getBillingAddress'])
            ->getMock();

        $this->searchCriteriaBuilder
            ->expects($this->once())
            ->method('addFilter')
            ->will($this->returnSelf());

        $this->searchCriteriaBuilder
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new SearchCriteria()));


        $this->orderRepository
            ->expects($this->once())
            ->method('getList')
            ->will($this->returnValue(new DataObject(['items' => [$orderMock]])));

        $this->aliasHelper
            ->expects($this->once())
            ->method('getAliasesForAddresses')
            ->will($this->returnValue($aliasCollection));

        $result = $this->model->getListForRetryPage();
        $this->assertTrue(1 >= count($result));
    }
}

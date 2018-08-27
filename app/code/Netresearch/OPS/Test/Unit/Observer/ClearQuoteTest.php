<?php
namespace Netresearch\OPS\Test\Unit\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Manager;
use Magento\Framework\Event\Observer;
use Magento\Quote\Model\Quote;
use Netresearch\OPS\Observer\ClearQuote;

class ClearQuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var ClearQuote
     */
    private $object;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
    }

    public function testExecuteRetryFlowFalse()
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->getObject(Quote::class);
        $quote->setId(12);

        /** @var Observer $observerMock */
        $observerMock = $this->getMockBuilder(Observer::class)
            ->setMethods(['getControllerAction'])
            ->disableOriginalConstructor()
            ->getMock();

        $observerMock->expects($this->once())
            ->method('getControllerAction')
            ->will($this->returnValue(\Netresearch\OPS\Controller\Payment\Retry::class));

        /** @var Session $checkoutSessionMock */
        $checkoutSessionMock = $this->getMockBuilder(Session::class)
            ->setMethods(['getPaymentRetryFlow', 'setQuoteId'])
            ->disableOriginalConstructor()
            ->getMock();

        $checkoutSessionMock->expects($this->once())
            ->method('getPaymentRetryFlow')
            ->will($this->returnValue(false));

        $checkoutSessionMock->replaceQuote($quote);

        $this->object = $this->objectManager->getObject(
            ClearQuote::class,
            ['checkoutSession' => $checkoutSessionMock]
        );

        $this->object->execute($observerMock);

        $this->assertTrue($checkoutSessionMock->hasQuote());
    }

    public function testExecuteRetryFlowTrue()
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->getObject(Quote::class);
        $quote->setId(12);

        $requestMock =  $this->getMockBuilder(\Magento\Framework\App\Request\Http::class)
            ->setMethods(['isAjax'])
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock->expects($this->once())
            ->method('isAjax')
            ->will($this->returnValue(false));

        /** @var Observer $observerMock */
        $observerMock = $this->getMockBuilder(Observer::class)
            ->setMethods(['getControllerAction', 'getRequest'])
            ->disableOriginalConstructor()
            ->getMock();

        $observerMock->expects($this->once())
            ->method('getControllerAction')
            ->will($this->returnValue('MyClass'));

        $observerMock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($requestMock));

        /** @var Manager $eventManagerMock */
        $eventManagerMock = $this->getMockBuilder(Manager::class)
            ->setMethods(['dispatch'])
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Session $checkoutSessionMock */
        $checkoutSessionMock = $this->getMockBuilder(Session::class)
            ->setMethods(
                ['getPaymentRetryFlow', 'setQuoteId', 'getQuote', 'setLastSuccessQuoteId', 'setPaymentRetryFlow']
            )
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionClass = new \ReflectionClass($checkoutSessionMock);
        $reflectionProperty = $reflectionClass->getProperty('_eventManager');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($checkoutSessionMock, $eventManagerMock);

        $checkoutSessionMock->expects($this->once())
            ->method('getPaymentRetryFlow')
            ->will($this->returnValue(true));

        $checkoutSessionMock->replaceQuote($quote);

        $this->object = $this->objectManager->getObject(
            ClearQuote::class,
            ['checkoutSession' => $checkoutSessionMock]
        );

        $this->object->execute($observerMock);

        $this->assertFalse($checkoutSessionMock->hasQuote());
    }
}

<?php
namespace Netresearch\OPS\Test\Unit\Observer;

class ClearPaymentMethodFromQuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Observer\ClearPaymentMethodFromQuote
     */
    private $object;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    private $observer;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->observer      = new \Magento\Framework\Event\Observer();
        $this->object        = $this->objectManager->getObject('\Netresearch\OPS\Observer\ClearPaymentMethodFromQuote');
    }

    public function testExecute()
    {
        $payment = $this->getMock('\Magento\Quote\Model\Quote\Payment', [], [], '', false, false);
        $payment->expects($this->once())->method('setMethod')->with(null)->will($this->returnSelf());
        $this->observer->setEvent(new \Magento\Framework\DataObject(['payment' => $payment]));
        $this->object->execute($this->observer);
    }

    public function testExecuteInvalidPayment()
    {
        $payment = $this->getMock('\Magento\Quote\Model\Order\Payment', [], [], '', false, false);
        $payment->expects($this->never())->method('setMethod')->with(null)->will($this->returnSelf());
        $this->observer->setEvent(new \Magento\Framework\DataObject(['payment' => $payment]));
        $this->object->execute($this->observer);
    }
}

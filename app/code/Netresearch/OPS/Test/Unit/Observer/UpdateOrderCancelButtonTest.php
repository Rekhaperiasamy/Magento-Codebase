<?php
namespace Netresearch\OPS\Test\Unit\Observer;

class UpdateOrderCancelButtonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Observer\UpdateOrderCancelButton
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
        $this->object        = $this->objectManager->getObject('\Netresearch\OPS\Observer\UpdateOrderCancelButton');
    }

    public function testExecute()
    {
        $message = __(
            "Are you sure you want to cancel this order? Warning: " .
            "Please check the payment status in the back-office of Ingenico ePayments before. " .
            "By cancelling this order you won\\'t be able to update the status in Magento anymore."
        );
        $cancelUrl = 'cancel.url';
        $paymentMethodInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\PaymentAbstract',
            [],
            [],
            '',
            false,
            false
        );
        $paymentMethodInstance->expects($this->once())->method('canCancelManually')->will($this->returnValue(true));
        $payment = new \Magento\Framework\DataObject(['method_instance' => $paymentMethodInstance]);
        $order   = new \Magento\Framework\DataObject(['payment' => $payment]);
        $block   = $this->getMock('\Magento\Sales\Block\Adminhtml\Order\View', [], [], '', false, false);
        $block->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $block->expects($this->once())->method('getCancelUrl')->will($this->returnValue($cancelUrl));
        $block->expects($this->once())->method('updateButton')->with(
            'order_cancel',
            'onclick',
            'deleteConfirm(\'' . $message . '\', \''
                                                                     . $cancelUrl . '\')'
        )->will($this->returnSelf());
        $this->observer->setBlock($block);
        $this->object->execute($this->observer);
    }

    public function testExecuteInvalidBlock()
    {
        $message = __(
            "Are you sure you want to cancel this order? " .
            "Warning: Please check the payment status in the back-office of Ingenico ePayments before. " .
            "By cancelling this order you won\\'t be able to update the status in Magento anymore."
        );
        $cancelUrl = 'cancel.url';
        $paymentMethodInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\PaymentAbstract',
            [],
            [],
            '',
            false,
            false
        );
        $paymentMethodInstance->expects($this->never())->method('canCancelManually')->will($this->returnValue(true));
        $payment = new \Magento\Framework\DataObject(['method_instance' => $paymentMethodInstance]);
        $order   = new \Magento\Framework\DataObject(['payment' => $payment]);
        $block   = $this->getMock('\Magento\Backend\Block\Template', [], [], '', false, false);
        $block->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $block->expects($this->never())->method('getCancelUrl')->will($this->returnValue($cancelUrl));
        $block->expects($this->never())->method('updateButton')->with(
            'order_cancel',
            'onclick',
            'deleteConfirm(\'' . $message . '\', \''
                                                                      . $cancelUrl . '\')'
        )->will($this->returnSelf());
        $this->observer->setBlock($block);
        $this->object->execute($this->observer);
    }

    public function testExecuteInvalidPayment()
    {
        $message = __(
            "Are you sure you want to cancel this order? " .
            "Warning: Please check the payment status in the back-office of Ingenico ePayments before. " .
            "By cancelling this order you won\\'t be able to update the status in Magento anymore."
        );
        $cancelUrl = 'cancel.url';
        $paymentMethodInstance = $this->getMock(
            '\Magento\Payment\Model\Method\Free',
            [],
            [],
            '',
            false,
            false
        );
        $paymentMethodInstance->expects($this->never())->method('canCancelManually')->will($this->returnValue(true));
        $payment = new \Magento\Framework\DataObject(['method_instance' => $paymentMethodInstance]);
        $order   = new \Magento\Framework\DataObject(['payment' => $payment]);
        $block   = $this->getMock('\Magento\Sales\Block\Adminhtml\Order\View', [], [], '', false, false);
        $block->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $block->expects($this->never())->method('getCancelUrl')->will($this->returnValue($cancelUrl));
        $block->expects($this->never())->method('updateButton')->with(
            'order_cancel',
            'onclick',
            'deleteConfirm(\'' . $message . '\', \''
                                                                      . $cancelUrl . '\')'
        )->will($this->returnSelf());
        $this->observer->setBlock($block);
        $this->object->execute($this->observer);
    }

    public function testExecuteCantCancelManually()
    {
        $message = __(
            "Are you sure you want to cancel this order? " .
            "Warning: Please check the payment status in the back-office of Ingenico ePayments before. " .
            "By cancelling this order you won\\'t be able to update the status in Magento anymore."
        );
        $cancelUrl = 'cancel.url';
        $paymentMethodInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\PaymentAbstract',
            [],
            [],
            '',
            false,
            false
        );
        $paymentMethodInstance->expects($this->once())->method('canCancelManually')->will($this->returnValue(false));
        $payment = new \Magento\Framework\DataObject(['method_instance' => $paymentMethodInstance]);
        $order   = new \Magento\Framework\DataObject(['payment' => $payment]);
        $block   = $this->getMock('\Magento\Sales\Block\Adminhtml\Order\View', [], [], '', false, false);
        $block->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $block->expects($this->never())->method('getCancelUrl')->will($this->returnValue($cancelUrl));
        $block->expects($this->never())->method('updateButton')->with(
            'order_cancel',
            'onclick',
            'deleteConfirm(\'' . $message . '\', \''
                                                                      . $cancelUrl . '\')'
        )->will($this->returnSelf());
        $this->observer->setBlock($block);
        $this->object->execute($this->observer);
    }
}

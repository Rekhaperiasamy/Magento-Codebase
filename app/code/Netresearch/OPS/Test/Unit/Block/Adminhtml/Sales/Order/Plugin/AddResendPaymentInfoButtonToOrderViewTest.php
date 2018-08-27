<?php
namespace Netresearch\OPS\Test\Unit\Block\Adminhtml\Sales\Order\Plugin;

class AddResendPaymentInfoButtonToOrderViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Block\Adminhtml\Sales\Order\Plugin\AddResendPaymentInfoButtonToOrderView
     */
    private $object;

    /**
     * @var \Magento\Sales\Block\Adminhtml\Order\View
     */
    private $block;

    /**
     * @var \Magento\Framework\View\Layout
     */
    private $layout;

    /**
     * @var \Magento\Framework\Authorization
     */
    private $authorization;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->block         = $this->getMock('\Magento\Sales\Block\Adminhtml\Order\View', [], [], '', false, false);
        $this->layout        = $this->getMock('\Magento\Framework\View\Layout', [], [], '', false, false);
        $this->authorization = $this->getMock('\Magento\Framework\Authorization', [], [], '', false, false);
        $this->object
                             = $this->objectManager->getObject(
                                 '\Netresearch\OPS\Block\Adminhtml\Sales\Order\Plugin\AddResendPaymentInfoButtonToOrderView',
                                 ['authorization' => $this->authorization]
                             );
    }

    public function testBeforeSetLayoutShowButton()
    {
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\PaymentAbstract', [], [], '', false, false);
        $payment         = new \Magento\Framework\DataObject([
                                                                 'method_instance'        => $paymentInstance,
                                                                 'additional_information' => ['status' => \Netresearch\OPS\Model\Status::PAYMENT_REFUSED]
                                                             ]);
        $order           = new \Magento\Framework\DataObject([
                                                                 'payment' => $payment,
                                                                 'state'   => \Magento\Sales\Model\Order::STATE_PROCESSING
                                                             ]);
        $this->block->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $this->authorization->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->block->expects($this->once())->method('addButton')->will($this->returnSelf());
        $this->assertNull($this->object->beforeSetLayout($this->block, $this->layout));
    }

    public function testBeforeSetLayoutIncorrectPayment()
    {
        $paymentInstance = $this->getMock('Magento\Payment\Model\Method\Free', [], [], '', false, false);
        $payment         = new \Magento\Framework\DataObject([
                                                                 'method_instance'        => $paymentInstance,
                                                                 'additional_information' => ['status' => \Netresearch\OPS\Model\Status::PAYMENT_REFUSED]
                                                             ]);
        $order           = new \Magento\Framework\DataObject([
                                                                 'payment' => $payment,
                                                                 'state'   => \Magento\Sales\Model\Order::STATE_PROCESSING
                                                             ]);
        $this->block->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $this->authorization->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->block->expects($this->never())->method('addButton')->will($this->returnSelf());
        $this->assertNull($this->object->beforeSetLayout($this->block, $this->layout));
    }

    public function testBeforeSetLayoutIncorrectOrderState()
    {
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\PaymentAbstract', [], [], '', false, false);
        $payment         = new \Magento\Framework\DataObject([
                                                                 'method_instance'        => $paymentInstance,
                                                                 'additional_information' => ['status' => \Netresearch\OPS\Model\Status::PAYMENT_REFUSED]
                                                             ]);
        $order           = new \Magento\Framework\DataObject([
                                                                 'payment' => $payment,
                                                                 'state'   => \Magento\Sales\Model\Order::STATE_COMPLETE
                                                             ]);
        $this->block->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $this->authorization->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->block->expects($this->never())->method('addButton')->will($this->returnSelf());
        $this->assertNull($this->object->beforeSetLayout($this->block, $this->layout));
    }

    public function testBeforeSetLayoutIncorrectPaymentStatus()
    {
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\PaymentAbstract', [], [], '', false, false);
        $payment         = new \Magento\Framework\DataObject([
                                                                 'method_instance'        => $paymentInstance,
                                                                 'additional_information' => ['status' => \Netresearch\OPS\Model\Status::PAYMENT_IN_PROGRESS]
                                                             ]);
        $order           = new \Magento\Framework\DataObject([
                                                                 'payment' => $payment,
                                                                 'state'   => \Magento\Sales\Model\Order::STATE_COMPLETE
                                                             ]);
        $this->block->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $this->authorization->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->block->expects($this->never())->method('addButton')->will($this->returnSelf());
        $this->assertNull($this->object->beforeSetLayout($this->block, $this->layout));
    }

    public function testBeforeSetLayoutAuthNotAllowed()
    {
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\PaymentAbstract', [], [], '', false, false);
        $payment         = new \Magento\Framework\DataObject([
                                                                 'method_instance'        => $paymentInstance,
                                                                 'additional_information' => ['status' => \Netresearch\OPS\Model\Status::PAYMENT_REFUSED]
                                                             ]);
        $order           = new \Magento\Framework\DataObject([
                                                                 'payment' => $payment,
                                                                 'state'   => \Magento\Sales\Model\Order::STATE_COMPLETE
                                                             ]);
        $this->block->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $this->authorization->expects($this->any())->method('isAllowed')->will($this->returnValue(false));
        $this->block->expects($this->never())->method('addButton')->will($this->returnSelf());
        $this->assertNull($this->object->beforeSetLayout($this->block, $this->layout));
    }
}

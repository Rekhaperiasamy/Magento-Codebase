<?php
namespace Netresearch\OPS\Test\Unit\Block\Adminhtml\Sales\Order\Plugin;

class AddStatusUpdateButtonToOrderViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Block\Adminhtml\Sales\Order\Plugin\AddStatusUpdateButtonToOrderView
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
                                 '\Netresearch\OPS\Block\Adminhtml\Sales\Order\Plugin\AddStatusUpdateButtonToOrderView',
                                 ['authorization' => $this->authorization]
                             );
    }

    public function testbeforeSetLayoutShowButton()
    {
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\PaymentAbstract', [], [], '', false, false);
        $payment         = new \Magento\Framework\DataObject([
                                                                 'method_instance' => $paymentInstance
                                                             ]);
        $order           = new \Magento\Framework\DataObject([
                                                                 'payment' => $payment
                                                             ]);
        $this->block->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $this->authorization->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->block->expects($this->once())->method('addButton')->will($this->returnSelf());
        $this->assertNull($this->object->beforeSetLayout($this->block, $this->layout));
    }

    public function testbeforeSetLayoutIncorrectPayment()
    {
        $paymentInstance = $this->getMock('Magento\Payment\Model\Method\Free', [], [], '', false, false);
        $payment         = new \Magento\Framework\DataObject([
                                                                 'method_instance' => $paymentInstance
                                                             ]);
        $order           = new \Magento\Framework\DataObject([
                                                                 'payment' => $payment
                                                             ]);
        $this->block->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $this->authorization->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->block->expects($this->never())->method('addButton')->will($this->returnSelf());
        $this->assertNull($this->object->beforeSetLayout($this->block, $this->layout));
    }

    public function testbeforeSetLayoutAuthNotAllowed()
    {
        $paymentInstance = $this->getMock('\Netresearch\OPS\Model\Payment\PaymentAbstract', [], [], '', false, false);
        $payment         = new \Magento\Framework\DataObject([
                                                                 'method_instance' => $paymentInstance
                                                             ]);
        $order           = new \Magento\Framework\DataObject([
                                                                 'payment' => $payment
                                                             ]);
        $this->block->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $this->authorization->expects($this->any())->method('isAllowed')->will($this->returnValue(false));
        $this->block->expects($this->never())->method('addButton')->will($this->returnSelf());
        $this->assertNull($this->object->beforeSetLayout($this->block, $this->layout));
    }
}

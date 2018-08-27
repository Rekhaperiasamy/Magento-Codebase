<?php

namespace Netresearch\OPS\Test\Unit\Observer;

class AppendCheckboxToRefundFormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Observer\AppendCheckboxToRefundForm
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
        $this->object        = $this->objectManager->getObject('\Netresearch\OPS\Observer\AppendCheckboxToRefundForm');
    }

    public function testExecute()
    {
        $blockName             = 'test';
        $output                = 'output';
        $templateFile          = 'template.html';
        $invoice               = new \Magento\Framework\DataObject(['transaction_id' => '123123']);
        $paymentMethodInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\PaymentAbstract',
            [],
            [],
            '',
            false,
            false
        );
        $payment               = new \Magento\Framework\DataObject(['method_instance' => $paymentMethodInstance]);
        $order                 = new \Magento\Framework\DataObject(['payment' => $payment]);
        $creditMemo            = $this->getMock('\Magento\Sales\Model\Order\Creditmemo', [], [], '', false, false);
        $creditMemo->expects($this->once())->method('canRefund')->will($this->returnValue(true));
        $creditMemo->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $creditMemo->expects($this->any())->method('getInvoice')->will($this->returnValue($invoice));
        $parentBlock = $this->getMock(
            '\Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create\Items',
            [],
            [],
            '',
            false,
            false
        );
        $parentBlock->expects($this->any())->method('getCreditmemo')->will($this->returnValue($creditMemo));
        $block = $this->getMock('\Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals', [], [], '', false, false);
        $block->expects($this->any())->method('getParentBlock')->will($this->returnValue($parentBlock));
        $checkBoxBlock = $this->getMock('\Magento\Backend\Block\Template', [], [], '', false, false);
        $checkBoxBlock->expects($this->once())->method('getTemplateFile')->will($this->returnValue($templateFile));
        $checkBoxBlock->expects($this->once())->method('fetchView')->will($this->returnValue($output));
        $layout = $this->getMock('\Magento\Framework\View\Layout', [], [], '', false, false);
        $layout->expects($this->once())->method('getBlock')->with($blockName)->will($this->returnValue($block));
        $layout->expects($this->once())
               ->method('createBlock')
               ->with(
                   'Netresearch\OPS\Block\Adminhtml\Sales\Order\Creditmemo\Totals\Checkbox',
                   'ops_refund_checkbox'
               )
               ->will($this->returnValue($checkBoxBlock));
        $transport = new \Magento\Framework\DataObject(['output' => $output]);
        $this->observer->setEvent(new \Magento\Framework\DataObject([
                                                                        'layout'       => $layout,
                                                                        'element_name' => $blockName,
                                                                        'transport'    => $transport
                                                                    ]));
        $this->object->execute($this->observer);
        $this->assertEquals($output . $output, $transport->getOutput());
    }

    public function testExecuteCantRefund()
    {
        $blockName             = 'test';
        $output                = 'output';
        $templateFile          = 'template.html';
        $invoice               = new \Magento\Framework\DataObject(['transaction_id' => '123123']);
        $paymentMethodInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\PaymentAbstract',
            [],
            [],
            '',
            false,
            false
        );
        $payment               = new \Magento\Framework\DataObject(['method_instance' => $paymentMethodInstance]);
        $order                 = new \Magento\Framework\DataObject(['payment' => $payment]);
        $creditMemo            = $this->getMock('\Magento\Sales\Model\Order\Creditmemo', [], [], '', false, false);
        $creditMemo->expects($this->once())->method('canRefund')->will($this->returnValue(false));
        $creditMemo->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $creditMemo->expects($this->any())->method('getInvoice')->will($this->returnValue($invoice));
        $parentBlock = $this->getMock(
            '\Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create\Items',
            [],
            [],
            '',
            false,
            false
        );
        $parentBlock->expects($this->any())->method('getCreditmemo')->will($this->returnValue($creditMemo));
        $block = $this->getMock('\Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals', [], [], '', false, false);
        $block->expects($this->any())->method('getParentBlock')->will($this->returnValue($parentBlock));
        $checkBoxBlock = $this->getMock('\Magento\Backend\Block\Template', [], [], '', false, false);
        $checkBoxBlock->expects($this->never())->method('getTemplateFile')->will($this->returnValue($templateFile));
        $checkBoxBlock->expects($this->never())->method('fetchView')->will($this->returnValue($output));
        $layout = $this->getMock('\Magento\Framework\View\Layout', [], [], '', false, false);
        $layout->expects($this->once())->method('getBlock')->with($blockName)->will($this->returnValue($block));
        $layout->expects($this->never())
               ->method('createBlock')
               ->with(
                   'Netresearch\OPS\Block\Adminhtml\Sales\Order\Creditmemo\Totals\Checkbox',
                   'ops_refund_checkbox'
               )
               ->will($this->returnValue($checkBoxBlock));
        $transport = new \Magento\Framework\DataObject(['output' => $output]);
        $this->observer->setEvent(new \Magento\Framework\DataObject([
                                                                        'layout'       => $layout,
                                                                        'element_name' => $blockName,
                                                                        'transport'    => $transport
                                                                    ]));
        $this->object->execute($this->observer);
        $this->assertEquals($output, $transport->getOutput());
    }

    public function testExecuteInvalidPayment()
    {
        $blockName             = 'test';
        $output                = 'output';
        $templateFile          = 'template.html';
        $invoice               = new \Magento\Framework\DataObject(['transaction_id' => '123123']);
        $paymentMethodInstance = $this->getMock('Magento\Payment\Model\Method\Free', [], [], '', false, false);
        $payment               = new \Magento\Framework\DataObject(['method_instance' => $paymentMethodInstance]);
        $order                 = new \Magento\Framework\DataObject(['payment' => $payment]);
        $creditMemo            = $this->getMock('\Magento\Sales\Model\Order\Creditmemo', [], [], '', false, false);
        $creditMemo->expects($this->never())->method('canRefund')->will($this->returnValue(true));
        $creditMemo->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $creditMemo->expects($this->any())->method('getInvoice')->will($this->returnValue($invoice));
        $parentBlock = $this->getMock(
            '\Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create\Items',
            [],
            [],
            '',
            false,
            false
        );
        $parentBlock->expects($this->any())->method('getCreditmemo')->will($this->returnValue($creditMemo));
        $block = $this->getMock('\Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals', [], [], '', false, false);
        $block->expects($this->any())->method('getParentBlock')->will($this->returnValue($parentBlock));
        $checkBoxBlock = $this->getMock('\Magento\Backend\Block\Template', [], [], '', false, false);
        $checkBoxBlock->expects($this->never())->method('getTemplateFile')->will($this->returnValue($templateFile));
        $checkBoxBlock->expects($this->never())->method('fetchView')->will($this->returnValue($output));
        $layout = $this->getMock('\Magento\Framework\View\Layout', [], [], '', false, false);
        $layout->expects($this->once())->method('getBlock')->with($blockName)->will($this->returnValue($block));
        $layout->expects($this->never())
               ->method('createBlock')
               ->with(
                   'Netresearch\OPS\Block\Adminhtml\Sales\Order\Creditmemo\Totals\Checkbox',
                   'ops_refund_checkbox'
               )
               ->will($this->returnValue($checkBoxBlock));
        $transport = new \Magento\Framework\DataObject(['output' => $output]);
        $this->observer->setEvent(new \Magento\Framework\DataObject([
                                                                        'layout'       => $layout,
                                                                        'element_name' => $blockName,
                                                                        'transport'    => $transport
                                                                    ]));
        $this->object->execute($this->observer);
        $this->assertEquals($output, $transport->getOutput());
    }

    public function testExecuteInvalidBlock()
    {
        $blockName             = 'test';
        $output                = 'output';
        $templateFile          = 'template.html';
        $invoice               = new \Magento\Framework\DataObject(['transaction_id' => '123123']);
        $paymentMethodInstance = $this->getMock(
            '\Netresearch\OPS\Model\Payment\PaymentAbstract',
            [],
            [],
            '',
            false,
            false
        );
        $payment               = new \Magento\Framework\DataObject(['method_instance' => $paymentMethodInstance]);
        $order                 = new \Magento\Framework\DataObject(['payment' => $payment]);
        $creditMemo            = $this->getMock('\Magento\Sales\Model\Order\Creditmemo', [], [], '', false, false);
        $creditMemo->expects($this->never())->method('canRefund')->will($this->returnValue(true));
        $creditMemo->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $creditMemo->expects($this->any())->method('getInvoice')->will($this->returnValue($invoice));
        $parentBlock = $this->getMock(
            '\Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create\Items',
            [],
            [],
            '',
            false,
            false
        );
        $parentBlock->expects($this->any())->method('getCreditmemo')->will($this->returnValue($creditMemo));
        $block = $this->getMock(
            '\Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create\Items',
            [],
            [],
            '',
            false,
            false
        );
        $block->expects($this->any())->method('getParentBlock')->will($this->returnValue($parentBlock));
        $checkBoxBlock = $this->getMock('\Magento\Backend\Block\Template', [], [], '', false, false);
        $checkBoxBlock->expects($this->never())->method('getTemplateFile')->will($this->returnValue($templateFile));
        $checkBoxBlock->expects($this->never())->method('fetchView')->will($this->returnValue($output));
        $layout = $this->getMock('\Magento\Framework\View\Layout', [], [], '', false, false);
        $layout->expects($this->once())->method('getBlock')->with($blockName)->will($this->returnValue($block));
        $layout->expects($this->never())
               ->method('createBlock')
               ->with(
                   'Netresearch\OPS\Block\Adminhtml\Sales\Order\Creditmemo\Totals\Checkbox',
                   'ops_refund_checkbox'
               )
               ->will($this->returnValue($checkBoxBlock));
        $transport = new \Magento\Framework\DataObject(['output' => $output]);
        $this->observer->setEvent(new \Magento\Framework\DataObject([
                                                                        'layout'       => $layout,
                                                                        'element_name' => $blockName,
                                                                        'transport'    => $transport
                                                                    ]));
        $this->object->execute($this->observer);
        $this->assertEquals($output, $transport->getOutput());
    }
}

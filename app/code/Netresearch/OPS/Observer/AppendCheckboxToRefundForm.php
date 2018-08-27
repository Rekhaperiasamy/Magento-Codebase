<?php

namespace Netresearch\OPS\Observer;

class AppendCheckboxToRefundForm implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * appends a checkbox for closing the transaction if it's a Ingenico Payment Services payment
     *
     * @event core_block_abstract_to_html_after
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();

        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $event->getLayout();
        $name = $event->getElementName();
        /** @var \Magento\Framework\View\Element\AbstractBlock $block */
        $block = $layout->getBlock($name);

        /*
         * show the checkbox only if the credit memo create page is displayed and
         * the refund can be done online and the payment is done via Ingenico Payment Services
         */
        if ($block instanceof \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals
            && $block->getParentBlock()
            instanceof \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create\Items
            && $block->getParentBlock()->getCreditmemo()->getOrder()->getPayment()
            && $block->getParentBlock()->getCreditmemo()->getOrder()->getPayment()->getMethodInstance()
            instanceof \Netresearch\OPS\Model\Payment\PaymentAbstract
            && $block->getParentBlock()->getCreditmemo()->canRefund()
            && $block->getParentBlock()->getCreditmemo()->getInvoice()
            && $block->getParentBlock()->getCreditmemo()->getInvoice()->getTransactionId()
        ) {
            $transport = $event->getTransport();
            $html = $transport->getData('output');
            /** @var \Magento\Backend\Block\Template $checkBoxBlock */
            $checkBoxBlock = $layout->createBlock(
                'Netresearch\OPS\Block\Adminhtml\Sales\Order\Creditmemo\Totals\Checkbox',
                'ops_refund_checkbox'
            );
            $html = $html . $checkBoxBlock->fetchView($checkBoxBlock->getTemplateFile());
            $transport->setData('output', $html);
        }
    }
}

<?php

namespace Netresearch\OPS\Observer;

class ShowWarningForClosedTransactions implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * fetch the creation of credit memo event and display warning message when
     * - credit memo could be done online
     * - payment is a Ingenico ePayments payment
     * - Ingenico ePayments transaction is closed
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

        /**
         * - credit memo could be done online
         * - payment is a Ingenico Payment Services payment
         * - Ingenico Payment Services transaction is closed
         */
        if ($block instanceof \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create
            && $block->getCreditmemo()->getOrder()->getPayment()
            && $block->getCreditmemo()->getOrder()->getPayment()->getMethodInstance()
            instanceof \Netresearch\OPS\Model\Payment\PaymentAbstract
            && $block->getCreditmemo()->getInvoice()
            && $block->getCreditmemo()->getInvoice()->getTransactionId()
            && false === $block->getCreditmemo()->canRefund()
        ) {
            $transport = $observer->getTransport();
            $html = $transport->getData('output');
            /** @var \Magento\Backend\Block\Template $warning */
            $warning = $layout->createBlock(
                'Netresearch\OPS\Block\Adminhtml\Sales\Order\Creditmemo\ClosedTransaction\Warning',
                'ops_closed-transaction-warning'
            );
            $html = $warning->fetchView($warning->getTemplateFile()) . $html;
            $transport->setData('output', $html);
        }
    }
}

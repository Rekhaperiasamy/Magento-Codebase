<?php

namespace Netresearch\OPS\Block\Adminhtml\Sales\Order\Invoice\Plugin;

class DisableCaptureForZeroAmountInvoice
{
    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Items $block
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @return null
     */
    public function beforeSetLayout(
        \Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Items $block,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $invoice = $block->getInvoice();
        if ($invoice->getBaseGrandTotal() <= 0.01
            && $invoice->getOrder()->getPayment()->getMethodInstance() instanceof
            \Netresearch\OPS\Model\Payment\PaymentAbstract
        ) {
            $invoice->getOrder()->getPayment()->getMethodInstance()->setCanCapture(false);
        }
        return null;
    }
}

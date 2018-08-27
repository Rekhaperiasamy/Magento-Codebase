<?php

namespace Netresearch\OPS\Observer;

use Magento\Framework\Event\Observer;

class SalesOrderPaymentCapture implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $payment \Magento\Sales\Model\Order\Payment */
        $payment = $observer->getPayment();
        $invoice = $observer->getInvoice();
        if ($payment->getMethodInstance() instanceof \Netresearch\OPS\Model\Payment\PaymentAbstract) {
            $payment->setInvoice($invoice);
        }
    }
}

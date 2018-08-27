<?php

namespace Netresearch\OPS\Observer;

class ClearPaymentMethodFromQuote implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * in some cases the payment method is not set properly by Magento so we need to reset the
     * payment method in the quote's payment before importing the data
     *
     * @event sales_quote_payment_import_data_before
     * @param \Magento\Framework\Event\Observer$observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($observer->getEvent()->getPayment() instanceof \Magento\Quote\Model\Quote\Payment) {
            $observer->getEvent()->getPayment()->setMethod(null);
        }
    }
}

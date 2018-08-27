<?php

namespace Netresearch\OPS\Observer;

class SetOrderStateDirectLink implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    protected $oPSPaymentHelper;

    /**
     * @param \Netresearch\OPS\Helper\Payment $oPSPaymentHelper
     */
    public function __construct(
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper
    ) {
        $this->oPSPaymentHelper = $oPSPaymentHelper;
    }

    /**
     * resets the order status back to pending payment in case of direct debits nl with order id as merchant ref
     * @event sales_order_payment_place_end
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $payment = $observer->getPayment();
        if ($payment->getMethodInstance() instanceof \Netresearch\OPS\Model\Payment\DirectDebit
            && $this->oPSPaymentHelper->isInlinePaymentWithOrderId($payment)
            && \Netresearch\OPS\Model\Status::AUTHORIZED == $payment->getAdditionalInformation('status')
            && $payment->getOrder()->getState() != \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT
        ) {
            $payment->getOrder()->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
            $payment->getOrder()->setStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
            $payment->getOrder()->addStatusHistoryComment(
                __('Payment has been authorized by Ingenico ePayments, but not yet captured.')
            );
        }
    }
}

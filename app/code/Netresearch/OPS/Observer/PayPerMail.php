<?php

namespace Netresearch\OPS\Observer;

use \Netresearch\OPS\Model\Payment\Features\PaymentEmail;

class PayPerMail implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var PaymentEmail
     */
    private $sendEmailModel;

    public function __construct(PaymentEmail $paymentEmail)
    {
        $this->sendEmailModel = $paymentEmail;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        if (!$order->getPayment()->getMethodInstance() instanceof \Netresearch\OPS\Model\Payment\PayPerMail) {
            return;
        }

        $this->sendEmailModel->resendPaymentInfo($order);
    }
}

<?php

namespace Netresearch\OPS\Controller\Payment;

class Placeform3dsecure extends \Netresearch\OPS\Controller\Payment
{
    /**
     * Render 3DSecure response HTML_ANSWER
     */
    public function execute()
    {
        $lastIncrementId = $this->_getCheckout()->getLastRealOrderId();

        if ($lastIncrementId) {
            $order = $this->salesOrderFactory->create();
            $order->loadByIncrementId($lastIncrementId);

            if ($order && $order->getId() && '' != $order->getPayment()->getAdditionalInformation('HTML_ANSWER')) {
                $resultPage = $this->pageFactory->create();
                return $resultPage;
            }
        }

        return $this->_redirect('checkout/onepage/success');
    }
}

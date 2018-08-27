<?php

namespace Netresearch\OPS\Controller\Payment;

class Accept extends \Netresearch\OPS\Controller\Payment
{
    /**
     * when payment gateway accept the payment, it will land to here
     * need to change order status as processed ops
     * update transaction id
     */
    public function execute()
    {
        $redirect = '';
        try {
            $order = $this->_getOrder();
            $this->_getCheckout()->setLastSuccessQuoteId($order->getQuoteId());
            $this->_getCheckout()->setLastQuoteId($order->getQuoteId());
            $this->_getCheckout()->setLastOrderId($order->getId());
        } catch (\Exception $e) {
            $this->oPSHelper->log(__('Exception in acceptAction: ' . $e->getMessage()));
            $this->oPSPaymentHelper->refillCart($this->_getOrder());
            $redirect = 'checkout/cart';
        }
        if ($redirect === '') {
            $redirect = 'checkout/onepage/success';
        }
        return $this->redirectOpsRequest($redirect);
    }
}

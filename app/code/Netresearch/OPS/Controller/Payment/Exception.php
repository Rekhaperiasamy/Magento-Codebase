<?php

namespace Netresearch\OPS\Controller\Payment;

class Exception extends \Netresearch\OPS\Controller\Payment
{
    /**
     * the payment result is uncertain
     * exception status can be 52 or 92
     * need to change order status as processing ops
     * update transaction id
     */
    public function execute()
    {
        $order = $this->_getOrder();
        $this->_getCheckout()->setLastSuccessQuoteId($order->getQuoteId());
        $this->_getCheckout()->setLastQuoteId($order->getQuoteId());
        $this->_getCheckout()->setLastOrderId($order->getId());

        $msg  = 'Your order has been registered, but your payment is still marked as pending.';
        $msg .= ' Please have patience until the final status is known.';

        $this->messageManager->addWarningMessage(__($msg));

        return $this->redirectOpsRequest('checkout/onepage/success');
    }
}

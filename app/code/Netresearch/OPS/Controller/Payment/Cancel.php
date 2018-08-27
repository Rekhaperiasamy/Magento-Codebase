<?php

namespace Netresearch\OPS\Controller\Payment;

class Cancel extends \Netresearch\OPS\Controller\Payment
{
    /**
     * when user cancel the payment
     * change order status to cancelled
     * need to redirect user to shopping cart
     */
    public function execute()
    {
        $this->_getCheckout()->setQuoteId($this->_getCheckout()->getOPSQuoteId());
        if (false == $this->_getOrder()->getId()) {
            $this->_order = null;
            $this->_getOrder($this->_getCheckout()->getLastQuoteId());
        }

        $this->getPaymentHelper()->refillCart($this->_getOrder());
        $redirect = 'checkout/cart';
        return $this->redirectOpsRequest($redirect);
    }
}

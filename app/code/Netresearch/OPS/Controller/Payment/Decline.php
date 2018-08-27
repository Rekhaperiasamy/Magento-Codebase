<?php

namespace Netresearch\OPS\Controller\Payment;

class Decline extends \Netresearch\OPS\Controller\Payment
{
    /**
     * when payment got decline
     * need to change order status to cancelled
     * take the user back to shopping cart
     */
    public function execute()
    {
        $this->_getCheckout()->setQuoteId($this->_getCheckout()->getOPSQuoteId());

        $this->getPaymentHelper()->refillCart($this->_getOrder());

        $message = __('Your payment information was declined. Please select another payment method.');
        $this->messageManager->addNotice($message);

        $redirect = 'checkout/cart';
        return $this->redirectOpsRequest($redirect);
    }
}

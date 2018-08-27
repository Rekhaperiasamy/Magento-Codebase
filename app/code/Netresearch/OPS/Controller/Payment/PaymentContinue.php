<?php
namespace Netresearch\OPS\Controller\Payment;

class PaymentContinue extends \Netresearch\OPS\Controller\Payment
{
    /**
     * when user cancel the payment and press on button "Back to Catalog" or "Back to Merchant Shop" in Orops
     */
    public function execute()
    {
        $order = $this->salesOrderFactory->create()->load(
            $this->_getCheckout()->getLastOrderId()
        );
        $this->getPaymentHelper()->refillCart($order);
        $redirect = $this->getRequest()->getParam('redirect');
        if ($redirect == 'catalog') { //In Case of "Back to Catalog" Button in OPS
            $redirect = '/';
        } else { //In Case of Cancel Auto-Redirect or "Back to Merchant Shop" Button
            $redirect = 'checkout/cart';
        }
        return $this->redirectOpsRequest($redirect);
    }
}

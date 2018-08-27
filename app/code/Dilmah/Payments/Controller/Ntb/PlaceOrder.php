<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Payments
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Payments\Controller\Ntb;

/**
 * Class PlaceOrder
 */
class PlaceOrder extends \Dilmah\Payments\Controller\Ntb\AbstractNtb
{
    /**
     * Submit the order
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        try {

            $this->_initCheckout();
            $this->_checkout->place();

            // prepare session to success or cancellation page
            $this->_getCheckoutSession()->clearHelperData();

            // "last successful quote"
            $quoteId = $this->_getQuote()->getId();
            $this->_getCheckoutSession()->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);

            // an order may be created
            $order = $this->_checkout->getOrder();
            if ($order) {
                $this->_getCheckoutSession()->setLastOrderId($order->getId())
                    ->setLastRealOrderId($order->getIncrementId())
                    ->setLastOrderStatus($order->getStatus());
            }

            $this->_redirect('checkout/onepage/success');
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                $e->getMessage()
            );
            $this->_redirect('checkout/cart');
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t place the order with American Express.')
            );
            $this->_redirect('checkout/cart');
        }
    }
}

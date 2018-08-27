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

use Magento\Checkout\Model\Type\Onepage;

class Start extends \Dilmah\Payments\Controller\Ntb\AbstractNtb
{
    /**
     * Start Express Checkout by requesting initial token and dispatching customer to PayPal
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        try {
            $this->_initCheckout();

            $customerData = $this->_customerSession->getCustomerDataObject();
            $quoteCheckoutMethod = $this->_getQuote()->getCheckoutMethod();
            if ($customerData->getId()) {
                $this->_checkout->setCustomerWithAddressChange(
                    $customerData,
                    $this->_getQuote()->getBillingAddress(),
                    $this->_getQuote()->getShippingAddress()
                );
            } elseif ((!$quoteCheckoutMethod || $quoteCheckoutMethod != Onepage::METHOD_REGISTER)
                && !$this->_objectManager->get('Magento\Checkout\Helper\Data')->isAllowedGuestCheckout(
                    $this->_getQuote(),
                    $this->_getQuote()->getStoreId()
                )
            ) {
                $this->messageManager->addNoticeMessage(
                    __('To check out, please sign in with your email address.')
                );

                $this->_objectManager->get('Magento\Checkout\Helper\ExpressRedirect')->redirectLogin($this);
                $this->_customerSession->setBeforeAuthUrl($this->_url->getUrl('*/*/*', ['_current' => true]));

                return;
            }

            $this->_checkout->start();

            $this->savePaymentHistory();
            $this->_view->loadLayout();
            $this->_view->renderLayout();
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t start American Express Checkout.\\n' . $e->getCode() . '-' . $e->getMessage())
            );
        }

        $this->_redirect('checkout/cart');
    }
}

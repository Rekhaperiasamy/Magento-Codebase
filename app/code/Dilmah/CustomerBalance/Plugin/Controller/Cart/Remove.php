<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_CustomerBalance
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\CustomerBalance\Plugin\Controller\Cart;

/**
 * Class Remove
 *
 * @package Dilmah\CustomerBalance\Plugin\Controller\Cart
 */
class Remove extends \Magento\CustomerBalance\Controller\Cart\Remove
{
    /**
     * Remove Store Credit from current quote and redirect to current page
     *
     * @param \Magento\CustomerBalance\Controller\Cart\Remove $subject
     * @param \Closure                                        $proceed
     *
     * @return void
     * @SuppressWarnings("unused")
     */
    public function aroundExecute(
        \Magento\CustomerBalance\Controller\Cart\Remove $subject,
        \Closure $proceed
    ) {
        if (!$this->_objectManager->get('Magento\CustomerBalance\Helper\Data')->isEnabled()) {
            $this->_redirect('customer/account/');
            return;
        }

        $quote = $this->_objectManager->get('Magento\Checkout\Model\Session')->getQuote();
        if ($quote->getUseCustomerBalance()) {
            $this->messageManager->addSuccess(__('The store credit payment has been removed from shopping cart.'));
            $quote->setUseCustomerBalance(false)->collectTotals()->save();
        } else {
            $this->messageManager->addError(__('You are not using store credit in your shopping cart.'));
        }
        $referrer = $this->_redirect->getRefererUrl();
        $strPart = substr(rtrim($referrer, '/'), -8);
        $_fragment = '';
        if (strtolower($strPart) == 'checkout') {
            $_fragment = '#payment';
        }
        $referrer = $referrer . $_fragment;
        $this->_redirect($referrer);
    }
}

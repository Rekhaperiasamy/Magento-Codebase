<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Reward
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Reward\Plugin\Controller\Cart;

/**
 * Class Remove
 *
 * @package Dilmah\Reward\Plugin\Controller\Cart
 */
class Remove extends \Magento\Reward\Controller\Cart\Remove
{
    /**
     * Remove Store Credit from current quote and redirect to current page
     *
     * @param \Magento\Reward\Controller\Cart\Remove $subject
     * @param \Closure $proceed
     * @return void
     * @SuppressWarnings("unused")
     */
    public function aroundExecute(
        \Magento\Reward\Controller\Cart\Remove $subject,
        \Closure $proceed
    ) {
        if (!$this->_objectManager->get('Magento\Reward\Helper\Data')->isEnabledOnFront() ||
            !$this->_objectManager->get('Magento\Reward\Helper\Data')->getHasRates()
        ) {
            return $this->_redirect('customer/account/');
        }

        $quote = $this->_objectManager->get('Magento\Checkout\Model\Session')->getQuote();

        if ($quote->getUseRewardPoints()) {
            $quote->setUseRewardPoints(false)->collectTotals()->save();
            $this->messageManager->addSuccess(__('You removed the reward points from this order.'));
        } else {
            $this->messageManager->addError(__('Reward points will not be used in this order.'));
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

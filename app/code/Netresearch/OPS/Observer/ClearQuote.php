<?php

namespace Netresearch\OPS\Observer;

use \Magento\Checkout\Model\Session as CheckoutSession;

class ClearQuote implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;


    /**
     * ClearSession constructor.
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Reset the Checkout.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * Section load get's called on retry page so we have to check for it also.
         */
        $controllerAction = $observer->getControllerAction();
        if ($this->checkoutSession->getPaymentRetryFlow()
            && !$controllerAction instanceof \Netresearch\OPS\Controller\Payment\Retry
            && !$controllerAction instanceof \Magento\Customer\Controller\Section\Load
            && !$observer->getRequest()->isAjax()
        ) {
            $this->checkoutSession->clearQuote();
            $this->checkoutSession->setPaymentRetryFlow(false);
        }

        return $this;
    }
}

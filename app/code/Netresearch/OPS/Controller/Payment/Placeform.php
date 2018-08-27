<?php
namespace Netresearch\OPS\Controller\Payment;

class Placeform extends \Netresearch\OPS\Controller\Payment
{
    /**
     * Load place from layout to make POST on ops
     */
    public function execute()
    {
        $lastIncrementId = $this->_getCheckout()->getLastRealOrderId();

        if ($lastIncrementId) {
            $order = $this->salesOrderFactory->create();
            $order->loadByIncrementId($lastIncrementId);
        }

        $this->_getCheckout()->setOPSQuoteId($this->_getCheckout()->getQuoteId());
        $this->_getCheckout()->setOPSLastSuccessQuoteId($this->_getCheckout()->getLastSuccessQuoteId());
        $this->_getCheckout()->clearQuote();

        $resultPage = $this->pageFactory->create();
        return $resultPage;
    }
}

<?php

namespace Netresearch\OPS\Controller\Payment;

class Retry extends \Netresearch\OPS\Controller\Payment
{
    /**
     * Action to retry paying the order on Ingenico
     *
     */
    public function execute()
    {
        $order = $this->_getOrder();
        $payment = $order->getPayment();
        $message = false;

        $params = [
            'SHASIGN' => $this->getRequest()->getParam('SHASIGN'),
            'orderID' => $this->getRequest()->getParam('orderID'),
        ];

        if ($this->_validateOPSData($params) === false) {
            $message = __('Hash not valid');
        } elseif ($this->canRetryPayment($payment)) {
            $this->checkoutSession->setPaymentRetryFlow(true);
            $this->checkoutSession->setOrderOnRetry($order->getIncrementId());
            $quoteId = $order->getQuoteId();
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->get($quoteId);
            $this->checkoutSession->replaceQuote($quote);
            $resultPage = $this->pageFactory->create();

            return $resultPage;
        } else {
            $message = __('Not possible to reenter the payment details for order %1', $order->getIncrementId());
        }

        if ($message) {
            $this->messageManager->addNoticeMessage($message);
        }

        return $this->redirectOpsRequest('/');
    }

    /**
     * @param \Magento\Sales\Model\Order\Payment $payment
     *
     * @return bool
     */
    private function canRetryPayment($payment)
    {
        $additionalInformation = $payment->getAdditionalInformation();
        if (is_array($additionalInformation) && array_key_exists('status', $additionalInformation)) {
            $status = $additionalInformation['status'];

            return \Netresearch\OPS\Model\Status::canResendPaymentInfo($status);
        }

        return true;
    }
}

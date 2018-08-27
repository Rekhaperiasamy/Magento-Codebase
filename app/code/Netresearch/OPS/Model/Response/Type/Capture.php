<?php
/**
 * Netresearch_OPS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 */

namespace Netresearch\OPS\Model\Response\Type;

/**
 * Capture.php
 *
 * @category Payment
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */
?>
<?php

use Magento\Framework\Exception\PaymentException;

class Capture extends \Netresearch\OPS\Model\Response\Type\TypeAbstract
{
    /**
     * Handles the specific actions for the concrete payment status
     */
    protected function _handleResponse()
    {
        if (!\Netresearch\OPS\Model\Status::isCapture($this->getStatus())) {
            throw new PaymentException(__('%1 is not a capture status!', $this->getStatus()));
        }

        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMethodInstance()->getInfoInstance();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();

        /**
         * Basically we have to check the following things here:
         *
         * Order state      - payment_review suggests an already existing intermediate status
         *                  - pending_payment or new suggests no feedback yet
         *
         * payment status   - intermediate and not failed -> move to payment review or add another comment
         *                  - intermediate and failed -> if recoverable let the order open and place comment
         *                  - finished - finish invoice dependent on order state
         */

        if (\Netresearch\OPS\Model\Status::isIntermediate($this->getStatus())) {
            $this->processIntermediateStatus($order, $payment);
        } else {
            // 9 or 95 are final statuses
            $message = $this->getFinalStatusComment();
            if ($order->getState() == \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW) {
                $payment->setNotificationResult(true);
                $payment->setPreparedMessage($message);
                if ($this->getShouldRegisterFeedback()) {
                    $payment->setIsTransactionApproved(true);
                    $payment->update(false);
                }
            } else {
                $payment->setPreparedMessage($message);
                if ($this->getShouldRegisterFeedback()) {
                    $payment->registerCaptureNotification($this->getAmount());
                }
            }
        }
        if ($this->getShouldRegisterFeedback()) {
            $order->save();
            $payment->save();
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Model\Order\Payment $payment
     */
    private function processIntermediateStatus(
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\Order\Payment $payment
    ) {
        $message = $this->getIntermediateStatusComment();
        $payment->setIsTransactionPending(true);
        if ($order->getState() == \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT
            || $order->getState() == \Magento\Sales\Model\Order::STATE_PROCESSING
        ) {
            // transaction was placed on PSP, initial feedback to shop or partial capture case
            $payment->setPreparedMessage($message);
            if ($this->getShouldRegisterFeedback()) {
                $payment->registerCaptureNotification($this->getAmount());
            }
        } elseif ($order->getState() == \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW) {
            // payment was pending and is still pending
            $payment->setIsTransactionApproved(false);
            $payment->setIsTransactionDenied(false);
            $payment->setPreparedMessage($message);

            if ($this->getShouldRegisterFeedback()) {
                $payment->update(false);
            }
        }
    }
}

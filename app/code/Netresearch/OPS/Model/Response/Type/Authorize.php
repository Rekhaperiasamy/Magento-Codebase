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
 * Authorize.php
 *
 * @category Payment
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */
?>
<?php

use Magento\Framework\Exception\PaymentException;

class Authorize extends \Netresearch\OPS\Model\Response\Type\TypeAbstract
{
    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    protected $oPSPaymentHelper;


    public function __construct(
        \Netresearch\OPS\Model\Config $config,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Netresearch\OPS\Helper\Alias $aliasHelper,
        array $data = []
    ) {
        parent::__construct($config, $aliasHelper, $data);
        $this->oPSPaymentHelper = $oPSPaymentHelper;
    }

    /**
     * Handles the specific actions for the concrete payment status
     */
    protected function _handleResponse()
    {
        if (!\Netresearch\OPS\Model\Status::isAuthorize($this->getStatus())) {
            throw new PaymentException(__('%1 is not a authorize status!', $this->getStatus()));
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMethodInstance()->getInfoInstance()->getOrder();
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMethodInstance()->getInfoInstance();

        // if no parent transaction id has been set yet set the parentTransactionId so we can void
        if (!$payment->getParentTransactionId()) {
            $payment->setParentTransactionId($this->getPayid());
        }

        if (\Netresearch\OPS\Model\Status::isFinal($this->getStatus())) {
            $this->handleAuthorizationDeclined($order, $payment);
            $this->registerFeedback($order, $payment);
        } else {
            $payment->setIsTransactionPending(true);
            if ($this->getStatus() == \Netresearch\OPS\Model\Status::AUTHORIZED_WAITING_EXTERNAL_RESULT) {
                $payment->setIsFraudDetected(true);
                $order->setState(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW);
                $order->setStatus(\Magento\Sales\Model\Order::STATUS_FRAUD);
                $order->addStatusHistoryComment(
                    __('Please have a look in Ingenico ePayments backend for more information.')
                );
            } else {
                $order->addStatusHistoryComment($this->getIntermediateStatusComment());
            }
            if ($this->getShouldRegisterFeedback()) {
                $payment->registerAuthorizationNotification($this->getAmount());
            }
        }

        if ($this->getShouldRegisterFeedback()) {
            $order->save();
            $payment->save();
        }

        return $this;
    }

    /**
     * handle authorization declined
     * thrown exception gets catched by core and order will not been created
     *
     * @throws PaymentException
     */
    protected function handleAuthorizationDeclined(
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\Order\Payment $payment
    ) {
        if ($this->getStatus() == \Netresearch\OPS\Model\Status::AUTHORISATION_DECLINED) {
            if (!$this->getShouldRegisterFeedback()) {
                throw new PaymentException(
                    __(
                        'Payment failed because the authorization was declined! ' .
                        'Please choose another payment method.'
                    )
                );
            } elseif ($order->getState() === \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW) {
                try {
                    // if the payment was previously in payment review/has status 46 the identification obviously
                    // failed and the order gets canceled
                    $payment->setNotificationResult(true);
                    $payment->deny(false);
                } catch (\Exception $e) {
                    if ($e->getMessage() === __('Order does not allow to be canceled.')) {
                        $this->manuallyCancelOrder($order);
                    }
                }
            }
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Model\Order\Payment $payment
     */
    protected function registerFeedback(\Magento\Sales\Model\Order $order, \Magento\Sales\Model\Order\Payment $payment)
    {
        if ($this->getShouldRegisterFeedback()) {
            if ($order->getState() === \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW) {
                if (\Netresearch\OPS\Model\Status::canResendPaymentInfo($this->getStatus())
                    && $this->oPSPaymentHelper->isInlinePayment($payment)
                ) {
                    $targetState = \Magento\Sales\Model\Order::STATE_CANCELED;
                    $payment->setNotificationResult(true);
                    $payment->deny(false);
                } else {
                    $targetState = \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT;
                    $payment->setIsTransactionApproved(true);
                    $payment->update(false);
                }

                if ($order->getState() != $targetState) {
                    $order->setState($targetState);
                    $order->addStatusHistoryComment($this->getFinalStatusComment(), true);
                }
            } elseif ($order->getState() === \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT
                || $order->getState() === \Magento\Sales\Model\Order::STATE_NEW
            ) {
                $payment->registerAuthorizationNotification($this->getAmount());
                $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
                $order->addStatusHistoryComment($this->getFinalStatusComment(), true);
            }
        } else {
            $this->addFinalStatusComment();
        }
    }

    /**
     * if fail with exception and the order is in payment_review state.
     * we therefore cancel the order 'manually'.
     *
     * below code is c&p from \Magento\Sales\Model\Order::registerCancellation:
     * @see \Magento\Sales\Model\Order::registerCancellation
     *
     * @param \Magento\Sales\Model\Order $order
     */
    protected function manuallyCancelOrder(\Magento\Sales\Model\Order $order)
    {
        $cancelState = \Magento\Sales\Model\Order::STATE_CANCELED;
        foreach ($order->getAllItems() as $item) {
            if ($cancelState != \Magento\Sales\Model\Order::STATE_PROCESSING
                && $item->getQtyToRefund()
            ) {
                if ($item->getQtyToShip() > $item->getQtyToCancel()) {
                    $cancelState = \Magento\Sales\Model\Order::STATE_PROCESSING;
                } else {
                    $cancelState = \Magento\Sales\Model\Order::STATE_COMPLETE;
                }
            }
            $item->cancel();
        }

        $order->setSubtotalCanceled($order->getSubtotal() - $order->getSubtotalInvoiced());
        $order->setBaseSubtotalCanceled(
            $order->getBaseSubtotal() - $order->getBaseSubtotalInvoiced()
        );

        $order->setTaxCanceled($order->getTaxAmount() - $order->getTaxInvoiced());
        $order->setBaseTaxCanceled($order->getBaseTaxAmount() - $order->getBaseTaxInvoiced());

        $order->setShippingCanceled($order->getShippingAmount() - $order->getShippingInvoiced());
        $order->setBaseShippingCanceled(
            $order->getBaseShippingAmount() - $order->getBaseShippingInvoiced()
        );

        $order->setDiscountCanceled(
            abs($order->getDiscountAmount()) - $order->getDiscountInvoiced()
        );
        $order->setBaseDiscountCanceled(
            abs($order->getBaseDiscountAmount()) - $order->getBaseDiscountInvoiced()
        );

        $order->setTotalCanceled($order->getGrandTotal() - $order->getTotalPaid());
        $order->setBaseTotalCanceled($order->getBaseGrandTotal() - $order->getBaseTotalPaid());

        $order->setState($cancelState);
        $order->addStatusHistoryComment($this->getFinalStatusComment(), true);
    }
}

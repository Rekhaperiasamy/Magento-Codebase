<?php

namespace Netresearch\OPS\Helper\Order;

/**
 * Created by PhpStorm.
 * User: paul.siedler
 * Date: 18.09.2014
 * Time: 13:48
 */
abstract class AbstractHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    protected $oPSPaymentHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper
    ) {
        parent::__construct($context);
        $this->oPSPaymentHelper = $oPSPaymentHelper;
    }

    /**
     * Return partial operation code for transaction type
     *
     * @return string Operation code defined in \Netresearch\OPS\Model\Payment\PaymentAbstract
     */
    abstract protected function getPartialOperationCode();

    /**
     * Return full operation code for transaction type
     *
     * @return string Operation code defined in \Netresearch\OPS\Model\Payment\PaymentAbstract
     */
    abstract protected function getFullOperationCode();

    /**
     * Checks if partial capture and returns 'full' or 'partial'
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @param float $amount
     * @return string 'partial' if type is partial, else 'full'
     */
    public function determineType($payment, $amount)
    {
        $orderTotalAmount = round(
            ($this->oPSPaymentHelper->getBaseGrandTotalFromSalesObject($payment->getOrder())) * 100,
            0
        );
        $amount           = round(($amount * 100), 0);

        if (abs($orderTotalAmount - $amount) <= 1) {
            return 'full';
        } else {
            return 'partial';
        }
    }

    /**
     * checks if the amount captured/refunded is equal to the amount of the full order
     * and returns the operation code accordingly
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @param float $amount
     * @return string operation code for the requested amount
     * @see getPartialOperationCode() and getFullOperationCode()
     */
    public function determineOperationCode($payment, $amount)
    {
        $orderTotalAmount = round(
            ($this->oPSPaymentHelper->getBaseGrandTotalFromSalesObject($payment->getOrder())) * 100,
            0
        );
        $totalProcessedAmount      = round((($this->getPreviouslyProcessedAmount($payment) + $amount) * 100), 0);

        if (abs($orderTotalAmount - $totalProcessedAmount) <= 1) {
            return $this->getFullOperationCode();
        } else {
            return $this->getPartialOperationCode();
        }
    }

    /**
     * Returns the Amount already processed for this kind of operation
     * eg. getBaseAmountPaidOnline and getRefundedAmount
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @return float amount already processed for this kind of operation
     */
    abstract protected function getPreviouslyProcessedAmount($payment);
}

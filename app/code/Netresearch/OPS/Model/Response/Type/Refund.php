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
 * Refund.php
 *
 * @category Payment
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */
?>
<?php

use Magento\Framework\Exception\PaymentException;

class Refund extends \Netresearch\OPS\Model\Response\Type\TypeAbstract
{
    /**
     * @var \Netresearch\OPS\Helper\Order\Refund
     */
    protected $opsOrderRefundHelper;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $dbTransactionFactory;

    /**
     * @var \Magento\Sales\Model\Order\InvoiceFactory
     */
    protected $invoiceFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Metadata
     */
    protected $metadata;

    /**
     * @var \Magento\Sales\Api\TransactionRepositoryInterface
     */
    protected $transactionRepository;


    public function __construct(
        \Netresearch\OPS\Model\Config $config,
        \Netresearch\OPS\Helper\Order\Refund $opsOrderRefundHelper,
        \Magento\Framework\DB\TransactionFactory $dbTransactionFactory,
        \Magento\Sales\Model\Order\InvoiceFactory $invoiceFactory,
        \Magento\Sales\Model\ResourceModel\Metadata $metadata,
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository,
        \Netresearch\OPS\Helper\Alias $aliasHelper,
        array $data = []
    ) {
        parent::__construct($config, $aliasHelper, $data);
        $this->opsOrderRefundHelper = $opsOrderRefundHelper;
        $this->dbTransactionFactory = $dbTransactionFactory;
        $this->invoiceFactory = $invoiceFactory;
        $this->metadata = $metadata;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Handles the specific actions for the concrete payment status
     */
    protected function _handleResponse()
    {
        if (!\Netresearch\OPS\Model\Status::isRefund($this->getStatus())) {
            throw new PaymentException(__('%1 is not a refund status!', $this->getStatus()));
        }

        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMethodInstance()->getInfoInstance();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();

        $creditMemo = $this->getCreditmemo($payment);

        if ($creditMemo->getId()) {
            if ($this->getStatus() == \Netresearch\OPS\Model\Status::REFUND_REFUSED) {
                $this->processRefusedRefund($creditMemo);
            } elseif (\Netresearch\OPS\Model\Status::isFinal($this->getStatus())
                && $creditMemo->getState() == \Magento\Sales\Model\Order\Creditmemo::STATE_OPEN
            ) {
                $creditMemo->setState(\Magento\Sales\Model\Order\Creditmemo::STATE_REFUNDED);
                $this->closeRefundTransaction($creditMemo);
                $this->addFinalStatusComment();
            } else {
                $creditMemo->setState(\Magento\Sales\Model\Order\Creditmemo::STATE_OPEN);
                $this->addIntermediateStatusComment();
            }
        } else {
            if ($this->getShouldRegisterFeedback()) {
                $payment->setParentTransactionId($this->getPayid());
                $payment->setTransactionId($this->getTransactionId());
                $payment->setIsTransactionClosed(\Netresearch\OPS\Model\Status::isFinal($this->getStatus()));
                $payment->registerRefundNotification($this->getAmount());
            }
            if (\Netresearch\OPS\Model\Status::isFinal($this->getStatus())) {
                $this->addFinalStatusComment();
            } else {
                $this->addIntermediateStatusComment();
                $creditMemo = $payment->getCreatedCreditmemo() ?: $payment->getCreditmemo();
                if ($creditMemo) {
                    $creditMemo->setState(\Magento\Sales\Model\Order\Creditmemo::STATE_OPEN);
                    $creditMemo->setTransactionId($payment->getLastTransId());
                }
            }
        }

        $transactionSave = $this->dbTransactionFactory->create();

        if ($this->getShouldRegisterFeedback()) {
            $transactionSave->addObject($order)
                ->addObject($payment);
            if ($creditMemo->getInvoice()) {
                $transactionSave->addObject($creditMemo->getInvoice());
            }
        }

        $transactionSave->addObject($creditMemo);
        $transactionSave->save();
    }

    /**
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @return \Magento\Sales\Model\Order\Creditmemo|null
     */
    protected function getCreditmemo(\Magento\Sales\Model\Order\Payment $payment)
    {
        if (!$this->hasPayidsub()) {
            $creditMemo = $this->determineCreditMemo();
            $payment->setRefundTransactionId($creditMemo->getTransactionId());
        } else {
            $creditMemo = $this->metadata->getNewInstance()->load($this->getTransactionId(), 'transaction_id');
            $payment->setRefundTransactionId($this->getTransactionId());
        }

        return $creditMemo;
    }

    /**
     * Will load the creditmemo by identifying open refund transactions
     *
     * @return \Magento\Sales\Model\Order\Creditmemo|null
     */
    protected function determineCreditMemo()
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMethodInstance()->getInfoInstance();
        $refundTransaction = $this->opsOrderRefundHelper->getOpenRefundTransaction($payment);
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $this->metadata->getNewInstance()->load($refundTransaction->getTxnId(), 'transaction_id');

        return $creditmemo;
    }

    /**
     * Closes the refund transaction for the given creditmemo
     *
     * @param $creditMemo
     */
    protected function closeRefundTransaction($creditMemo)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMethodInstance()->getInfoInstance();
        /** @var \Magento\Sales\Model\Order\Payment\Transaction $refundTransaction */
        $refundTransaction = $this->transactionRepository->getByTransactionId(
            $creditMemo->getTransactionId(),
            $payment->getId(),
            $payment->getOrder()->getId()
        );
        if ($refundTransaction) {
            $refundTransaction->setIsClosed(true)
                ->save();
        }
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     */
    public function processRefusedRefund(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $this->cancelCreditmemo($creditmemo);
        $this->closeRefundTransaction($creditmemo);
        $invoice = $this->invoiceFactory->create()->load($creditmemo->getInvoiceId());
        $invoice->setIsUsedForRefund(0)
            ->setBaseTotalRefunded(
                $invoice->getBaseTotalRefunded() - $creditmemo->getBaseGrandTotal()
            );
        $creditmemo->setInvoice($invoice);
        /** @var \Magento\Sales\Model\Order\Creditmemo\Item $item */
        foreach ($creditmemo->getAllItems() as $item) {
            $item->getOrderItem()->setAmountRefunded(
                $item->getOrderItem()->getAmountRefunded() - $item->getRowTotal()
            );
            $item->getOrderItem()->setBaseAmountRefunded(
                $item->getOrderItem()->getBaseAmountRefunded() - $item->getBaseRowTotal()
            );
        }
        $order->setTotalRefunded($order->getTotalRefunded() - $creditmemo->getBaseGrandTotal());
        $order->setBaseTotalRefunded($order->getBaseTotalRefunded() - $creditmemo->getBaseGrandTotal());

        $this->addRefusedStatusComment();
        $state = \Magento\Sales\Model\Order::STATE_COMPLETE;
        if ($order->canShip() || $order->canInvoice()) {
            $state = \Magento\Sales\Model\Order::STATE_PROCESSING;
        }
        $order->setState($state);
        $order->addStatusHistoryComment(
            $this->getRefusedStatusComment(__('Refund refused by Ingenico ePayments.')),
            true
        );
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @throws PaymentException
     */
    protected function cancelCreditmemo(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        try {
            $creditmemo->setState(\Magento\Sales\Model\Order\Creditmemo::STATE_CANCELED);
            foreach ($creditmemo->getAllItems() as $item) {
                $item->cancel();
            }
            $creditmemo->save();
            /** @var \Magento\Sales\Model\Order\Payment $payment */
            $payment = $this->getMethodInstance()->getInfoInstance();
            $payment->cancelCreditmemo($creditmemo);
        } catch (\Exception $e) {
            throw new PaymentException(__('Could not cancel creditmemo'), $e);
        }
    }
}

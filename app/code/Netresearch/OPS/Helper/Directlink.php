<?php
/**
 * \Netresearch\OPS\Helper\DirectLink
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    André Herrn <andre.herrn@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory as TransactionCollectionFactory;

class Directlink extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    protected $oPSPaymentHelper;

    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    /**
     * @var \Netresearch\OPS\Model\Api\DirectLink
     */
    protected $oPSApiDirectlink;

    /**
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    protected $checkoutTypeOnepage;

    /**
     * @var TransactionCollectionFactory
     */
    protected $salesTransactionCollectionFactory;

    /**
     * @var \Netresearch\OPS\Model\Response\Handler
     */
    protected $responseHandler;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Model\Api\DirectLink $oPSApiDirectlink,
        \Magento\Checkout\Model\Type\Onepage $checkoutTypeOnepage,
        TransactionCollectionFactory $salesTransactionCollectionFactory,
        \Netresearch\OPS\Model\Response\Handler $responseHandler
    ) {
        parent::__construct($context);
        $this->oPSHelper = $oPSHelper;
        $this->oPSPaymentHelper = $oPSPaymentHelper;
        $this->oPSConfigFactory = $oPSConfigFactory;
        $this->oPSApiDirectlink = $oPSApiDirectlink;
        $this->checkoutTypeOnepage = $checkoutTypeOnepage;
        $this->salesTransactionCollectionFactory = $salesTransactionCollectionFactory;
        $this->responseHandler = $responseHandler;
    }

    /**
     * Creates Transactions for directlink activities
     *
     * @param \Magento\Sales\Model\Order $order
     * @param int $transactionID - persistent transaction id
     * @param int $subPayID - identifier for each transaction
     * @param array $arrInformation - add dynamic data
     *
     * @return \Netresearch\OPS\Helper\Directlink $this
     */
    public function directLinkTransact(
        $order,
        $transactionID,
        $subPayID,
        $arrInformation = [],
        $closed = 0
    ) {
        $payment = $order->getPayment();
        $payment->setTransactionId($transactionID."/".$subPayID);
        $payment->setParentTransactionId($transactionID);
        $payment->setIsTransactionClosed($closed);
        $payment->setTransactionAdditionalInfo($arrInformation, null);
        return $this;
    }

    /**
     * Checks if there is an active transaction for a special order for special
     * type
     *
     * @param string $type - refund, capture etc.
     * @param int $orderID
     * @return bool success
     */
    public function checkExistingTransact($type, $orderID)
    {
        $transaction = $this->salesTransactionCollectionFactory->create()
            ->addAttributeToFilter('order_id', $orderID)
            ->addAttributeToFilter('txn_type', $type)
            ->addAttributeToFilter('is_closed', 0)
            ->getLastItem();

        return ($transaction->getTxnId()) ? true : false;
    }

    /**
     * get transaction type for given OPS status
     *
     * @param string $status
     *
     * @return string
     * @codingStandardsIgnoreStart
     */
    public function getTypeForStatus($status)
    {
        switch ($status) {
            case \Netresearch\OPS\Model\Status::REFUNDED:
            case \Netresearch\OPS\Model\Status::REFUND_PENDING:
            case \Netresearch\OPS\Model\Status::REFUND_UNCERTAIN:
            case \Netresearch\OPS\Model\Status::REFUND_REFUSED:
            case \Netresearch\OPS\Model\Status::REFUNDED_OK:
                return \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_TRANSACTION_TYPE;
            case \Netresearch\OPS\Model\Status::PAYMENT_REQUESTED:
            case \Netresearch\OPS\Model\Status::PAYMENT_PROCESSED_BY_MERCHANT:
            case \Netresearch\OPS\Model\Status::PAYMENT_PROCESSING:
            case \Netresearch\OPS\Model\Status::PAYMENT_UNCERTAIN:
            case \Netresearch\OPS\Model\Status::PAYMENT_IN_PROGRESS:
            case \Netresearch\OPS\Model\Status::PAYMENT_REFUSED:
            case \Netresearch\OPS\Model\Status::PAYMENT_DECLINED_BY_ACQUIRER:
                return \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_TRANSACTION_TYPE;
            case \Netresearch\OPS\Model\Status::AUTHORIZED_AND_CANCELLED: //Void finished
            case \Netresearch\OPS\Model\Status::AUTHORIZED_AND_CANCELLED_OK:
            case \Netresearch\OPS\Model\Status::DELETION_WAITING:
            case \Netresearch\OPS\Model\Status::DELETION_UNCERTAIN:
            case \Netresearch\OPS\Model\Status::DELETION_REFUSED:
                return \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_VOID_TRANSACTION_TYPE;
            case \Netresearch\OPS\Model\Status::PAYMENT_DELETED:
            case \Netresearch\OPS\Model\Status::PAYMENT_DELETION_PENDING:
            case \Netresearch\OPS\Model\Status::PAYMENT_DELETION_UNCERTAIN:
            case \Netresearch\OPS\Model\Status::PAYMENT_DELETION_REFUSED:
            case \Netresearch\OPS\Model\Status::PAYMENT_DELETION_OK:
            case \Netresearch\OPS\Model\Status::DELETION_HANDLED_BY_MERCHANT:
                return \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_DELETE_TRANSACTION_TYPE;
        }
    }
    // @codingStandardsIgnoreEnd

    /**
     * Process Direct Link Feedback to do: Capture, De-Capture and Refund
     *
     * @param \Magento\Sales\Model\Order $order  Order
     * @param array $params Request params
     *
     * @return void
     */
    public function processFeedback($order, $params)
    {
        $this->responseHandler->processResponse($params, $order->getPayment()->getMethodInstance());
        $order->getPayment()->save();
    }

    /**
     * Get the payment transaction by PAYID and Operation
     *
     * @param \Magento\Sales\Model\Order $order
     * @param int                        $payId
     * @param string                     $authorization
     *
     * @return \Magento\Sales\Model\Order\Payment\Transaction
     *
     * @throws \Exception
     */
    public function getPaymentTransaction($order, $payId, $operation)
    {
        $transactionCollection = $this->salesTransactionCollectionFactory->create()
            ->addAttributeToFilter('txn_type', $operation)
            ->addAttributeToFilter('is_closed', 0)
            ->addAttributeToFilter('order_id', $order->getId());
        if ($payId != '') {
            $transactionCollection->addAttributeToFilter('parent_txn_id', $payId);
        }

        if ($transactionCollection->count()>1 || $transactionCollection->count() == 0) {
            $errorMsq = __(
                "Warning, transaction count is %1 instead of 1 for the Payid '%2', order '%3' and Operation '%4'.",
                $transactionCollection->count(),
                $payId,
                $order->getId(),
                $operation
            );
            $this->oPSHelper->log($errorMsq);
            throw new \Magento\Framework\Exception\LocalizedException($errorMsq);
        }

        if ($transactionCollection->count() == 1) {
            $transaction = $transactionCollection->getLastItem();
            $transaction->setOrderPaymentObject($order->getPayment());
            return $transaction;
        }
    }

    /**
     * Check if there are payment transactions for an order and an operation
     *
     * @param \Magento\Sales\Model\Order $order
     * @param string $authorization
     *
     * @return boolean
     */
    public function hasPaymentTransactions($order, $operation)
    {
        $transactionCollection = $this->salesTransactionCollectionFactory->create()
            ->addAttributeToFilter('txn_type', $operation)
            ->addAttributeToFilter('is_closed', 0)
            ->addAttributeToFilter('order_id', $order->getId());

        return (0 < $transactionCollection->count());
    }

    /**
     * validate incoming and internal amount value format and convert it to float
     *
     * @param string
     * @return float
     */
    public function formatAmount($amount)
    {
        // Avoid quotes added somewhere unknown
        if (preg_match("/^[\"']([0-9-\..,-]+)[\"']$/i", $amount, $matches)) {
            $this->oPSHelper
                ->log("Warning in formatAmount: Found quotes around amount in '" . var_export($amount, true) . "'");
            $amount = $matches[1];
        }

        return number_format($amount, 2);
    }

    /**
     * determine if the current OPS request is valid
     *
     * @param mixed $openTransaction
     * @param \Magento\Sales\Model\Order $order
     * @param array $opsRequestParams
     *
     * @return boolean
     */
    public function isValidOpsRequest($openTransaction, \Magento\Sales\Model\Order $order, $opsRequestParams)
    {
        $typeForStatus = $this->getTypeForStatus($opsRequestParams['STATUS']);
        if ($typeForStatus == \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_DELETE_TRANSACTION_TYPE) {
            return false;
        }

        $requestedAmount = null;
        if (array_key_exists('amount', $opsRequestParams)) {
            $requestedAmount = $this->formatAmount($opsRequestParams['amount']);
        }

        /* find expected amount */
        $expectedAmount = null;
        $transactionInfo = unserialize($openTransaction->getAdditionalInformation('arrInfo'));
        if (array_key_exists('amount', $transactionInfo)) {
            if (null === $expectedAmount || $transactionInfo['amount'] == $requestedAmount) {
                $expectedAmount = $this->formatAmount($transactionInfo['amount']);
            }
        }

        if ($typeForStatus == \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_TRANSACTION_TYPE
            || $typeForStatus == \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_VOID_TRANSACTION_TYPE
        ) {
            if (null === $requestedAmount
                || 0 == count($openTransaction)
                || $requestedAmount != $expectedAmount
            ) {
                return false;
            }
        }

        if ($typeForStatus == \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_TRANSACTION_TYPE) {
            if (null === $requestedAmount) {
                $this->oPSHelper->log('Please configure Ingenico ePayments to submit amount');
                return false;
            }
            $grandTotal = $this->formatAmount($this->oPSPaymentHelper->getBaseGrandTotalFromSalesObject($order));
            if ($grandTotal != $requestedAmount && $expectedAmount != $requestedAmount) {
                return false;
            }
        }
        return true;
    }

    public function performDirectLinkRequest($quote, $params, $storeId = null)
    {
        $url = $this->oPSConfigFactory->create()->getDirectLinkGatewayOrderPath($storeId);
        $response = $this->oPSApiDirectlink->performRequest($params, $url, $storeId);

        /**
         * allow null as valid state for creating the order with status 'pending'
         */
        if (null !== $response['STATUS'] && $this->oPSPaymentHelper->isPaymentFailed($response['STATUS'])) {
            throw new \Magento\Framework\Exception\PaymentException(__('Ingenico ePayments Payment failed'));
        }
        return $response;
    }
}

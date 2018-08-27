<?php

namespace Netresearch\OPS\Helper\Order;

/**
 * @package
 * @copyright 2011 Netresearch
 * @author    André Herrn <andre.herrn@netresearch.de>
 * @license   OSL 3.0
 */
class Refund extends \Netresearch\OPS\Helper\Order\AbstractHelper
{
    protected $payment;
    protected $amount;
    protected $params;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory
     */
    protected $salesPaymentTransactionsFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Sales\Model\Order\InvoiceFactory
     */
    protected $salesOrderInvoiceFactory;

    /**
     * @var \Netresearch\OPS\Helper\Directlink
     */
    protected $oPSDirectlinkHelper;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $salesOrderFactory;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\CatalogInventory\Helper\Data
     */
    protected $catalogInventoryHelper;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $frameworkTransactionFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory $salesPaymentTransactionsFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Model\Order\InvoiceFactory $salesOrderInvoiceFactory
    ) {
        parent::__construct($context, $oPSPaymentHelper);
        $this->salesPaymentTransactionsFactory = $salesPaymentTransactionsFactory;
        $this->request = $request;
        $this->salesOrderInvoiceFactory = $salesOrderInvoiceFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFullOperationCode()
    {
        return \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_FULL;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPartialOperationCode()
    {
        return \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_PARTIAL;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPreviouslyProcessedAmount($payment)
    {
        return $payment->getBaseAmountRefundedOnline();
    }

    /**
     * @param \Magento\Framework\DataObject $payment
     * @return $this
     */
    public function setPayment(\Magento\Framework\DataObject $payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param  array $params
     * @return $this
     */
    public function setCreditMemoRequestParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return array params
     */
    public function getCreditMemoRequestParams()
    {
        if (!is_array($this->params)) {
            $this->setCreditMemoRequestParams($this->request->getParams());
        }

        return $this->params;
    }

    public function getInvoiceFromCreditMemoRequest()
    {
        $params = $this->getCreditMemoRequestParams();
        if (array_key_exists('invoice_id', $params)) {
            return $this->salesOrderInvoiceFactory->create()->load($params['invoice_id']);
        }

        return null;
    }

    public function getCreditMemoFromRequest()
    {
        $params = $this->getCreditMemoRequestParams();
        if (array_key_exists('creditmemo', $params)) {
            return $params['creditmemo'];
        }

        return [];
    }

    /**
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @param float $amount
     */
    public function prepareOperation($payment, $amount)
    {
        $params = $this->getCreditMemoRequestParams();

        if (array_key_exists('creditmemo', $params)) {
            $arrInfo           = $params['creditmemo'];
            $arrInfo['amount'] = $amount;
        }
        $arrInfo['type']      = $this->determineType($payment, $amount);
        $arrInfo['operation'] = $this->determineOperationCode($payment, $amount);

        if ($arrInfo['type'] == 'full') {
            // hard overwrite operation code for last transaction
            $arrInfo['operation'] = $this->getFullOperationCode();
        }

        return $arrInfo;
    }

    /**
     * Checks for open refund transaction
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     *
     * @return \Magento\Sales\Model\Order\Payment\Transaction|null
     */
    public function getOpenRefundTransaction($payment)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection $refundTransactions */
        $refundTransactions = $this->salesPaymentTransactionsFactory->create();
        $transaction = $refundTransactions->addPaymentIdFilter($payment->getId())
            ->addTxnTypeFilter(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_REFUND)
            ->setOrderFilter($payment->getOrder())
            ->addFieldToFilter('is_closed', 0)
            ->getFirstItem();

        return $transaction;
    }
}

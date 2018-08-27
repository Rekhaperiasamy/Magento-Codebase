<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Payments
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Payments\Model\Ntb;

use Magento\Customer\Api\Data\CustomerInterface as CustomerDataObject;
use Magento\Framework\Webapi\Exception;
use Magento\Quote\Model\Quote\Address;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;

/**
 * Wrapper that performs American express and Checkout communication
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Checkout
{
    /**
     * Value for SaleTxn
     */
    const SALETXN = 'SaleTxn';

    /**
     * Value for SaleMerchUpdated
     */
    const SALEMERCHUPDATED = 'SaleMerchUpdated';

    /**
     * Value for SaleTxnVerify
     */
    const SALETXNVERIFY = 'SaleTxnVerify';

    /**
     * Accepted
     */
    const ACCEPTED = 'ACCEPTED';

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $_quote;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Customer ID
     *
     * @var int
     */
    protected $_customerId;

    /**
     * Order
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * Checkout data
     *
     * @var \Magento\Checkout\Helper\Data
     */
    protected $_checkoutData;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var OrderSender
     */
    protected $orderSender;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $quoteManagement;

    /**
     * Merchant Id
     *
     * @var string
     */
    protected $_merchantId = null;

    /**
     * IPG Client Ip address
     *
     * @var string
     */
    protected $_ipgClientIp = null;

    /**
     * Socket Timeout
     *
     * @var string
     */
    protected $_socketTimeout = null;

    /**
     * IPG Client Port
     *
     * @var string
     */
    protected $_ipgClientPort = null;

    /**
     * IPG Server Url
     *
     * @var string
     */
    protected $_ipgServerUrl = '';

    /**
     * debug mode
     *
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_debugModeEnabled = 0;

    /**
     * Error No
     *
     * @var string
     */
    protected $_errNo = null;

    /**
     * Error String
     *
     * @var string
     */
    protected $_errString = '';

    /**
     * Socket Timeout
     *
     * @var string
     */
    protected $_ipgSocket = '';

    /**
     * Error message
     *
     * @var string
     */
    protected $_errorMessage = '';

    /**
     * Invoice Sent Error
     *
     * @var string
     */
    protected $_invoiceSentError = '';

    /**
     * Encryption error
     *
     * @var string
     */
    protected $_encryptionErr = '';

    /**
     * Invoice
     *
     * @var string
     */
    protected $_invoice = '';

    /**
     * Encrypted invoice
     *
     * @var string
     */
    protected $_encryptedInvoice = '';

    /**
     * Return Url
     *
     * @var string
     */
    protected $_returnUrl = '';

    /**
     * @var string
     */
    protected $transactionStatus;

    /**
     * @var bool|null
     */
    protected $receiptSentError = null;

    /**
     * @var bool|null
     */
    protected $decryptionErr = null;

    /**
     * @var bool|null
     */
    protected $socketCreationErr = null;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var \Dilmah\Payments\Logger\Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $_ipgTxnId;

    /**
     * Checkout constructor.
     *
     * @param \Magento\Checkout\Helper\Data $checkoutData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Quote\Api\CartManagementInterface $quoteManagement
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param OrderSender $orderSender
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Dilmah\Payments\Logger\Logger $logger
     * @param array $params
     * @throws \Exception
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Checkout\Helper\Data $checkoutData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        OrderSender $orderSender,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Dilmah\Payments\Logger\Logger $logger,
        $params = []
    ) {
        $this->quoteManagement = $quoteManagement;
        $this->_checkoutData = $checkoutData;
        $this->_checkoutSession = $checkoutSession;
        $this->_messageManager = $messageManager;
        $this->orderSender = $orderSender;
        $this->quoteRepository = $quoteRepository;
        $this->transactionFactory = $transactionFactory;
        $this->logger = $logger;
        $this->_customerSession = isset($params['session'])
        && $params['session'] instanceof \Magento\Customer\Model\Session ? $params['session'] : $customerSession;

        $this->paymentHelper = $paymentHelper;
        $method = $this->paymentHelper->getMethodInstance(\Dilmah\Payments\Model\Ntb::PAYMENT_METHOD_NTB_CODE);

        $this->_merchantId = $method->getConfigData('merchant_id');
        $this->_ipgClientIp = $method->getConfigData('client_ip');
        $this->_ipgClientPort = $method->getConfigData('client_port');
        $this->_socketTimeout = $method->getConfigData('socket_timeout');
        $this->_ipgServerUrl = $method->getConfigData('server_url');
        $this->_returnUrl = $method->getConfigData('return_url');
        $this->_debugModeEnabled = $method->getConfigData('debug_mode');
        $this->_merchantVar1 = $method->getConfigData('merchant_var1');
        $this->_merchantVar2 = $method->getConfigData('merchant_var2');
        $this->_merchantVar3 = $method->getConfigData('merchant_var3');
        $this->_merchantVar4 = $method->getConfigData('merchant_var4');

        $this->invoiceSender = $invoiceSender;

        if (isset($params['quote']) && $params['quote'] instanceof \Magento\Quote\Model\Quote) {
            $this->_quote = $params['quote'];
        } else {
            throw new \Exception('Quote instance is required.');
        }
    }

    /**
     * Setter for customer with billing and shipping address changing ability
     *
     * @param CustomerDataObject $customerData
     * @param Address|null $billingAddress
     * @param Address|null $shippingAddress
     *
     * @return $this
     */
    public function setCustomerWithAddressChange(
        CustomerDataObject $customerData,
        $billingAddress = null,
        $shippingAddress = null
    ) {
        $this->_quote->assignCustomerWithAddressChange($customerData, $billingAddress, $shippingAddress);
        $this->_customerId = $customerData->getId();
        return $this;
    }


    /**
     * Reserve order ID for specified quote
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function start()
    {
        $this->_quote->collectTotals();

        if (!$this->_quote->getGrandTotal()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'PayPal can\'t process orders with a zero balance due. '
                    . 'To finish your purchase, please go through the standard checkout process.'
                )
            );
        }

        $this->_quote->reserveOrderId();
        $this->quoteRepository->save($this->_quote);
    }

    /**
     * Generate request xml needed for ntb.
     *
     * @param string $action
     * @return string
     */
    public function generateRequestXml($action)
    {
        $invoiceXml = '';
        switch ($action) {
            case self::SALETXN:
                $invoiceXml = "<req>" .
                    "<mer_id>" . $this->_merchantId . "</mer_id>" .
                    "<mer_txn_id>" . $this->_onepage->getQuote()->getReservedOrderId() . "</mer_txn_id>" .
                    "<action>" . $action . "</action>";

                if ($this->_returnUrl != "") {
                    $invoiceXml .= "<ret_url>" . $this->_returnUrl . "/decrypt</ret_url>";
                }

                $invoiceXml .= "</req>";
                break;

            case self::SALEMERCHUPDATED:
                break;

            case self::SALETXNVERIFY:
                break;
        }
        return $invoiceXml;
    }

    /**
     * Function createSocketConnection
     *
     * @return bool|resource
     */
    public function createSocketConnection()
    {
        if ($this->_ipgClientIp != "" && $this->_ipgClientPort != "") {
            return fsockopen(
                $this->_ipgClientIp,
                $this->_ipgClientPort,
                $this->_errNo,
                $this->_errString,
                $this->_socketTimeout
            );
        } else {
            $this->socketCreationErr = true;
            return false;
        }
    }

    /**
     * Function sendInvoiceToClient
     *
     * @param resource $ipgSocket
     * @param string $invoiceXml
     * @return bool
     */
    public function sendInvoiceToClient($ipgSocket, $invoiceXml)
    {
        socket_set_timeout($ipgSocket, $this->_socketTimeout);

        // Write the invoice to socket connection
        if (fwrite($ipgSocket, $invoiceXml) === false) {
            return false;
        }
    }

    /**
     * Function sendReceiptToIpgClient
     *
     * @param string $ipgSocket
     * @param string $invoiceXml
     * @return bool
     */
    public function sendReceiptToIpgClient($ipgSocket, $invoiceXml)
    {
        socket_set_timeout($ipgSocket, $this->_socketTimeout);

        // Write the invoice to socket connection
        if (fwrite($ipgSocket, $invoiceXml) === false) {
            $this->receiptSentError = true;
            return false;
        }
    }

    /**
     * Function getEncryptedInvoice
     *
     * @param string $ipgSocket
     * @return string
     */
    public function getEncryptedInvoice($ipgSocket)
    {
        $encryptedInvoice = '';
        while (!feof($ipgSocket)) {
            $encryptedInvoice .= fread($ipgSocket, 8192);
        }
        return $encryptedInvoice;
    }

    /**
     * close shocket connection
     *
     * @param string $ipgSocket
     * @return void
     */
    public function closeSocketConnection($ipgSocket)
    {
        fclose($ipgSocket);
    }

    /**
     * check for encryption errors
     *
     * @param string $encryptedInvoice
     * @return array
     */
    public function hasEncryptionErrors($encryptedInvoice)
    {
        $_errors = [];
        if (!(strpos($encryptedInvoice, '<error_code>') === false
            && strpos($encryptedInvoice, '</error_code>') === false
            && strpos($encryptedInvoice, '<error_msg>') === false
            && strpos($encryptedInvoice, '</error_msg>') === false)
        ) {
            $_errors['err_no'] = substr(
                $encryptedInvoice,
                (strpos($encryptedInvoice, '<error_code>') + 12),
                (strpos($encryptedInvoice, '</error_code>') - (strpos($encryptedInvoice, '<error_code>') + 12))
            );
            $_errors['err_msg'] = substr(
                $encryptedInvoice,
                (strpos($encryptedInvoice, '<error_msg>') + 11),
                (strpos($encryptedInvoice, '</error_msg>') - (strpos($encryptedInvoice, '<error_msg>') + 11))
            );
            $this->setEncryptionErr(true);
        }
        return $_errors;
    }

    /**
     * check for decryption errors
     *
     * @param string $encryptedReceipt
     * @return array
     */
    public function hasDecryptionErrors($encryptedReceipt)
    {
        $_errors = [];
        if (!(strpos($encryptedReceipt, '<error_code>') === false
            && strpos($encryptedReceipt, '</error_code>') === false
            && strpos($encryptedReceipt, '<error_msg>') === false
            && strpos($encryptedReceipt, '</error_msg>') === false)
        ) {
            $_errors['err_no'] = substr(
                $encryptedReceipt,
                (strpos($encryptedReceipt, '<error_code>') + 12),
                (strpos($encryptedReceipt, '</error_code>') - (strpos($encryptedReceipt, '<error_code>') + 12))
            );
            $_errors['err_msg'] = substr(
                $encryptedReceipt,
                (strpos($encryptedReceipt, '<error_msg>') + 11),
                (strpos($encryptedReceipt, '</error_msg>') - (strpos($encryptedReceipt, '<error_msg>') + 11))
            );
            $this->decryptionErr = true;
        }
        return $_errors;
    }

    /**
     * Validate receipt sent from ntb gateway
     *
     * @param string $encryptedReceiptPay
     *
     * @return string
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function validateReceipt($encryptedReceiptPay)
    {
        $additionalInformation = [];
        try {
            $ipgSocket = $this->createSocketConnection();
            if ($ipgSocket === false) {
                $this->logger->critical('Could not establish a socket connection for given configurations');
                throw new \Exception('Could not establish a socket connection for given configurations');
            }
            $receiptCreated = $this->sendReceiptToIpgClient($ipgSocket, $encryptedReceiptPay);
            if ($receiptCreated === false) {
                $this->logger->critical('Encrypted Receipt could not be written to socket connection');
                throw new \Exception('Encrypted Receipt could not be written to socket connection');
            }

            $decryptedReceipt = $this->getEncryptedInvoice($ipgSocket);
            if (empty($decryptedReceipt)) {
                $this->logger->critical('Receipt encryption failed');
                throw new \Exception('Receipt encryption failed');
            }

            $this->closeSocketConnection($ipgSocket);
            $_errors = $this->hasDecryptionErrors($decryptedReceipt);

            if (!empty($_errors)) {
                $this->logger->critical($_errors['err_no'] . ' - ' . $_errors['err_msg']);
                if ($this->_debugModeEnabled) {
                    $additionalInformation['error_receipt'] = $decryptedReceipt;
                    $this->_quote->getPayment()->setAdditionalInformation($additionalInformation);
                }
            } else {
                $additionalInformation = $this->getAdditionalInfoArray($decryptedReceipt);
                if (!empty($additionalInformation)) {
                    $this->_quote->getPayment()->setAdditionalInformation($additionalInformation);
                }
            }

            if (!$this->socketCreationErr && !$this->receiptSentError && !$this->decryptionErr) {
                if (!(strpos($decryptedReceipt, '<txn_status>') === false
                    && strpos($decryptedReceipt, '</txn_status>') === false)
                ) {
                    $this->transactionStatus = substr(
                        $decryptedReceipt,
                        (strpos($decryptedReceipt, '<txn_status>') + 12),
                        (strpos($decryptedReceipt, '</txn_status>') - (strpos($decryptedReceipt, '<txn_status>') + 12))
                    );
                }
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $this->transactionStatus;
    }

    /**
     * Function getTransactionStatus
     *
     * @return string
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     * Place the order when customer returned from NTB until this moment all quote data must be valid.
     *
     * @return void
     * @throws \Exception
     */
    public function place()
    {
        if ($this->getCheckoutMethod() == \Magento\Checkout\Model\Type\Onepage::METHOD_GUEST) {
            $this->prepareGuestQuote();
        }

        $this->ignoreAddressValidation();
        $this->_quote->collectTotals();
        $order = $this->quoteManagement->submit($this->_quote);

        if (!$order) {
            return;
        }

        $this->_order = $order;

        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING); // set order state to 'processing'
        $order->setStatus('paid');
        $this->orderSender->send($order); // send order email
        $invoice = $this->invoice(); // create the invoice
        if ($invoice instanceof \Magento\Sales\Model\Order\Invoice) {
            $this->invoiceSender->send($invoice); // send invoice email
        }
    }

    /**
     * Make sure addresses will be saved without validation errors
     *
     * @return void
     */
    private function ignoreAddressValidation()
    {
        $this->_quote->getBillingAddress()->setShouldIgnoreValidation(true);
        if (!$this->_quote->getIsVirtual()) {
            $this->_quote->getShippingAddress()->setShouldIgnoreValidation(true);
        }
    }

    /**
     * Return order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Get checkout method
     *
     * @return string
     */
    public function getCheckoutMethod()
    {
        if ($this->getCustomerSession()->isLoggedIn()) {
            return \Magento\Checkout\Model\Type\Onepage::METHOD_CUSTOMER;
        }
        if (!$this->_quote->getCheckoutMethod()) {
            if ($this->_checkoutData->isAllowedGuestCheckout($this->_quote)) {
                $this->_quote->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_GUEST);
            } else {
                $this->_quote->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_REGISTER);
            }
        }
        return $this->_quote->getCheckoutMethod();
    }

    /**
     * Get customer session object
     *
     * @return \Magento\Customer\Model\Session
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @return $this
     */
    protected function prepareGuestQuote()
    {
        $quote = $this->_quote;
        $quote->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID);
        return $this;
    }

    /**
     * create invoice to the current order
     *
     * @return \Magento\Sales\Model\Order\Invoice
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws bool
     */
    public function invoice()
    {
        $invoice = $this->getOrder()->prepareInvoice();
        $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
        $invoice->register();
        /** @var \Magento\Framework\DB\Transaction $transaction */
        $transaction = $this->transactionFactory->create();
        $transaction->addObject($invoice)
            ->addObject($invoice->getOrder())
            ->save();

        return $invoice;
    }

    /**
     * Prepare additional information to be saved in quote table
     *
     * @param string $decryptedReceipt
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function getAdditionalInfoArray($decryptedReceipt)
    {
        $additionalInformation = [];
        if (!(strpos($decryptedReceipt, '<acc_no>') === false
            && strpos($decryptedReceipt, '</acc_no>') === false)
        ) {
            $additionalInformation['acc_no'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<acc_no>') + 8),
                (strpos($decryptedReceipt, '</acc_no>') - (strpos($decryptedReceipt, '<acc_no>') + 8))
            );
        }

        if (!(strpos($decryptedReceipt, '<action>') === false
            && strpos($decryptedReceipt, '</action>') === false)
        ) {
            $additionalInformation['action'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<action>') + 8),
                (strpos($decryptedReceipt, '</action>') - (strpos($decryptedReceipt, '<action>') + 8))
            );
        }

        if (!(strpos($decryptedReceipt, '<bank_ref_id>') === false
            && strpos($decryptedReceipt, '</bank_ref_id>') === false)
        ) {
            $additionalInformation['bank_ref_id'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<bank_ref_id>') + 13),
                (strpos($decryptedReceipt, '</bank_ref_id>') - (strpos($decryptedReceipt, '<bank_ref_id>') + 13))
            );
        }

        if (!(strpos($decryptedReceipt, '<cur>') === false && strpos($decryptedReceipt, '</cur>') === false)) {
            $additionalInformation['currency'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<cur>') + 5),
                (strpos($decryptedReceipt, '</cur>') - (strpos($decryptedReceipt, '<cur>') + 5))
            );
        }

        if (!(strpos($decryptedReceipt, '<ipg_txn_id>') === false
            && strpos($decryptedReceipt, '</ipg_txn_id>') === false)
        ) {
            $additionalInformation['ipg_txn_id'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<ipg_txn_id>') + 12),
                (strpos($decryptedReceipt, '</ipg_txn_id>') - (strpos($decryptedReceipt, '<ipg_txn_id>') + 12))
            );

            $this->setIpgTxnId($additionalInformation['ipg_txn_id']);
        }

        if (!(strpos($decryptedReceipt, '<lang>') === false && strpos($decryptedReceipt, '</lang>') === false)) {
            $additionalInformation['lang'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<lang>') + 6),
                (strpos($decryptedReceipt, '</lang>') - (strpos($decryptedReceipt, '<lang>') + 6))
            );
        }

        if (!(strpos($decryptedReceipt, '<mer_txn_id>') === false
            && strpos($decryptedReceipt, '</mer_txn_id>') === false)
        ) {
            $additionalInformation['merchant_txn_id'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<mer_txn_id>') + 12),
                (strpos($decryptedReceipt, '</mer_txn_id>') - (strpos($decryptedReceipt, '<mer_txn_id>') + 12))
            );
        }

        if (!(strpos($decryptedReceipt, '<mer_var1>') === false
            && strpos($decryptedReceipt, '</mer_var1>') === false)
        ) {
            $additionalInformation['merchant_var1'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<mer_var1>') + 10),
                (strpos($decryptedReceipt, '</mer_var1>') - (strpos($decryptedReceipt, '<mer_var1>') + 10))
            );
        }

        if (!(strpos($decryptedReceipt, '<mer_var2>') === false
            && strpos($decryptedReceipt, '</mer_var2>') === false)
        ) {
            $additionalInformation['merchant_var2'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<mer_var2>') + 10),
                (strpos($decryptedReceipt, '</mer_var2>') - (strpos($decryptedReceipt, '<mer_var2>') + 10))
            );
        }

        if (!(strpos($decryptedReceipt, '<mer_var3>') === false
            && strpos($decryptedReceipt, '</mer_var3>') === false)
        ) {
            $additionalInformation['merchant_var3'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<mer_var3>') + 10),
                (strpos($decryptedReceipt, '</mer_var3>') - (strpos($decryptedReceipt, '<mer_var3>') + 10))
            );
        }

        if (!(strpos($decryptedReceipt, '<mer_var4>') === false
            && strpos($decryptedReceipt, '</mer_var4>') === false)
        ) {
            $additionalInformation['merchant_var4'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<mer_var4>') + 10),
                (strpos($decryptedReceipt, '</mer_var4>') - (strpos($decryptedReceipt, '<mer_var4>') + 10))
            );
        }

        if (!(strpos($decryptedReceipt, '<name>') === false && strpos($decryptedReceipt, '</name>') === false)) {
            $additionalInformation['name'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<name>') + 6),
                (strpos($decryptedReceipt, '</name>') - (strpos($decryptedReceipt, '<name>') + 6))
            );
        }

        if (!(strpos($decryptedReceipt, '<reason>') === false
            && strpos($decryptedReceipt, '</reason>') === false)
        ) {
            $additionalInformation['reason'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<reason>') + 8),
                (strpos($decryptedReceipt, '</reason>') - (strpos($decryptedReceipt, '<reason>') + 8))
            );
        }

        if (!(strpos($decryptedReceipt, '<txn_amt>') === false
            && strpos($decryptedReceipt, '</txn_amt>') === false)
        ) {
            $additionalInformation['transaction_amount'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<txn_amt>') + 9),
                (strpos($decryptedReceipt, '</txn_amt>') - (strpos($decryptedReceipt, '<txn_amt>') + 9))
            );
        }

        if (!(strpos($decryptedReceipt, '<txn_status>') === false
            && strpos($decryptedReceipt, '</txn_status>') === false)
        ) {
            $additionalInformation['transaction_status'] = substr(
                $decryptedReceipt,
                (strpos($decryptedReceipt, '<txn_status>') + 12),
                (strpos($decryptedReceipt, '</txn_status>') - (strpos($decryptedReceipt, '<txn_status>') + 12))
            );
        }
        return $additionalInformation;
    }

    /**
     * set Ipg transaction id
     *
     * @param string $val
     * @return void
     */
    public function setIpgTxnId($val)
    {
        $this->_ipgTxnId = $val;
    }

    /**
     * get ipg transaction id
     *
     * @return string
     */
    public function getIpgTxnId()
    {
        return $this->_ipgTxnId;
    }
}

<?php
namespace Netresearch\OPS\Helper;

use Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory as TransactionCollectionFactory;
use Netresearch\OPS\Model\Payment\PaymentAbstract;

/**
 * \Netresearch\OPS\Helper\Payment
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @author    André Herrn <andre.herrn@netresearch.de>
 * @license   OSL 3.0
 */
class Payment extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $shaAlgorithm = null;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    protected $oPSConfig;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $frameworkTransactionFactory;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $checkoutCart;

    /**
     * @var \Netresearch\OPS\Model\Payment\CcFactory
     */
    protected $oPSPaymentCcFactory;

    /**
     * @var TransactionCollectionFactory
     */
    protected $salesTransactionCollectionFactory;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var \Magento\Sales\Api\InvoiceManagementInterface
     */
    protected $invoiceManagement;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Netresearch\OPS\Model\Response\Handler
     */
    protected $responseHandler;

    /**
     * Payment constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Netresearch\OPS\Model\Config $oPSConfig
     * @param Data $oPSHelper
     * @param \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\DB\TransactionFactory $frameworkTransactionFactory
     * @param \Magento\Checkout\Model\Cart $checkoutCart
     * @param \Netresearch\OPS\Model\Payment\CcFactory $oPSPaymentCcFactory
     * @param TransactionCollectionFactory $salesTransactionCollectionFactory
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     * @param \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Netresearch\OPS\Model\Response\Handler $responseHandler
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Netresearch\OPS\Model\Config $oPSConfig,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\DB\TransactionFactory $frameworkTransactionFactory,
        \Magento\Checkout\Model\Cart $checkoutCart,
        \Netresearch\OPS\Model\Payment\CcFactory $oPSPaymentCcFactory,
        TransactionCollectionFactory $salesTransactionCollectionFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Netresearch\OPS\Model\Response\Handler $responseHandler
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->oPSConfig = $oPSConfig;
        $this->oPSHelper = $oPSHelper;
        $this->oPSConfigFactory = $oPSConfigFactory;
        $this->registry = $registry;
        $this->frameworkTransactionFactory = $frameworkTransactionFactory;
        $this->checkoutCart = $checkoutCart;
        $this->oPSPaymentCcFactory = $oPSPaymentCcFactory;
        $this->salesTransactionCollectionFactory = $salesTransactionCollectionFactory;
        $this->orderManagement = $orderManagement;
        $this->invoiceManagement = $invoiceManagement;
        $this->messageManager = $messageManager;
        $this->responseHandler = $responseHandler;
    }

    /**
     * Get checkout session namespace
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckout()
    {
        return $this->checkoutSession;
    }

    /**
     * @return \Netresearch\OPS\Model\Config
     */
    protected function getConfig()
    {
        return $this->oPSConfig;
    }

    /**
     * Get encrypt / decrypt algorithm
     *
     * @return string
     */
    public function getCryptMethod()
    {
        if (null === $this->shaAlgorithm) {
            $this->shaAlgorithm = $this->getConfig()->getConfigData('secret_key_type');
        }

        return $this->shaAlgorithm;
    }

    /**
     * Crypt Data by SHA1 ctypting algorithm by secret key
     *
     * @param array  $data
     *
     * @return string hash
     */
    public function shaCrypt($data)
    {
        if (is_array($data)) {
            return hash($this->getCryptMethod(), implode("", $data));
        }
        if (is_string($data)) {
            return hash($this->getCryptMethod(), $data);
        } else {
            return "";
        }
    }

    /**
     * Check hash crypted by SHA1 with existing data
     *
     * @param array  $data
     * @param string $hash
     *
     * @return bool
     */
    public function shaCryptValidation($data, $hashFromOPS)
    {
        if (is_array($data)) {
            $data = implode("", $data);
        }

        $hashUtf8    = strtoupper(hash($this->getCryptMethod(), $data));
        $hashNonUtf8 = strtoupper(hash($this->getCryptMethod(), utf8_encode($data)));

        $this->oPSHelper->log(__("Module Secureset: %1", $data));

        if ($this->compareHashes($hashFromOPS, $hashUtf8)) {
            return true;
        } else {
            $this->oPSHelper->log(__("Trying again with non-utf8 secureset"));

            return $this->compareHashes($hashFromOPS, $hashNonUtf8);
        }
    }

    private function compareHashes($hashFromOPS, $actual)
    {
        $this->oPSHelper->log(
            __(
                "Checking hashes\nHashed String by Magento: %1\nHashed String by Ingenico ePayments: %2",
                $actual,
                $hashFromOPS
            )
        );

        if ($hashFromOPS == $actual) {
            $this->oPSHelper->log("Successful validation");

            return true;
        }

        return false;
    }

    /**
     * Return set of data which is ready for SHA crypt
     *
     * @param array  $data
     * @param string $key
     *
     * @return string
     */
    public function getSHAInSet($params, $SHAkey)
    {
        $params = $this->prepareParamsAndSort($params);
        $plainHashString = "";
        foreach ($params as $paramSet) :
            if ($paramSet['value'] == '' || $paramSet['key'] == 'SHASIGN') {
                continue;
            }
            $plainHashString .= strtoupper($paramSet['key']) . "=" . $paramSet['value'] . $SHAkey;
        endforeach;

        return $plainHashString;
    }

    /**
     * Return prepared and sorted array for SHA Signature Validation
     *
     * @param array $params
     *
     * @return string
     */
    public function prepareParamsAndSort($params)
    {
        unset($params['CardNo']);
        unset($params['Brand']);
        unset($params['SHASign']);

        $params = array_change_key_case($params, CASE_UPPER);

        //PHP ksort takes care about "_", OPS not
        $sortedParams = [];
        foreach ($params as $key => $value) :
            $sortedParams[str_replace("_", "", $key)] = ['key' => $key, 'value' => $value];
        endforeach;
        ksort($sortedParams);

        return $sortedParams;
    }

    /*
     * Get SHA-1-IN hash for ops-authentification
     *
     * All Parameters have to be alphabetically, UPPERCASE
     * Empty Parameters shouldn't appear in the secure string
     *
     * @param array  $formFields
     * @param string $shaCode
     *
     * @return string
     */
    public function getSHASign($formFields, $shaCode = null, $storeId = null)
    {
        if (null === $shaCode) {
            $shaCode = $this->oPSConfigFactory->create()->getShaOutCode($storeId);
        }
        $formFields = array_change_key_case($formFields, CASE_UPPER);
        uksort($formFields, 'strnatcasecmp');
        $plainHashString = '';
        foreach ($formFields as $formKey => $formVal) {
            if (null === $formVal || '' === $formVal || $formKey == 'SHASIGN') {
                continue;
            }
            $plainHashString .= strtoupper($formKey) . '=' . $formVal . $shaCode;
        }

        return $plainHashString;
    }

    /**
     * We get some CC info from ops, so we must save it
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array                      $ccInfo
     *
     * @return \Netresearch\OPS\Helper\Payment
     */
    public function _prepareCCInfo($order, $ccInfo)
    {
        if (isset($ccInfo['CN'])) {
            $order->getPayment()->setCcOwner($ccInfo['CN']);
        }

        if (isset($ccInfo['CARDNO'])) {
            $order->getPayment()->setCcNumberEnc($ccInfo['CARDNO']);
            $order->getPayment()->setCcLast4(substr($ccInfo['CARDNO'], -4));
        }

        if (isset($ccInfo['ED'])) {
            $order->getPayment()->setCcExpMonth(substr($ccInfo['ED'], 0, 2));
            $order->getPayment()->setCcExpYear(substr($ccInfo['ED'], 2, 2));
        }

        return $this;
    }

    public function isPaymentAccepted($status)
    {
        return in_array(
            $status,
            [
                \Netresearch\OPS\Model\Status::AUTHORIZED,
                \Netresearch\OPS\Model\Status::AUTHORIZATION_WAITING,
                \Netresearch\OPS\Model\Status::AUTHORIZED_UNKNOWN,
                \Netresearch\OPS\Model\Status::WAITING_CLIENT_PAYMENT,
                \Netresearch\OPS\Model\Status::PAYMENT_REQUESTED,
                \Netresearch\OPS\Model\Status::PAYMENT_PROCESSING,
                \Netresearch\OPS\Model\Status::PAYMENT_UNCERTAIN,
                \Netresearch\OPS\Model\Status::WAITING_FOR_IDENTIFICATION
            ]
        );
    }

    public function isPaymentAuthorizeType($status)
    {
        return in_array(
            $status,
            [
                \Netresearch\OPS\Model\Status::AUTHORIZED,
                \Netresearch\OPS\Model\Status::AUTHORIZATION_WAITING,
                \Netresearch\OPS\Model\Status::AUTHORIZED_UNKNOWN,
                \Netresearch\OPS\Model\Status::WAITING_CLIENT_PAYMENT
            ]
        );
    }

    public function isPaymentCaptureType($status)
    {
        return in_array(
            $status,
            [
                \Netresearch\OPS\Model\Status::PAYMENT_REQUESTED,
                \Netresearch\OPS\Model\Status::PAYMENT_PROCESSING,
                \Netresearch\OPS\Model\Status::PAYMENT_UNCERTAIN
            ]
        );
    }

    public function isPaymentFailed($status)
    {
        return false == $this->isPaymentAccepted($status);
    }

    /**
     * apply ops state for order
     *
     * @param \Magento\Sales\Model\Order $order Order
     * @param array $params Request params
     *
     * @return mixed
     * @codingStandardsIgnoreStart
     */
    public function applyStateForOrder($order, $params)
    {
        $this->responseHandler->processResponse($params, $order->getPayment()->getMethodInstance());
        $order->getPayment()->save();

        $feedbackStatus = '';

        switch ($params['STATUS']) {
            case \Netresearch\OPS\Model\Status::WAITING_FOR_IDENTIFICATION: //3D-Secure
                $feedbackStatus = \Netresearch\OPS\Model\Status\Feedback::OPS_ORDER_FEEDBACK_STATUS_ACCEPT;
                break;
            case \Netresearch\OPS\Model\Status::AUTHORIZED:
            case \Netresearch\OPS\Model\Status::AUTHORIZED_WAITING_EXTERNAL_RESULT:
            case \Netresearch\OPS\Model\Status::AUTHORIZATION_WAITING:
            case \Netresearch\OPS\Model\Status::WAITING_CLIENT_PAYMENT:
                $feedbackStatus = \Netresearch\OPS\Model\Status\Feedback::OPS_ORDER_FEEDBACK_STATUS_ACCEPT;
                break;
            case \Netresearch\OPS\Model\Status::PAYMENT_REQUESTED:
            case \Netresearch\OPS\Model\Status::PAYMENT_PROCESSING:
            case \Netresearch\OPS\Model\Status::PAYMENT_PROCESSED_BY_MERCHANT:
                $feedbackStatus = \Netresearch\OPS\Model\Status\Feedback::OPS_ORDER_FEEDBACK_STATUS_ACCEPT;
                break;
            case \Netresearch\OPS\Model\Status::AUTHORISATION_DECLINED:
            case \Netresearch\OPS\Model\Status::PAYMENT_REFUSED:
                $feedbackStatus = \Netresearch\OPS\Model\Status\Feedback::OPS_ORDER_FEEDBACK_STATUS_DECLINE;
                break;
            case \Netresearch\OPS\Model\Status::CANCELED_BY_CUSTOMER:
                $feedbackStatus = \Netresearch\OPS\Model\Status\Feedback::OPS_ORDER_FEEDBACK_STATUS_CANCEL;
                break;
            default:
                //all unknown transaction will accept as exceptional
                $feedbackStatus = \Netresearch\OPS\Model\Status\Feedback::OPS_ORDER_FEEDBACK_STATUS_EXCEPTION;
        }

        return $feedbackStatus;
    }
    // @codingStandardsIgnoreEnd

    /**
     * Process success action by accept url
     *
     * @param \Magento\Sales\Model\Order $order  Order
     * @param array                  $params Request params
     */
    public function acceptOrder($order, $params, $instantCapture = 0)
    {
        $this->_getCheckout()->setLastSuccessQuoteId($order->getQuoteId());
        $this->_prepareCCInfo($order, $params);
        $this->setPaymentTransactionInformation($order->getPayment(), $params, 'accept');
        $this->setFraudDetectionParameters($order->getPayment(), $params);

        if ($transaction = $this->getTransactionByTransactionId($order->getQuoteId())) {
            $transaction->setTxnId($params['PAYID'])->save();
        }

        try {
            if (false === $this->forceAuthorize($order)
                && ($this->getConfig()->getConfigData('payment_action')
                    == \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE
                    || $instantCapture)
                && $params['STATUS'] != \Netresearch\OPS\Model\Status::WAITING_CLIENT_PAYMENT
            ) {
                $this->_processDirectSale($order, $params, $instantCapture);
            } else {
                $this->_processAuthorize($order, $params);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Order can not be saved.'));
            throw $e;
        }
    }

    /**
     * Set Payment Transaction Information
     *
     * @param \Magento\Sales\Model\Order\Payment $payment Sales Payment Model
     * @param array                          $params Request params
     * @param string                         $action Action (accept|cancel|decline|wait|exception)
     */
    protected function setPaymentTransactionInformation(\Magento\Sales\Model\Order\Payment $payment, $params, $action)
    {
        $payment->setTransactionId($params['PAYID']);
        $code = $payment->getMethodInstance()->getCode();

        $isInline = false;

        /* In authorize mode we still have no authorization transaction for CC and DirectDebit payments,
         * so capture or cancel won't work. So we need to create a new authorization transaction for them
         * when a payment was accepted by Ingenico ePayments
         *
         * In exception-case we create the authorization-transaction too
         * because some exception-cases can turn into accepted
         */
        if (('accept' === $action || 'exception' === $action)
            && in_array($code, ['ops_cc', 'ops_directDebit'])
        ) {
            $payment->setIsTransactionClosed(false);
            $isInline = $this->isInlinePayment($payment);
            /* create authorization transaction for non-inline pms */
            if (false === $isInline || (array_key_exists('HTML_ANSWER', $params)
                    || 0 < strlen($payment->getAdditionalInformation('HTML_ANSWER')))
            ) {
                $payment->addTransaction("authorization", null, true, __("Process outgoing transaction"));
            }
            $payment->setLastTransId($params['PAYID']);
        }

        /* Ingenico ePayments sends parameter HTML_ANSWER to trigger 3D secure redirection */
        if (isset($params['HTML_ANSWER']) && ('ops_cc' == $code)) {
            $payment->setAdditionalInformation('HTML_ANSWER', $params['HTML_ANSWER']);
            $payment->setIsTransactionPending(true);
        }

        $payment->setAdditionalInformation('paymentId', $params['PAYID']);
        $payment->setAdditionalInformation('status', $params['STATUS']);
        if (array_key_exists('ACCEPTANCE', $params) && 0 < strlen(trim($params['ACCEPTANCE']))) {
            $payment->setAdditionalInformation('acceptance', $params['ACCEPTANCE']);
        }
        if (array_key_exists('BRAND', $params) && ('ops_cc' == $code) && 0 < strlen(trim($params['BRAND']))) {
            $payment->setAdditionalInformation('CC_BRAND', $params['BRAND']);
        }
        if (false === $isInline || array_key_exists('HTML_ANSWER', $params)) {
            $payment->setIsTransactionClosed(true);
        }
        $payment->setDataChanges(true);
        $payment->save();
    }

    /**
     * add fraud detection of Ingenico ePayments to additional payment data
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @param array                              $params
     */
    protected function setFraudDetectionParameters($payment, $params)
    {
        $params = array_change_key_case($params, CASE_UPPER);
        if (array_key_exists('SCORING', $params)) {
            $payment->setAdditionalInformation('scoring', $params['SCORING']);
        }
        if (array_key_exists('SCO_CATEGORY', $params)) {
            $payment->setAdditionalInformation('scoringCategory', $params['SCO_CATEGORY']);
        }
        $additionalScoringData = [];
        foreach ($this->getConfig()->getAdditionalScoringKeys() as $key) {
            if (array_key_exists($key, $params)) {
                if (false === mb_detect_encoding($params[$key], 'UTF-8', true)) {
                    $additionalScoringData[$key] = utf8_encode($params[$key]);
                } else {
                    $additionalScoringData[$key] = $params[$key];
                }
            }
        }
        $payment->setAdditionalInformation('additionalScoringData', serialize($additionalScoringData));
    }

    /**
     * Get Payment Exception Message
     *
     * @param int $ops_status Request OPS Status
     */
    protected function getPaymentExceptionMessage($ops_status)
    {
        $exceptionMessage = '';
        switch ($ops_status) {
            case \Netresearch\OPS\Model\Status::PAYMENT_UNCERTAIN:
                $exceptionMessage = __(
                    'A technical problem arose during payment process, giving unpredictable result. ' .
                    'Ingenico ePayments status: %1.',
                    $this->oPSHelper->getStatusText($ops_status)
                );
                break;
            default:
                $exceptionMessage = __(
                    'An unknown exception was thrown in the payment process. Ingenico ePayments status: %1.',
                    $this->oPSHelper->getStatusText($ops_status)
                );
        }

        return $exceptionMessage;
    }

    /**
     * send invoice to customer if that was configured by the merchant
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice Invoice to be sent
     * @return void
     */
    public function sendInvoiceToCustomer(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        if (false == $invoice->getEmailSent() && $this->getConfig()->getSendInvoice()) {
            $this->invoiceManagement->notify($invoice->getEntityId());
        }
    }

    /**
     * Process Configured Payment Actions: Authorized, Default operation
     * just place order
     *
     * @param \Magento\Sales\Model\Order $order  Order
     * @param array                      $params Request params
     */
    protected function _processAuthorize($order, $params)
    {
        $status = $params['STATUS'];
        if ($status == \Netresearch\OPS\Model\Status::WAITING_CLIENT_PAYMENT) {
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
            $order
                ->addStatusHistoryComment(
                    __('Waiting for payment. Ingenico ePayments status: %1.', $this->oPSHelper->getStatusText($status))
                )
                ->setIsCustomerNotified(false);

            // send new order mail for bank transfer, since it is 'successfully' authorized at this point
            if ($order->getPayment()->getMethodInstance() instanceof \Netresearch\OPS\Model\Payment\BankTransfer
                && $order->getEmailSent() != 1
            ) {
                $this->orderManagement->notify($order->getEntityId());
            }
        } elseif ($status == \Netresearch\OPS\Model\Status::AUTHORIZATION_WAITING) {
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
            $order
                ->addStatusHistoryComment(
                    __(
                        'Authorization uncertain. Ingenico ePayments status: %1.',
                        $this->oPSHelper->getStatusText($status)
                    )
                )
                ->setIsCustomerNotified(false);
        } else {
            // for 3DS payments the order has to be retrieved from the payment review step
            if ($this->isInlinePayment($order->getPayment())
                && 0 < strlen(trim($order->getPayment()->getAdditionalInformation('HTML_ANSWER')))
                && $order->getPayment()->getAdditionalInformation('status') == \Netresearch\OPS\Model\Status::AUTHORIZED
            ) {
                $order->getPayment()->setIsTransactionApproved(true)
                    ->update(true)
                    ->save();
            }
            if ($this->isRedirectPaymentMethod($order) === true
                && $order->getEmailSent() != 1
            ) {
                $this->orderManagement->notify($order->getEntityId());
            }

            if (!$this->isPaypalSpecialStatus($order->getPayment()->getMethodInstance(), $status)) {
                $payId = $params['PAYID'];
                $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
                $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                $order
                    ->addStatusHistoryComment(
                        __(
                            'Processed by Ingenico ePayments. Payment ID: %1. Ingenico ePayments status: %2.',
                            $payId,
                            $this->oPSHelper->getStatusText($status)
                        )
                    )
                    ->setIsCustomerNotified(false);
            }
        }
        $order->save();
    }

    /**
     * Special status handling for Paypal and status 91
     *
     * @param $pm
     * @param $status
     *
     * @return bool
     */
    protected function isPaypalSpecialStatus($pm, $status)
    {
        return $pm instanceof \Netresearch\OPS\Model\Payment\Paypal
            && $status == \Netresearch\OPS\Model\Status::PAYMENT_PROCESSING;
    }

    /**
     * Fetches transaction with given transaction id
     *
     * @param string $txnId
     *
     * @return mixed
     */
    public function getTransactionByTransactionId($transactionId)
    {
        if (!$transactionId) {
            return;
        }
        $transaction = $this->salesTransactionCollectionFactory->create()
            ->addAttributeToFilter('txn_id', $transactionId)
            ->getLastItem();
        if (null === $transaction->getId()) {
            return false;
        }
        $transaction->getOrderPaymentObject();

        return $transaction;
    }

    /**
     * refill cart
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return void
     */
    public function refillCart($order)
    {
        // add items
        $cart = $this->checkoutCart;

        if (0 < $cart->getQuote()->getItemsCollection()->count()) {
            //cart is not empty, so refilling it is not required
            return;
        }
        foreach ($order->getItemsCollection() as $item) {
            try {
                $cart->addOrderItem($item);
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }
        $cart->save();

        // add coupon code
        $coupon = $order->getCouponCode();
        if (null !== $coupon) {
            $this->checkoutSession->getQuote()->setCouponCode($coupon)->save();
        }
    }

    /**
     * Save OPS Status to Payment
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @param array                              $params OPS-Response
     *
     * @return void
     */
    public function saveOpsStatusToPayment(\Magento\Sales\Model\Order\Payment $payment, $params)
    {
        $payment
            ->setAdditionalInformation('status', $params['STATUS'])
            ->save();
    }

    /**
     * Check is payment method is a redirect method
     *
     * @param \Magento\Sales\Model\Order $order
     */
    protected function isRedirectPaymentMethod($order)
    {
        $method = $order->getPayment()->getMethodInstance();
        if ($method
            && $method->getOrderPlaceRedirectUrl() != '' //Magento returns ''
            && $method->getOrderPlaceRedirectUrl() !== false
        ) { //Ops return false
            return true;
        } else {
            return false;
        }
    }

    public function getQuote()
    {
        return $this->_getCheckout()->getQuote();
    }

    /**
     * sets the state to pending payment if neccessary (order is in state new)
     * and adds a comment to status history
     *
     * @param $order - the order
     */
    public function handleUnknownStatus($order)
    {
        if ($order instanceof \Magento\Sales\Model\Order) {
            $message = __(
                'Unknown Ingenico ePayments state for this order. ' .
                'Please check Ingenico ePayments backend for this order.'
            );
            if ($order->getState() == \Magento\Sales\Model\Order::STATE_NEW) {
                $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
                $order->setStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
                $order->addStatusHistoryComment($message)
                    ->setIsCustomerNotified(false);
            } else {
                $order->addStatusHistoryComment($message)
                    ->setIsCustomerNotified(false);
            }
            $order->save();
        }
    }

    /**
     * returns the base grand total from either a quote or an order
     *
     * @param $salesObject
     *
     * @return double the base amount of the order
     * @throws Exception if $salesObject is not a quote or an order
     */
    public function getBaseGrandTotalFromSalesObject($salesObject)
    {
        if ($salesObject instanceof \Magento\Sales\Model\Order or $salesObject instanceof \Magento\Quote\Model\Quote) {
            return $salesObject->getBaseGrandTotal();
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('$salesObject is not a quote or an order instance')
            );
        }
    }

    /**
     * Save the last used refund operation code to payment
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @param string                         $operationCode
     *
     * @return void
     */
    public function saveOpsRefundOperationCodeToPayment(\Magento\Sales\Model\Order\Payment $payment, $operationCode)
    {
        if (in_array(
            strtoupper(trim($operationCode)),
            [
                PaymentAbstract::OPS_REFUND_FULL,
                PaymentAbstract::OPS_REFUND_PARTIAL
            ]
        )
        ) {
            $this->oPSHelper->log(
                sprintf(
                    "set last refund operation '%s' code to payment for order '%s'",
                    $operationCode,
                    $payment->getOrder()->getIncrementId()
                )
            );
            $payment
                ->setAdditionalInformation('lastRefundOperationCode', $operationCode)
                ->save();
        }
    }

    /**
     * sets the canRefund information depending on the last refund operation code
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     */
    public function setCanRefundToPayment(\Magento\Sales\Model\Order\Payment $payment)
    {
        $refundOperationCode = $payment->getAdditionalInformation('lastRefundOperationCode');
        if (in_array(
            strtoupper(trim($refundOperationCode)),
            [
                PaymentAbstract::OPS_REFUND_FULL,
                PaymentAbstract::OPS_REFUND_PARTIAL
            ]
        )
        ) {
            /*
             * a further refund is possible if the transaction remains open, that means either the merchant
             * did not close the transaction or the refunded amount is less than the orders amount
             */
            $canRefund = ($refundOperationCode == PaymentAbstract::OPS_REFUND_PARTIAL);
            $this->oPSHelper->log(
                sprintf(
                    "set canRefund to '%s' for payment of order '%s'",
                    var_export($canRefund, true),
                    $payment->getOrder()->getIncrementId()
                )
            );
            $payment
                ->setAdditionalInformation('canRefund', $canRefund)
                ->save();
        }
    }

    /**
     * determine whether the payment supports only authorize or not
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return true . if so, false otherwise
     */
    protected function forceAuthorize(\Magento\Sales\Model\Order $order)
    {
        return $order->getPayment()->getMethodInstance() instanceof \Netresearch\OPS\Model\Payment\Kwixo\KwixoAbstract;
    }

    /**
     * add ops_cc payment to checkout methods if quote total is zero and zero amount checkout is activated
     *
     * @param \Magento\Payment\Block\Form\Container $block
     *
     * @return $this
     */
    public function addCCForZeroAmountCheckout(\Magento\Payment\Block\Form\Container $block)
    {
        $methods = $block->getMethods();
        if (false === $this->checkIfCCisInCheckoutMethods($methods)) {
            $ccPayment = $this->oPSPaymentCcFactory->create();
            if ($ccPayment->getFeatureModel()->isCCAndZeroAmountAuthAllowed($ccPayment, $block->getQuote())) {
                $ccPayment->setInfoInstance($block->getQuote()->getPayment());
                $methods[] = $ccPayment;
                $block->setData('methods', $methods);
            }
        }

        return $this;
    }

    /**
     * check if ops_cc is in payment methods array
     *
     * @param $methods
     *
     * @return array
     */
    protected function checkIfCCisInCheckoutMethods($methods)
    {
        $result = false;
        foreach ($methods as $method) {
            if ($method->getCode() == 'ops_cc') {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * checks if the payment method can use order's increment id as merchant's reference
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @return bool
     */
    public function isInlinePaymentWithOrderId(\Magento\Payment\Model\InfoInterface $payment)
    {
        return $this->isInlinePayment($payment)
            && ($this->getConfig()->getInlineOrderReference() == PaymentAbstract::REFERENCE_ORDER_ID
            || $this->getConfig()->getInlineOrderReference() == null);
    }

    /**
     * checks if the payment method can pbe processed via direct link
     *
     * @param \Magento\Payment\Model\InfoInterface|\Magento\Sales\Model\Order\Payment $payment
     *
     * @return bool
     */
    public function isInlinePayment($payment)
    {
        $result = false;
        $methodInstance = $payment->getMethodInstance();
        if ($methodInstance instanceof \Netresearch\OPS\Model\Payment\DirectDebit
            || ($methodInstance instanceof \Netresearch\OPS\Model\Payment\Cc
                && ($methodInstance->hasBrandAliasInterfaceSupport($payment)
                    || $this->oPSHelper->isAdminSession()))
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * checks if the inline payment can use quote id as merchant's reference
     *
     * @param \Magento\Payment\Model\Info $payment
     *
     * @return bool
     */
    public function isInlinePaymentWithQuoteId(\Magento\Payment\Model\Info $payment)
    {
        return $this->isInlinePayment($payment)
        && (0 === strlen(
            trim($payment->getMethodInstance()->getConfigPaymentAction())
        ));
    }

    /**
     * sets the invoices of an order to paid
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return \Netresearch\OPS\Helper\Payment
     */
    public function setInvoicesToPaid($order)
    {
        /** @var $invoice \Magento\Sales\Model\Order\Invoice */
        foreach ($order->getInvoiceCollection() as $invoice) {
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            $invoice->setState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
            $invoice->save();
        }

        return $this;
    }

    /**
     * cancel all invoices for a given order
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return \Netresearch\OPS\Helper\Payment
     * @throws \Exception
     */
    public function cancelInvoices($order)
    {
        /** @var $invoice \Magento\Sales\Model\Order\Invoice */
        foreach ($order->getInvoiceCollection() as $invoice) {
            $invoice->cancel();
            $invoice->save();
        }

        return $this;
    }

    /**
     * Returns if the current payment status is an invalid one, namely if it is one of the following:
     * \Netresearch\OPS\Model\Status::INVALID_INCOMPLETE,
     * \Netresearch\OPS\Model\Status::CANCELED_BY_CUSTOMER,
     * \Netresearch\OPS\Model\Status::AUTHORISATION_DECLINED,
     *
     * @param $status
     * @return bool
     */
    public function isPaymentInvalid($status)
    {
        return in_array(
            $status,
            [
                \Netresearch\OPS\Model\Status::INVALID_INCOMPLETE,
                \Netresearch\OPS\Model\Status::CANCELED_BY_CUSTOMER,
                \Netresearch\OPS\Model\Status::AUTHORISATION_DECLINED,
            ]
        );
    }
}

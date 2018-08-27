<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Model\Payment;

use Magento\Framework\Exception\PaymentException;

/**PAYMENT_PROCESSING
 * OPS payment method model
 */
class PaymentAbstract extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * OPS template modes
     */
    const TEMPLATE_OPS_REDIRECT              = 'ops';
    const TEMPLATE_OPS_IFRAME                = 'ops_iframe';
    const TEMPLATE_OPS_TEMPLATE              = 'ops_template';
    const TEMPLATE_MAGENTO_INTERNAL          = 'magento';

    /**
     * redirect references
     */
    const REFERENCE_QUOTE_ID = 'quoteId';
    const REFERENCE_ORDER_ID = 'orderId';

    /**
     * Layout of the payment method
     */
    const PMLIST_HORIZONTAL_LEFT = 0;
    const PMLIST_HORIZONTAL = 1;
    const PMLIST_VERTICAL = 2;

    /**
     * OPS payment action constant
     */
    const OPS_AUTHORIZE_ACTION = 'RES';
    const OPS_AUTHORIZE_CAPTURE_ACTION = 'SAL';
    const OPS_CAPTURE_FULL = 'SAS';
    const OPS_CAPTURE_PARTIAL = 'SAL';
    const OPS_CAPTURE_DIRECTDEBIT_NL = 'VEN';
    const OPS_DELETE_AUTHORIZE = 'DEL';
    const OPS_DELETE_AUTHORIZE_AND_CLOSE = 'DES';
    const OPS_REFUND_FULL = 'RFS';
    const OPS_REFUND_PARTIAL = 'RFD';

    /**
     * 3D-Secure
     */
    const OPS_DIRECTLINK_WIN3DS = 'MAINW';

    /**
     * Module Transaction Type Codes
     */
    const OPS_CAPTURE_TRANSACTION_TYPE = 'capture';
    const OPS_VOID_TRANSACTION_TYPE = 'void';
    const OPS_REFUND_TRANSACTION_TYPE = 'refund';
    const OPS_DELETE_TRANSACTION_TYPE = 'delete';
    const OPS_AUTHORIZE_TRANSACTION_TYPE = 'authorize';

    /**
     * Session key for device fingerprinting consent
     */
    const FINGERPRINT_CONSENT_SESSION_KEY = 'device_fingerprinting_consent';

    protected $pm = '';
    protected $brand = '';

    protected $_code = 'ops';
    protected $_formBlockType = 'Netresearch\OPS\Block\Form';
    protected $_config = null;
    protected $requestHelper = null;
    protected $backendOperationParameterModel = null;
    protected $encoding = 'utf-8';

    /**
     * @var \Netresearch\OPS\Model\Backend\Operation\ParameterFactory
     */
    protected $oPSBackendOperationParameterFactory;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    protected $oPSConfig;

    /**
     * @var \Netresearch\OPS\Helper\Payment\Request
     */
    protected $oPSPaymentRequestHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $salesOrderFactory;

    /**
     * @var \Netresearch\OPS\Helper\Order
     */
    protected $oPSOrderHelper;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    protected $oPSPaymentHelper;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $stringUtils;

    /**
     * @var \Netresearch\OPS\Helper\Order\Capture
     */
    protected $oPSOrderCaptureHelper;

    /**
     * @var \Netresearch\OPS\Model\Api\DirectLink
     */
    protected $oPSApiDirectlink;

    /**
     * @var \Netresearch\OPS\Helper\Directlink
     */
    protected $oPSDirectlinkHelper;

    /**
     * @var \Netresearch\OPS\Model\Status\UpdateFactory
     */
    protected $oPSStatusUpdateFactory;

    /**
     * @var \Netresearch\OPS\Helper\Order\Refund
     */
    protected $oPSOrderRefundHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Magento Payment Behaviour Settings
     */
    protected $_isGateway = false;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = true;
    protected $_canUseInternal = false;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = false;
    protected $_isInitializeNeeded = true;
    protected $_canManageRecurringProfiles = false;

    /**
     * OPS behaviour settings
     */

    protected $_needsCartDataForRequest = false;

    protected $_needsShipToParams = true;

    protected $_needsBillToParams = false;

    /**
     * @var \Netresearch\OPS\Model\Response\Handler
     */
    protected $oPSResponseHandler;

    /**
     * @var \Netresearch\OPS\Model\Validator\Parameter\FactoryFactory
     */
    protected $oPSValidatorParameterFactoryFactory;

    /**
     * @var \Netresearch\OPS\Helper\Validation\Result
     */
    protected $oPSValidationResultHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $salesOrderFactory,
        \Magento\Framework\Stdlib\StringUtils $stringUtils,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Netresearch\OPS\Model\Backend\Operation\ParameterFactory $oPSBackendOperationParameterFactory,
        \Netresearch\OPS\Model\Config $oPSConfig,
        \Netresearch\OPS\Helper\Payment\Request $oPSPaymentRequestHelper,
        \Netresearch\OPS\Helper\Order $oPSOrderHelper,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Netresearch\OPS\Helper\Order\Capture $oPSOrderCaptureHelper,
        \Netresearch\OPS\Model\Api\DirectLink $oPSApiDirectlink,
        \Netresearch\OPS\Helper\Directlink $oPSDirectlinkHelper,
        \Netresearch\OPS\Model\Status\UpdateFactory $oPSStatusUpdateFactory,
        \Netresearch\OPS\Helper\Order\Refund $oPSOrderRefundHelper,
        \Netresearch\OPS\Model\Response\Handler $oPSResponseHandler,
        \Netresearch\OPS\Model\Validator\Parameter\FactoryFactory $oPSValidatorParameterFactoryFactory,
        \Netresearch\OPS\Helper\Validation\Result $oPSValidationResultHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->salesOrderFactory = $salesOrderFactory;
        $this->stringUtils = $stringUtils;
        $this->request = $request;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->oPSBackendOperationParameterFactory = $oPSBackendOperationParameterFactory;
        $this->oPSConfig = $oPSConfig;
        $this->oPSPaymentRequestHelper = $oPSPaymentRequestHelper;
        $this->oPSOrderHelper = $oPSOrderHelper;
        $this->oPSHelper = $oPSHelper;
        $this->oPSPaymentHelper = $oPSPaymentHelper;
        $this->oPSOrderCaptureHelper = $oPSOrderCaptureHelper;
        $this->oPSApiDirectlink = $oPSApiDirectlink;
        $this->oPSDirectlinkHelper = $oPSDirectlinkHelper;
        $this->oPSStatusUpdateFactory = $oPSStatusUpdateFactory;
        $this->oPSOrderRefundHelper = $oPSOrderRefundHelper;
        $this->oPSResponseHandler = $oPSResponseHandler;
        $this->oPSValidatorParameterFactoryFactory = $oPSValidatorParameterFactoryFactory;
        $this->oPSValidationResultHelper = $oPSValidationResultHelper;
    }

    /**
     * @param null $backendOperationParameterModel
     */
    public function setBackendOperationParameterModel($backendOperationParameterModel)
    {
        $this->backendOperationParameterModel = $backendOperationParameterModel;
    }

    /**
     * @return \Netresearch\OPS\Model\Backend\Operation\Parameter
     */
    public function getBackendOperationParameterModel()
    {
        if (null === $this->backendOperationParameterModel) {
            $this->backendOperationParameterModel = $this->oPSBackendOperationParameterFactory->create();
        }

        return $this->backendOperationParameterModel;
    }

    /**
     * Return OPS Config
     *
     * @return \Netresearch\OPS\Model\Config
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $this->_config = $this->oPSConfig;
        }

        return $this->_config;
    }

    public function getConfigData($field, $storeId = null)
    {
        if ($field === 'payment_action') {
            return $this->getConfigPaymentAction();
        }
        return parent::getConfigData($field, $storeId);
    }

    public function getConfigPaymentAction()
    {
        return $this->getPaymentAction();
    }

    /**
     * get the frontend gateway path based on encoding property
     */
    public function getFrontendGateWay()
    {
        $gateway = $this->getConfig()->getFrontendGatewayPath();

        return $gateway;
    }

    /**
     * return if shipment params are needed for request
     *
     * @return bool
     */
    public function getNeedsShipToParams()
    {
        return $this->_needsShipToParams;
    }

    /**
     * return if billing params are needed for request
     *
     * @return bool
     */
    public function getNeedsBillToParams()
    {
        return $this->_needsBillToParams;
    }

    /**
     * returns the request helper
     *
     * @return \Netresearch\OPS\Helper\Payment\Request
     */
    public function getRequestHelper()
    {
        if (null === $this->requestHelper) {
            $this->requestHelper = $this->oPSPaymentRequestHelper;
        }

        return $this->requestHelper;
    }

    /**
     * if payment method is available
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     *
     * @return boolean
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        $storeId = 0;
        // allow multi store/site for backend orders with disabled backend payment methods in default store
        if (null !== $quote && null !== $quote->getId()) {
            $storeId = $quote->getStoreId();
        }
        if ($this->_appState->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
            && false === $this->isEnabledForBackend($storeId)
        ) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    /**
     * if method is enabled for backend payments
     *
     * @param int $storeId
     * @return bool
     */
    public function isEnabledForBackend($storeId = null)
    {
        return $this->getConfigData('backend_enabled', $storeId);
    }

    /**
     * Redirect url to ops submit form
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return $this->getConfig()->getPaymentRedirectUrl();
    }

    protected function getPayment()
    {
        $checkout = $this->checkoutSession;
        $payment = $checkout->getQuote()->getPayment();
        if (!$payment->getId()) {
            $payment = $this->salesOrderFactory->create()
                ->loadByIncrementId($checkout->getLastRealOrderId())->getPayment();
        }
        return $payment;
    }

    public function getOpsBrand($payment = null)
    {
        if (null === $payment) {
            $payment = $this->getInfoInstance();
        }
        $brand = trim($payment->getAdditionalInformation('BRAND'));
        if (!strlen($brand)) {
            $brand = $this->brand;
        }

        return $brand;
    }

    public function getOpsCode($payment = null)
    {
        if (null === $payment) {
            $payment = $this->getInfoInstance();
        }
        $pm = trim($payment->getAdditionalInformation('PM'));
        if (!strlen($pm)) {
            $pm=$this->pm;
        }
        return $pm;
    }

    /**
     * Return payment_action value from config area
     *
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->getConfig()->getPaymentAction($this->getStoreId());
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param string[]|null $requestParams
     *
     * @return string[]
     */
    public function getMethodDependendFormFields($order, $requestParams = null)
    {
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        if (null === $shippingAddress || false === $shippingAddress) {
            $shippingAddress = $billingAddress;
        }

        $payment = $order->getPayment()->getMethodInstance();
        $quote = $this->oPSOrderHelper->getQuote($order->getQuoteId());

        $formFields = [];
        $formFields['ORIG'] = $this->oPSHelper->getModuleVersionString();
        $formFields['BRAND'] = $payment->getOpsBrand($order->getPayment());
        if ($this->getConfig()->canSubmitExtraParameter($order->getStoreId())) {
            $formFields['CN'] = $billingAddress->getFirstname().' '.$billingAddress->getLastname();
            $formFields['COM'] = $this->_getOrderDescription($order);
            $formFields['ADDMATCH'] = $this->oPSOrderHelper->checkIfAddressesAreSame($order);
            $ownerParams = $this->getRequestHelper()->getOwnerParams($billingAddress, $quote);
            $formFields['ECOM_BILLTO_POSTAL_POSTALCODE'] = $billingAddress->getPostcode();
            $formFields = array_merge($formFields, $ownerParams);
        }

        if ($this->customerSession->isLoggedIn()) {
            $formFields['CUID'] = $this->customerSession->getCustomerId();
        }

        return $formFields;
    }

    /**
     * return ship to params if needed otherwise false
     *
     * @param $shippingAddress
     *
     * @return array|bool
     */
    public function getShipToParams($shippingAddress)
    {
        $shipToParams = false;
        if ($this->getNeedsShipToParams()
            && $this->getConfig()->canSubmitExtraParameter()
            && $shippingAddress
        ) {
            $shipToParams = $this->getRequestHelper()->extractShipToParameters($shippingAddress);
        }

        return $shipToParams;
    }

    /**
     * return ship to params if needed otherwise false
     *
     * @param $billingAddress
     *
     * @return array|bool
     */
    public function getBillToParams($billingAddress)
    {
        $billToParams = false;
        if ($this->getNeedsBillToParams()
            && $this->getConfig()->canSubmitExtraParameter()
            && $billingAddress
        ) {
            $billToParams = $this->getRequestHelper()->extractBillToParameters($billingAddress);
        }

        return $billToParams;
    }

    /**
     * Prepare params array to send it to gateway page via POST
     *
     * @param \Magento\Sales\Model\Order
     * @param array
     *
     * @return array
     */
    public function getFormFields($order, $requestParams, $isRequest = true)
    {
        if (empty($order)) {
            if (!($order = $this->getOrder())) {
                return [];
            }
        }

        // get mandatory parameters
        $formFields = $this->getMandatoryFormFields($order);

        $formFields = array_merge($formFields, $this->oPSPaymentRequestHelper->getTemplateParams($order->getStoreId()));

        $formFields['ACCEPTURL'] = $this->getConfig()->getAcceptUrl();
        $formFields['DECLINEURL'] = $this->getConfig()->getDeclineUrl();
        $formFields['EXCEPTIONURL'] = $this->getConfig()->getExceptionUrl();
        $formFields['CANCELURL'] = $this->getConfig()->getCancelUrl();

        $params = $this->getBackUrlParams($order, $formFields['ORDERID']);

        $formFields['BACKURL'] = $this->getConfig()->getPaymentRetryUrl($params, $order->getStoreId());

        /** @var  $order \Magento\Sales\Model\Order */
        $shipToFormFields = $this->getShipToParams($order->getShippingAddress());
        if (is_array($shipToFormFields)) {
            $formFields = array_merge($formFields, $shipToFormFields);
        }

        $billToFormFields = $this->getBillToParams($order->getBillingAddress());
        if (is_array($billToFormFields)) {
            $formFields = array_merge($formFields, $billToFormFields);
        }

        $cartDataFormFields = $this->getOrderItemData($order);

        if (is_array($cartDataFormFields)) {
            $formFields = array_merge($formFields, $cartDataFormFields);
        }

        // get method specific parameters
        $methodDependendFields = $this->getMethodDependendFormFields($order, $requestParams);
        if (is_array($methodDependendFields)) {
            $formFields = array_merge($formFields, $methodDependendFields);
        }

        $formFields = $this->transliterateParams($formFields);

        $shaSign = $this->oPSPaymentHelper->shaCrypt(
            $this->oPSPaymentHelper->getSHASign($formFields, null, $order->getStoreId())
        );

        if ($isRequest) {
            $this->oPSHelper->log(__(
                "Register Order %1 in Ingenico ePayments \n\nAll form fields: " .
                "%2\nIngenico ePayments String to hash: %3\nHash: %4",
                $order->getIncrementId(),
                serialize($formFields),
                $this->oPSPaymentHelper->getSHASign($formFields, null, $order->getStoreId()),
                $shaSign
            ));
        }

        $formFields['SHASIGN'] = $shaSign;

        return $formFields;
    }

    /**
     * Get OPS Payment Action value
     *
     * @param string
     *
     * @return string
     */
    protected function _getOPSPaymentOperation()
    {
        $value = $this->getPaymentAction();
        if ($value == \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE) {
            $value = self::OPS_AUTHORIZE_ACTION;
        } elseif ($value == \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE) {
            $value = self::OPS_AUTHORIZE_CAPTURE_ACTION;
        }

        return $value;
    }

    protected function convertToLatin1($StringToConvert)
    {
        $returnString = '';
        $chars = str_split($StringToConvert);
        foreach ($chars as $char) {
            if (31 < ord($char) && ord($char) < 127) {
                $returnString .= $char;
            }
        }

        return $returnString;
    }

    /**
     * get formated order description
     *
     * @param \Magento\Sales\Model\Order
     *
     * @return string
     */
    public function _getOrderDescription($order)
    {

        /** @var \Magento\Sales\Model\Order\Item[] $items */
        $items = $order->getAllItems();
        $description = array_reduce(
            $items,
            function ($acc, $item) {
                if (!$item->getParentItem()) {
                    $acc .= $item->getName();
                }
                    return $acc;
            },
            ''
        );

        list($description) = $this->transliterateParams([$description]);
        $description = mb_substr($description, 0, 100);

        return $description;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     *
     * @return $this
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Get Main OPS Helper
     *
     * @return \Netresearch\OPS\Helper\Data
     */
    public function getHelper()
    {
        return $this->oPSHelper;
    }

    /**
     * Method that will be executed instead of authorize or capture
     * if flag isInitializeNeeded set to true
     *
     * @param string $paymentAction
     * @param object $stateObject
     *
     * @return \Netresearch\OPS\Model\Payment\PaymentAbstract
     */
    public function initialize($paymentAction, $stateObject)
    {
        $stateObject->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT)
            ->setStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);

        $message = __('Customer got redirected to Ingenico ePayments for %1. Awaiting feedback.', $paymentAction);

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getInfoInstance()->getOrder();

        $order->addStatusHistoryComment($message);

        return $this;
    }

    /**
     * accept payment
     *
     * @see \Magento\Sales\Model\Order\Payment::accept
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @return boolean
     * @throws PaymentException
     */
    public function acceptPayment(\Magento\Payment\Model\InfoInterface $payment)
    {
        parent::acceptPayment($payment);
        $status = $payment->getAdditionalInformation('status');

        if ($status == \Netresearch\OPS\Model\Status::AUTHORIZED
            || $status == \Netresearch\OPS\Model\Status::PAYMENT_REQUESTED
        ) {
            return true;
        }

        throw new PaymentException(
            __(
                'The order can not be accepted via Magento. ' .
                'For the actual status of the payment check the Ingenico ePayments backend.'
            )
        );
    }

    /**
     * cancel order if in payment review state
     *
     * @see \Magento\Sales\Model\Order\Payment::deny
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @return boolean
     * @throws PaymentException
     */
    public function denyPayment(\Magento\Payment\Model\InfoInterface $payment)
    {
        parent::denyPayment($payment);

        $this->messageManager->addNotice(
            __(
                'Order has been canceled permanently in Magento. ' .
                'Changes in Ingenico ePayments platform will no longer be considered.'
            )
        );

        return true;
    }

    /**
     * check if payment is in payment review state
     *
     * @return bool
     */
    public function canReviewPayment()
    {
        $status = $this->getInfoInstance()->getAdditionalInformation('status');
        return \Netresearch\OPS\Model\Status::canResendPaymentInfo($status);
    }

    /**
     * Determines if a capture will be processed
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     *
     * @throws \Exception
     * @return $this
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        // disallow Ingenico ePayments online capture if amount is zero
        if ($amount < 0.01) {
            return parent::capture($payment, $amount);
        }

        if (true === $this->_registry->registry('ops_auto_capture')) {
            $this->_registry->unregister('ops_auto_capture');

            return parent::capture($payment, $amount);
        }

        $orderId = $payment->getOrder()->getId();
        $arrInfo = $this->oPSOrderCaptureHelper->prepareOperation($payment, $amount);
        $storeId = $payment->getOrder()->getStoreId();

        if ($this->isRedirectNoticed($orderId)) {
            return $this;
        }
        try {
            $requestParams = $this->getBackendOperationParameterModel()->getParameterFor(
                self::OPS_CAPTURE_TRANSACTION_TYPE,
                $this,
                $payment,
                $amount,
                $arrInfo
            );
            $requestParams = $this->transliterateParams($requestParams);
            $response = $this->oPSApiDirectlink->performRequest(
                $requestParams,
                $this->oPSConfig->getDirectLinkGatewayPath($storeId),
                $storeId
            );

            $this->oPSResponseHandler->processResponse($response, $this, false);

            return $this;
        } catch (\Exception $e) {
            $this->oPSStatusUpdateFactory->create()->updateStatusFor($payment->getOrder());
            $this->oPSHelper->log("Exception in capture request:".$e->getMessage());
            throw $e;
        }
    }

    /**
     * Refund
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float                                $amount
     *
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\PaymentException
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if ($this->oPSOrderRefundHelper->getOpenRefundTransaction($payment)->getId()) {
            throw new PaymentException(
                __(
                    "There is already one creditmemo in the queue. " .
                    "The Creditmemo will be created automatically as soon as " .
                    "Ingenico ePayments sends an acknowledgement."
                )
            );
        }

        $refundHelper = $this->oPSOrderRefundHelper->setAmount($amount)->setPayment($payment);
        $arrInfo = $refundHelper->prepareOperation($payment, $amount);
        $storeId = $payment->getOrder()->getStoreId();

        try {
            $requestParams  = $this->getBackendOperationParameterModel()->getParameterFor(
                self::OPS_REFUND_TRANSACTION_TYPE,
                $this,
                $payment,
                $amount,
                $arrInfo
            );
            $requestParams = $this->transliterateParams($requestParams);
            $response = $this->oPSApiDirectlink->performRequest(
                $requestParams,
                $this->oPSConfig->getDirectLinkGatewayPath($storeId),
                $storeId
            );
            $this->oPSResponseHandler->processResponse($response, $this, false);
        } catch (\Exception $e) {
            $this->oPSHelper->log($e->getMessage());
            $this->oPSStatusUpdateFactory->create()->updateStatusFor($payment->getOrder());
            throw $e;
        }

        return $this;
    }

    /**
     * Returns the mandatory fields for requests to Ingenico ePayments
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return array
     */
    public function getMandatoryFormFields($order)
    {
        $formFields = $this->oPSPaymentRequestHelper->getMandatoryRequestFields($order);
        $paymentAction = $this->_getOPSPaymentOperation();
        if ($paymentAction) {
            $formFields['OPERATION'] = $paymentAction;
        }

        return $formFields;
    }

    /**
     * determines if the close transaction parameter is set in the credit memo data
     *
     * @param array $creditMemoData
     *
     * @return bool
     */
    protected function getCloseTransactionFromCreditMemoData($creditMemoData)
    {
        $closeTransaction = false;
        if (array_key_exists('ops_close_transaction', $creditMemoData)
            && strtolower(trim($creditMemoData['ops_close_transaction'])) == 'on'
        ) {
            $closeTransaction = true;
        }
        return $closeTransaction;
    }

    /**
     * Custom cancel behavior, deny cancel and force custom to use void instead
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @return void
     * @throws \Exception
     */
    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        /*
         * Important: If an order was voided successfully and the user clicks on cancel in order-view
         * this method is not triggered anymore
         */

        //Proceed parent cancel method in case that regirstry value ops_auto_void is set
        if (true === $this->_registry->registry('ops_auto_void')) :
            $this->_registry->unregister('ops_auto_void');

            parent::cancel($payment);
        endif;

        //If order has state 'pending_payment' and the payment has Ingenico ePayments-status 0 or null (unknown)
        // then cancel the order
        if (true === $this->canCancelManually($payment->getOrder())) {
            $payment->getOrder()->addStatusHistoryComment(
                __("The order was cancelled manually. The Ingenico ePayments-state is 0 or null.")
            );
            parent::cancel($payment);
        }

        //Abort cancel method by throwing a PaymentException
        throw new PaymentException(__('Please use void to cancel the operation.'));
    }

    /**
     * Custom void behavior, trigger Ingenico ePayments cancel request
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @return $this
     * @throws \Exception
     */
    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        $status = $payment->getAdditionalInformation('status');

        if (!\Netresearch\OPS\Model\Status::canVoidTransaction($status)) {
            throw new PaymentException(__('Status %1 can not be voided.', $status));
        }

        //Set initital params
        $orderID = $payment->getOrder()->getId();
        $order = $this->salesOrderFactory->create()->load($orderID);

        //Calculate amount which has to be captured
        $alreadyCaptured = $payment->getBaseAmountPaidOnline();

        $grandTotal = $this->oPSPaymentHelper
            ->getBaseGrandTotalFromSalesObject($order);
        $voidAmount = $grandTotal - $alreadyCaptured;
        $storeId = $order->getStoreId();
        //Build void directLink-Request-Params
        $requestParams = [
            'AMOUNT'    => $this->getHelper()->getAmount($voidAmount),
            'PAYID'     => $payment->getAdditionalInformation('paymentId'),
            'OPERATION' => self::OPS_DELETE_AUTHORIZE,
            'CURRENCY'  => $this->storeManager->getStore($storeId)->getBaseCurrencyCode()
        ];

        //Check if there is already a waiting void transaction, if yes: redirect to order view
        if ($this->oPSDirectlinkHelper->checkExistingTransact(
            self::OPS_VOID_TRANSACTION_TYPE,
            $orderID
        )
        ) {
            $this->messageManager->addError(
                __('You already sent a void request. Please wait until the void request will be acknowledged.')
            );
            return $this;
        }

        try {
            //perform ops cancel request
            $response = $this->oPSApiDirectlink
                ->performRequest(
                    $requestParams,
                    $this->oPSConfig->getDirectLinkGatewayPath($storeId),
                    $order->getStoreId()
                );

            if ($response['STATUS'] == \Netresearch\OPS\Model\Status::INVALID_INCOMPLETE
                && $payment->getAdditionalInformation('status') == \Netresearch\OPS\Model\Status::PAYMENT_REQUESTED
            ) {
                throw new PaymentException(
                    __('Order can no longer be voided. You have to refund the order online.')
                );
            }

            $this->oPSResponseHandler->processResponse($response, $this, false);

            return $this;
        } catch (\Exception $e) {
            $this->oPSStatusUpdateFactory->create()->updateStatusFor($payment->getOrder());
            $this->oPSHelper->log("Exception in void request:" . $e->getMessage());
            throw $e;
        }
    }

    /**
     * get question for fields with disputable value
     * users are asked to correct the values before redirect to Ingenico ePayments
     *
     * @param \Magento\Sales\Model\Order $order Current order
     * @param array $requestParams Request parameters
     *
     * @return string
     */
    public function getQuestion($order, $requestParams)
    {
        return null;
    }

    /**
     * get an array of fields with disputable value
     * users are asked to correct the values before redirect to Ingenico ePayments
     *
     * @param \Magento\Sales\Model\Order $order         Current order
     * @param array                  $requestParams Request parameters
     *
     * @return array
     */
    public function getQuestionedFormFields($quote, $requestParams)
    {
        return [];
    }

    /**
     * if we need some missing form params
     * users are asked to correct the values before redirect to Ingenico ePayments
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array                  $requestParams Parameters sent in current request
     * @param array                  $formFields    Parameters to be sent to Ingenico ePayments
     *
     * @return bool
     */
    public function hasFormMissingParams($order, $requestParams, $formFields = null)
    {
        if (false == is_array($requestParams)) {
            $requestParams = [];
        }
        if (null === $formFields) {
            $formFields = $this->getFormFields($order, $requestParams, false);
        }
        $availableParams = array_merge($requestParams, $formFields);
        $requiredParams = $this->getQuestionedFormFields($order, $requestParams);
        foreach ($requiredParams as $requiredParam) {
            if (false == array_key_exists($requiredParam, $availableParams)
                || 0 == strlen($availableParams[$requiredParam])
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if order can be cancelled manually
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool
     */
    public function canCancelManually($order)
    {
        $payment = $order->getPayment();

        //If payment has Ingenico ePayments-status 0 or null (unknown) or another offline cancelable status
        $status = $payment->getAdditionalInformation('status');

        return null === $status
        || in_array(
            $status,
            [
                \Netresearch\OPS\Model\Status::INVALID_INCOMPLETE,
                \Netresearch\OPS\Model\Status::CANCELED_BY_CUSTOMER,
                \Netresearch\OPS\Model\Status::AUTHORISATION_DECLINED,
                \Netresearch\OPS\Model\Status::PAYMENT_DELETED
            ]
        );
    }

    public function getOpsHtmlAnswer($payment = null)
    {
        $returnValue = '';
        if (null === $payment) {
            $quoteId = $this->checkoutSession->getQuote()->getId();
            if (null === $quoteId) {
                $orderIncrementId = $this->checkoutSession->getLastRealOrderId();
                $order = $this->salesOrderFactory->create()->loadByAttribute('increment_id', $orderIncrementId);
            } else {
                $order = $this->salesOrderFactory->create()->loadByAttribute('quote_id', $quoteId);
            }
            if ($order instanceof \Magento\Sales\Model\Order && 0 < $order->getId()) {
                $payment = $order->getPayment();
                $returnValue = $payment->getAdditionalInformation('HTML_ANSWER');
            }
        } elseif ($payment instanceof \Magento\Payment\Model\Info) {
            $returnValue = $payment->getAdditionalInformation('HTML_ANSWER');
        }

        return $returnValue;
    }

    public function getShippingTaxRate($order)
    {
        return $this->getRequestHelper()->getShippingTaxRate($order);
    }

    protected function isRedirectNoticed($orderId)
    {
        if ($this->oPSDirectlinkHelper->checkExistingTransact(self::OPS_CAPTURE_TRANSACTION_TYPE, $orderId)) {
            $this->messageManager->addNotice(
                __('You already sent a capture request. Please wait until the capture request is acknowledged.')
            );
            return true;
        }
        if ($this->oPSDirectlinkHelper->checkExistingTransact(self::OPS_VOID_TRANSACTION_TYPE, $orderId)) {
            $this->messageManager->addNotice(
                __('There is one void request waiting. Please wait until this request is acknowledged.')
            );
            return true;
        }

        return false;
    }

    /**
     * @param \Netresearch\OPS\Model\Config $config
     */
    public function setConfig(\Netresearch\OPS\Model\Config $config)
    {
        $this->_config = $config;
    }

    /**
     * If cart Item information has to be transmitted to Ingenico ePayments
     *
     * @return bool
     */

    public function needsOrderItemDataForRequest()
    {
        return $this->_needsCartDataForRequest;
    }

    /**
     * Returns array with the order item data formatted in Ingenico ePayments fashion if payment method implementation
     * needs the data otherwise just returns false.
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return array|false
     */
    public function getOrderItemData(\Magento\Sales\Model\Order $order)
    {
        if (!$this->needsOrderItemDataForRequest()) {
            return false;
        }

        return $this->oPSPaymentRequestHelper->extractOrderItemParameters($order);
    }

    /**
     * {@inheritDoc}
     */
    public function canVoid()
    {
        $status = $this->getInfoInstance()->getAdditionalInformation('status');
        if (\Netresearch\OPS\Model\Status::canVoidTransaction($status)) {
            return parent::canVoid();
        } else {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);

        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo->getData($this->getCode())) {
            foreach ($paymentInfo->getData($this->getCode()) as $key => $value) {
                $paymentInfo->setAdditionalInformation($key, $value);
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function validate()
    {
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof \Magento\Sales\Model\Order\Payment) {
            $billingAddress = $paymentInfo->getOrder()->getBillingAddress();
            $shippingAddress = $paymentInfo->getOrder()->getShippingAddress();
            $salesObject = $paymentInfo->getOrder();
        } else {
            $salesObject = $paymentInfo->getQuote();
            $billingAddress = $salesObject->getBillingAddress();
            $shippingAddress = $salesObject->getShippingAddress();
        }

        $validator = $this->oPSValidatorParameterFactoryFactory->create()->getValidatorFor(
            \Netresearch\OPS\Model\Validator\Parameter\Factory::TYPE_REQUEST_PARAMS_VALIDATION
        );

        $params = $this->getRequestHelper()->getOwnerParams($billingAddress, $salesObject);
        $billingParams = $this->getBillToParams($billingAddress);
        $shippingParams = $this->getShipToParams($shippingAddress);
        if ($shippingParams) {
            $params = array_merge($params, $shippingParams);
        }
        if ($billingParams) {
            $params = array_merge($params, $billingParams);
        }

        if (false === $validator->isValid($params)) {
            $result = $this->oPSValidationResultHelper->getValidationFailedResult(
                $validator->getMessages(),
                $salesObject
            );
            throw new \Magento\Framework\Exception\PaymentException(
                __('Validation failed %1', join(', ', $result['fields']))
            );
        }

        return parent::validate();
    }

    /**
     * Transliterates params if necessary by configuration
     *
     * @param string[] $formFields formfields to transliterate
     *
     * @return string[]
     */
    private function transliterateParams($formFields)
    {
        if (strtoupper($this->getEncoding()) != 'UTF-8') {
            $cTypeLocale = setlocale(LC_CTYPE, 0);
            $storeLocale = $this->_scopeConfig->getValue('general/locale/code');
            setlocale(LC_CTYPE, $storeLocale, $storeLocale . '.utf8');

            array_walk(
                $formFields,
                function (&$value) {
                    $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
                }
            );

            setlocale(LC_CTYPE, $cTypeLocale);
        }

        return $formFields;
    }

    /**
     * @param $order
     * @param $orderId
     *
     * @return array
     */
    protected function getBackUrlParams($order, $orderId)
    {
        $params = [
            'orderID' => $orderId
        ];
        $secret = $this->getConfig()->getShaInCode($order->getStoreId());
        $raw = $this->oPSPaymentHelper->getSHAInSet($params, $secret);
        $params['SHASIGN'] = strtoupper($this->oPSPaymentHelper->shaCrypt($raw));

        return $params;
    }
}

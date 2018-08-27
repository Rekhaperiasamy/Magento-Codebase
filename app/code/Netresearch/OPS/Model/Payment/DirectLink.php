<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Model\Payment;

abstract class DirectLink extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** Check if we can capture directly from the backend */
    protected $_canUseInternal = true;

    protected $config = null;

    protected $directLinkHelper = null;

    protected $paymentHelper = null;

    protected $quoteHelper = null;

    protected $requestParamsHelper = null;

    protected $validationFactory = null;

    protected $dataHelper = null;

    protected $_isInitializeNeeded = false;

    /**
     * @var \Netresearch\OPS\Helper\Alias
     */
    protected $oPSAliasHelper;

    /**
     * @var \Netresearch\OPS\Helper\Quote
     */
    protected $oPSQuoteHelper;

    /**
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    protected $checkoutTypeOnepage;

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
        \Magento\Checkout\Model\Type\Onepage $checkoutTypeOnepage,
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
        \Netresearch\OPS\Helper\Quote $oPSQuoteHelper,
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
            $storeManager,
            $checkoutSession,
            $salesOrderFactory,
            $stringUtils,
            $request,
            $customerSession,
            $messageManager,
            $oPSBackendOperationParameterFactory,
            $oPSConfig,
            $oPSPaymentRequestHelper,
            $oPSOrderHelper,
            $oPSHelper,
            $oPSPaymentHelper,
            $oPSOrderCaptureHelper,
            $oPSApiDirectlink,
            $oPSDirectlinkHelper,
            $oPSStatusUpdateFactory,
            $oPSOrderRefundHelper,
            $oPSResponseHandler,
            $oPSValidatorParameterFactoryFactory,
            $oPSValidationResultHelper,
            $resource,
            $resourceCollection,
            $data
        );
        $this->checkoutTypeOnepage = $checkoutTypeOnepage;
        $this->oPSQuoteHelper = $oPSQuoteHelper;
    }

    /**
     * @param \Netresearch\OPS\Helper\Payment\DirectLink\RequestInterface $requestParamsHelper
     */
    public function setRequestParamsHelper($requestParamsHelper)
    {
        $this->requestParamsHelper = $requestParamsHelper;
    }

    /**
     * sets the quote helper
     *
     * @param \Netresearch\OPS\Helper\Quote $quoteHelper
     */
    public function setQuoteHelper(\Netresearch\OPS\Helper\Quote $quoteHelper)
    {
        $this->quoteHelper = $quoteHelper;
    }

    /**
     * gets the quote helper
     *
     * @return \Netresearch\OPS\Helper\Quote
     */
    public function getQuoteHelper()
    {
        if (null === $this->quoteHelper) {
            $this->quoteHelper = $this->oPSQuoteHelper;
        }

        return $this->quoteHelper;
    }

    /**
     * @param \Netresearch\OPS\Helper\Directlink $directLinkHelper
     */
    public function setDirectLinkHelper($directLinkHelper)
    {
        $this->directLinkHelper = $directLinkHelper;
    }

    /**
     * @return \Netresearch\OPS\Helper\Directlink
     */
    public function getDirectLinkHelper()
    {
        if (null === $this->directLinkHelper) {
            $this->directLinkHelper = $this->oPSDirectlinkHelper;
        }

        return $this->directLinkHelper;
    }

    /**
     * @param \Netresearch\OPS\Helper\Payment $paymentHelper
     */
    public function setPaymentHelper($paymentHelper)
    {
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @return \Netresearch\OPS\Helper\Payment
     */
    public function getPaymentHelper()
    {
        if (null === $this->paymentHelper) {
            $this->paymentHelper = $this->oPSPaymentHelper;
        }

        return $this->paymentHelper;
    }

    /**
     * @param \Netresearch\OPS\Model\Config $config
     */
    public function setConfig(\Netresearch\OPS\Model\Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return \Netresearch\OPS\Model\Config
     */
    public function getConfig()
    {
        if (null === $this->config) {
            $this->config = $this->oPSConfig;
        }

        return $this->config;
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     *
     * @return $this
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if ($this->isInlinePayment($payment)
            && \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE == $this->getConfigPaymentAction()
        ) {
            $order = $payment->getOrder();
            $quote = $this->getQuoteHelper()->getQuote();
            $this->confirmPayment($order, $quote, $payment);
        }
        return $this;
    }

    /**
     * Saves the payment model and runs the request to Ingenico ePayments webservice
     *
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @throws \Exception
     */
    protected function confirmPayment(
        \Magento\Sales\Model\Order $order,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Framework\DataObject $payment
    ) {
        $this->handleAdminPayment($quote);
        $requestParams = $this->getRequestParamsHelper()->getDirectLinkRequestParams($quote, $order, $payment);
        $this->invokeRequestParamValidation($requestParams);
        $this->performPreDirectLinkCallActions($quote, $payment);
        $response = $this->getDirectLinkHelper()->performDirectLinkRequest(
            $quote,
            $requestParams,
            $quote->getStoreId()
        );
        if ($response) {
            $this->oPSResponseHandler->processResponse($response, $this, false);
            $this->performPostDirectLinkCallAction($quote, $order);
        } else {
            $this->getPaymentHelper()->handleUnknownStatus($order);
        }
    }

    /**
     * Handles backend payments on Magento side
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return \Netresearch\OPS\Model\Payment\DirectLink
     */
    abstract protected function handleAdminPayment(\Magento\Quote\Model\Quote $quote);

    /**
     * @return \Netresearch\OPS\Helper\Payment\DirectLink\RequestInterface
     */
    abstract protected function getRequestParamsHelper();

    /**
     * Perform necessary preparation before request to Ingenico ePayments is sent
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Framework\DataObject $payment
     * @param array $requestParams
     *
     * @return \Netresearch\OPS\Model\Payment\DirectLink
     */
    abstract protected function performPreDirectLinkCallActions(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Framework\DataObject $payment,
        $requestParams = []
    );

    /**
     * Perform necessary work after the Directlink Request was sent and an response was received and processed
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Sales\Model\Order $order
     *
     * @return \Netresearch\OPS\Model\Payment\DirectLink
     */
    abstract protected function performPostDirectLinkCallAction(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Sales\Model\Order $order
    );

    /**
     * performs direct link request either for inline paymentsand direct sale mode
     * or the normal maintenance call (invoice)
     *
     * {@override}
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     *
     * @return \Netresearch\OPS\Model\Payment\DirectLink
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        /**
         * process direct sale inline payments (initial request)
         */
        if (\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE == $this->getConfigPaymentAction()
            && $this->getPaymentHelper()->isInlinePayment($payment)
        ) {
            $order = $payment->getOrder();
            $quote = $this->getQuoteHelper()->getQuote();
            $this->confirmPayment($order, $quote, $payment);
        } /**
         * invoice request authorize mode if the payment was placed on Ingenico ePayments side
         */
        elseif (0 < strlen(trim($payment->getAdditionalInformation('paymentId')))) {
            parent::capture($payment, $amount);
        }
    }

    /**
     * checks if the selected payment supports inline mode
     *
     * @param $payment - the payment to check
     *
     * @return bool - true if it's support inline mode, false otherwise
     */
    protected function isInlinePayment($payment)
    {
        $result = false;

        $methodInstance = $payment->getMethodInstance();
        if ((
                $methodInstance instanceof \Netresearch\OPS\Model\Payment\Cc
                && $methodInstance->hasBrandAliasInterfaceSupport($payment)
                || $this->getDataHelper()->isAdminSession()
            )
            || $methodInstance instanceof \Netresearch\OPS\Model\Payment\DirectDebit
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * Get one page checkout model
     *
     * @return \Magento\Checkout\Model\Type\Onepage
     */
    public function getOnepage()
    {
        return $this->checkoutTypeOnepage;
    }

    /**
     * Validate checkout request parameters
     *
     * @param $requestParams
     *
     * @throws \Exception
     * @return \Netresearch\OPS\Model\Payment\DirectLink
     */
    protected function invokeRequestParamValidation($requestParams)
    {
        $validator = $this->getValidationFactory()->getValidatorFor(
            \Netresearch\OPS\Model\Validator\Parameter\Factory::TYPE_REQUEST_PARAMS_VALIDATION
        );
        if (false == $validator->isValid($requestParams)) {
            $this->getOnepage()->getCheckout()->setGotoSection('payment');
            throw new \Magento\Framework\Exception\PaymentException(
                __('The data you have provided can not be processed by Ingenico ePayments')
            );
        }

        return $this;
    }

    /**
     * @return \Netresearch\OPS\Model\Validator\Parameter\Factory
     */
    public function getValidationFactory()
    {
        if (null === $this->validationFactory) {
            $this->validationFactory = $this->oPSValidatorParameterFactoryFactory->create();
        }

        return $this->validationFactory;
    }

    /**
     * sets the used validation factory
     *
     * @param \Netresearch\OPS\Model\Validator\Parameter\Factory $validationFactory
     */
    public function setValidationFactory(\Netresearch\OPS\Model\Validator\Parameter\Factory $validationFactory)
    {
        $this->validationFactory = $validationFactory;
    }

    /**
     * @param \Netresearch\OPS\Helper\Data $dataHelper
     */
    public function setDataHelper(\Netresearch\OPS\Helper\Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return \Netresearch\OPS\Helper\Data
     */
    public function getDataHelper()
    {
        if (null === $this->dataHelper) {
            $this->dataHelper = $this->oPSHelper;
        }
        return $this->dataHelper;
    }
}

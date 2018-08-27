<?php
namespace Netresearch\OPS\Model\Payment;

use Magento\Payment\Model\Method\ConfigInterface;
use Magento\Payment\Model\Method\TransparentInterface;

/**
 * \Netresearch\OPS\Model\Payment\Cc
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
class Cc extends \Netresearch\OPS\Model\Payment\DirectLink implements TransparentInterface, ConfigInterface
{
    const CODE = 'ops_cc';

    /** info source path */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Cc';

    /** @var string $_formBlockType define a specific form block */
    protected $_formBlockType = 'Netresearch\OPS\Block\Form\Cc';

    /** payment code */
    protected $_code = self::CODE;

    protected $featureModel = null;

    /**
     * @var \Netresearch\OPS\Helper\Creditcard
     */
    protected $oPSCreditcardHelper;

    /**
     * @var \Netresearch\OPS\Model\Payment\Features\ZeroAmountAuthFactory
     */
    protected $oPSPaymentFeaturesZeroAmountAuthFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $salesOrderFactory
     * @param \Magento\Framework\Stdlib\StringUtils $stringUtils
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Checkout\Model\Type\Onepage $checkoutTypeOnepage
     * @param \Netresearch\OPS\Model\Backend\Operation\ParameterFactory $oPSBackendOperationParameterFactory
     * @param \Netresearch\OPS\Model\Config $oPSConfig
     * @param \Netresearch\OPS\Helper\Payment\Request $oPSPaymentRequestHelper
     * @param \Netresearch\OPS\Helper\Order $oPSOrderHelper
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     * @param \Netresearch\OPS\Helper\Payment $oPSPaymentHelper
     * @param \Netresearch\OPS\Helper\Order\Capture $oPSOrderCaptureHelper
     * @param \Netresearch\OPS\Model\Api\DirectLink $oPSApiDirectlink
     * @param \Netresearch\OPS\Helper\Directlink $oPSDirectlinkHelper
     * @param \Netresearch\OPS\Model\Status\UpdateFactory $oPSStatusUpdateFactory
     * @param \Netresearch\OPS\Helper\Order\Refund $oPSOrderRefundHelper
     * @param \Netresearch\OPS\Model\Response\Handler $oPSResponseHandler
     * @param \Netresearch\OPS\Model\Validator\Parameter\FactoryFactory $oPSValidatorParameterFactoryFactory
     * @param \Netresearch\OPS\Helper\Validation\Result $oPSValidationResultHelper
     * @param \Netresearch\OPS\Helper\Quote $oPSQuoteHelper
     * @param \Netresearch\OPS\Helper\Alias $oPSAliasHelper
     * @param \Netresearch\OPS\Helper\Creditcard $oPSCreditcardHelper
     * @param Features\ZeroAmountAuthFactory $oPSPaymentFeaturesZeroAmountAuthFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
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
        \Netresearch\OPS\Helper\Alias $oPSAliasHelper,
        \Netresearch\OPS\Helper\Creditcard $oPSCreditcardHelper,
        \Netresearch\OPS\Model\Payment\Features\ZeroAmountAuthFactory $oPSPaymentFeaturesZeroAmountAuthFactory,
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
            $checkoutTypeOnepage,
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
            $oPSQuoteHelper,
            $resource,
            $resourceCollection,
            $data
        );
        $this->oPSAliasHelper = $oPSAliasHelper;
        $this->oPSCreditcardHelper = $oPSCreditcardHelper;
        $this->oPSPaymentFeaturesZeroAmountAuthFactory = $oPSPaymentFeaturesZeroAmountAuthFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);
        $additionalData = $data->getAdditionalData();
        $ccBrand = isset($additionalData['CC_BRAND']) ? $additionalData['CC_BRAND'] : null;
        $alias = isset($additionalData['alias']) ? $additionalData['alias'] : null;
        $cvc = isset($additionalData['cvc']) ? $additionalData['cvc'] : null;
        $infoInstance = $this->getInfoInstance();
        if ($ccBrand) {
            $infoInstance->setAdditionalInformation('CC_BRAND', $ccBrand);
        }
        if ($alias) {
            $infoInstance->setAdditionalInformation('alias', $alias);
        }
        if ($cvc) {
            $infoInstance->setAdditionalInformation('cvc', $cvc);
        }
        return $this;
    }

    /** ops payment code */
    public function getOpsCode($payment = null)
    {
        $opsBrand = $this->getOpsBrand($payment);
        if ('PostFinance card' == $opsBrand) {
            return 'PostFinance Card';
        }
        if ('UNEUROCOM' == $this->getOpsBrand($payment)) {
            return 'UNEUROCOM';
        }

        return 'CreditCard';
    }

    /**
     * @param null $payment
     *
     * @return array|mixed|null
     */
    public function getOpsBrand($payment = null)
    {
        if (null === $payment) {
            $payment = $this->checkoutSession->getQuote()->getPayment();
        }

        return $payment->getAdditionalInformation('CC_BRAND');
    }

    public function getOrderPlaceRedirectUrl($payment = null)
    {
        if ($this->hasBrandAliasInterfaceSupport($payment)) {
            if ('' == $this->getOpsHtmlAnswer($payment)) {
                return false;
            } // Prevent redirect on cc payment
            else {
                return $this->oPSConfig->get3dSecureRedirectUrl();
            }
        }

        return parent::getOrderPlaceRedirectUrl();
    }

    /**
     * only some brands are supported to be integrated into onepage checkout
     *
     * @return array
     */
    public function getBrandsForAliasInterface()
    {
        return $this->oPSConfig->getInlinePaymentCcTypes($this->getCode());
    }

    /**
     * if cc brand supports ops alias interface
     *
     * @param $payment
     *
     * @return bool
     */
    public function hasBrandAliasInterfaceSupport($payment = null)
    {
        return in_array(
            $this->getOpsBrand($payment),
            $this->getBrandsForAliasInterface()
        );
    }

    /**
     * Validates alias for in quote provided addresses
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Framework\DataObject $payment
     *
     * @throws \Exception
     */
    protected function validateAlias($quote, $payment)
    {
        $alias = $payment->getAdditionalInformation('alias');
        if (0 < strlen(trim($alias))
            && is_numeric($payment->getAdditionalInformation('cvc'))
            && false === $this->oPSAliasHelper->isAliasValidForAddresses(
                $quote->getCustomerId(),
                $alias,
                $quote->getBillingAddress(),
                $quote->getShippingAddress(),
                $quote->getStoreId()
            )
        ) {
            $this->getOnepage()->getCheckout()->setGotoSection('payment');
            throw new \Magento\Framework\Exception\PaymentException(
                __('Invalid payment information provided!')
            );
        }
    }

    /**
     * @return \Netresearch\OPS\Helper\Creditcard
     */
    public function getRequestParamsHelper()
    {
        if (null === $this->requestParamsHelper) {
            $this->requestParamsHelper = $this->oPSCreditcardHelper;
        }

        return $this->requestParamsHelper;
    }

    protected function performPreDirectLinkCallActions(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Framework\DataObject $payment,
        $requestParams = []
    ) {
        $this->oPSAliasHelper->cleanUpAdditionalInformation($payment, true);
        if (true === $this->oPSConfig->isAliasManagerEnabled($this->getCode())) {
            $this->validateAlias($quote, $payment);
        }

        return $this;
    }

    protected function performPostDirectLinkCallAction(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Sales\Model\Order $order
    ) {
        $this->oPSAliasHelper->setAliasActive($quote, $order);
        return $this;
    }

    protected function handleAdminPayment(\Magento\Quote\Model\Quote $quote)
    {
        return $this;
    }

    /**
     * returns allow zero amount authorization
     * only TRUE if configured payment action for the store is authorize
     *
     * @param mixed null|int $storeId
     *
     * @return bool
     */
    public function isZeroAmountAuthorizationAllowed($storeId = null)
    {
        $authorizePaymentAction = \Netresearch\OPS\Model\Payment\PaymentAbstract::ACTION_AUTHORIZE;
        $zeroAmountCheckout = $this->_scopeConfig
            ->getValue(
                'payment/ops_cc/zero_amount_checkout',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        return $this->getConfig()->getPaymentAction($storeId) == $authorizePaymentAction && $zeroAmountCheckout;
    }

    /**
     * @return \Netresearch\OPS\Model\Payment\Features\ZeroAmountAuth
     */
    public function getFeatureModel()
    {
        if (null === $this->featureModel) {
            $this->featureModel = $this->oPSPaymentFeaturesZeroAmountAuthFactory->create();
        }

        return $this->featureModel;
    }

    /**
     * setter for canCapture from outside, needed for zero amount order since we need to disable online capture
     * but still need to be able to create a invoice
     *
     * @param $canCapture
     */
    public function setCanCapture($canCapture)
    {
        if ($this->_canCapture != $canCapture) {
            $this->_canCapture = $canCapture;
        }
    }

    public function getConfigInterface()
    {
        return $this;
    }

    public function getValue($field, $storeId = null)
    {
        return $this->getConfigData($field, $storeId);
    }

    /**
     * @param string $methodCode
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return void
     * @codingStandardsIgnoreStart
     */
    public function setMethodCode($methodCode)
    {
    }
    // @codingStandardsIgnoreEnd

    /**
     * @param string $pathPattern
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return void
     * @codingStandardsIgnoreStart
     */
    public function setPathPattern($pathPattern)
    {
    }
    // @codingStandardsIgnoreEnd

    public function isInitializeNeeded()
    {
        return !$this->oPSPaymentHelper->isInlinePayment($this->getInfoInstance());
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodDependendFormFields($order, $requestParams = null)
    {
        $formFields = parent::getMethodDependendFormFields($order, $requestParams);
        if ($this->oPSConfig->getCreditDebitSplit($order->getStoreId())) {
            $formFields['CREDITDEBIT'] = "C";
        }

        $alias = $order->getPayment()->getAdditionalInformation('alias') ?: '';

        $formFields['ALIAS'] = $alias;

        if ($this->getConfigData('active_alias')) {
            if ($alias) {
                $formFields['ALIASOPERATION'] = "BYPSP";
                $formFields['ECI'] = 9;
                $formFields['ALIASUSAGE'] = $this->getConfig()->getAliasUsageForExistingAlias(
                    $order->getPayment()->getMethodInstance()->getCode(),
                    $order->getStoreId()
                );
            } else {
                $formFields['ALIASOPERATION'] = "BYPSP";
                $formFields['ALIASUSAGE'] = $this->getConfig()->getAliasUsageForNewAlias(
                    $order->getPayment()->getMethodInstance()->getCode(),
                    $order->getStoreId()
                );
            }
        }

        return $formFields;
    }
}

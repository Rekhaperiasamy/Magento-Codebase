<?php

namespace Netresearch\OPS\Model\Payment;

/**
 * @package
 * @copyright 2016 Netresearch
 * @author    Paul Siedler <paul.siedler@netresearch.de>
 * @license   OSL 3.0
 */
class Debitcard extends \Netresearch\OPS\Model\Payment\Cc
{
    const CODE = 'ops_dc';

    /** payment code */
    protected $_code = self::CODE;

    /**
     * @var \Netresearch\OPS\Helper\Debitcard
     */
    protected $oPSDebitcardHelper;

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
     * @param \Netresearch\OPS\Helper\Debitcard $oPSDebitcardHelper
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
        Features\ZeroAmountAuthFactory $oPSPaymentFeaturesZeroAmountAuthFactory,
        \Netresearch\OPS\Helper\Debitcard $oPSDebitcardHelper,
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
            $oPSAliasHelper,
            $oPSCreditcardHelper,
            $oPSPaymentFeaturesZeroAmountAuthFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->oPSDebitcardHelper = $oPSDebitcardHelper;
    }

    /** ops payment code */
    public function getOpsCode($payment = null)
    {
        return 'CreditCard';
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodDependendFormFields($order, $requestParams = null)
    {
        $formFields = parent::getMethodDependendFormFields($order, $requestParams);
        if ($this->getConfig()->getCreditDebitSplit($order->getStoreId())) {
            $formFields['CREDITDEBIT'] = "D";
        }

        return $formFields;
    }

    /**
     * @return \Netresearch\OPS\Helper\Debitcard
     */
    public function getRequestParamsHelper()
    {
        if (null === $this->requestParamsHelper) {
            $this->requestParamsHelper = $this->oPSDebitcardHelper;
        }

        return $this->requestParamsHelper;
    }
}

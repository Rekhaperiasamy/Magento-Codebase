<?php
/**
 * \Netresearch\OPS\Model\Payment\DirectDebit
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */

namespace Netresearch\OPS\Model\Payment;

class DirectDebit extends \Netresearch\OPS\Model\Payment\DirectLink
{
    const CODE = 'ops_directDebit';
    /* define a specific form block */
    protected $_formBlockType = 'Netresearch\OPS\Block\Form\DirectDebit';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';

    /** payment code */
    protected $_code = self::CODE;

    /**
     * @var \Netresearch\OPS\Helper\DirectDebit
     */
    protected $oPSDirectDebitHelper;

    /**
     * DirectDebit constructor.
     *
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
     * @param \Netresearch\OPS\Helper\DirectDebit $oPSDirectDebitHelper
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
        \Netresearch\OPS\Helper\DirectDebit $oPSDirectDebitHelper,
        \Netresearch\OPS\Helper\Alias $oPSAliasHelper,
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
        $this->oPSDirectDebitHelper = $oPSDirectDebitHelper;
    }

    public function getPaymentAction()
    {
        return \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE;
    }

    public function getOrderPlaceRedirectUrl()
    {
        // Prevent redirect on direct debit payment
        return false;
    }

    /**
     * @return \Netresearch\OPS\Helper\DirectDebit
     */
    public function getRequestParamsHelper()
    {
        if (null === $this->requestParamsHelper) {
            $this->requestParamsHelper = $this->oPSDirectDebitHelper;
        }

        return $this->requestParamsHelper;
    }

    protected function performPreDirectLinkCallActions(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Framework\DataObject $payment,
        $requestParams = []
    ) {
    
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
     * {@inheritdoc}
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);

        $additionalData = $data->getAdditionalData();
        $info = $this->getInfoInstance();
        $brand = isset($additionalData['brand']) ? $additionalData['brand'] : null;
        $alias = isset($additionalData['alias']) ? $additionalData['alias'] : null;

        if ($alias) {
            $info->setAdditionalInformation('alias', $alias);
        }
        if ($brand) {
            $info->setAdditionalInformation('BRAND', $brand);
            $info->setAdditionalInformation('PM', $brand);
        }

        return $this;
    }
}

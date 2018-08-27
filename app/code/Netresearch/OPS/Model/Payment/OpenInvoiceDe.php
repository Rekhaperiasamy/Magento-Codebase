<?php
/**
 * \Netresearch\OPS\Model\Payment\OpenInvoiceDe
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Model\Payment;

class OpenInvoiceDe extends \Netresearch\OPS\Model\Payment\OpenInvoice\OpenInvoiceAbstract
{
    const CODE = 'ops_openInvoiceDe';

    protected $pm = 'Open Invoice DE';
    protected $brand = 'Open Invoice DE';

    /** if we can capture directly from the backend */
    protected $_canBackendDirectCapture = false;

    protected $_canCapturePartial = false;
    protected $_canRefundInvoicePartial = false;

    /** payment code */
    protected $_code = 'ops_openInvoiceDe';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

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
        \Magento\Eav\Model\Config $eavConfig,
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
        $this->eavConfig = $eavConfig;
    }

    /**
     * Open Invoice DE is not available if quote has a coupon
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return boolean
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        /* availability depends on quote */
        if (false == $quote instanceof \Magento\Quote\Model\Quote) {
            return false;
        }

        /* not available if quote contains a coupon and allow_discounted_carts is disabled */
        if (!$this->isAvailableForDiscountedCarts()
            && $quote->getSubtotal() != $quote->getSubtotalWithDiscount()
        ) {
            return false;
        }

        /* not available if there is no gender or no birthday */
        if (null === $quote->getCustomerGender() || null === $quote->getCustomerDob()) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    public function getPaymentAction()
    {
        return \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE;
    }

    public function getMethodDependendFormFields($order, $requestParams = null)
    {
        $formFields = parent::getMethodDependendFormFields($order, $requestParams);

        $shippingAddress = $order->getShippingAddress();

        $gender = $this->eavConfig
            ->getAttribute('customer', 'gender')
            ->getSource()
            ->getOptionText($order->getCustomerGender());

        $formFields['CIVILITY']               = $gender == 'Male' ? 'Herr' : 'Frau';
        $formFields[ 'ECOM_CONSUMER_GENDER' ] = $gender == 'Male' ? 'M' : 'F';

        if (!$this->getConfig()->canSubmitExtraParameter($order->getStoreId())) {
            // add the shipto parameters even if the submitOption is false, because they are required for OpenInvoice
            $shipToParams = $this->getRequestHelper()->extractShipToParameters($shippingAddress, $order);
            $formFields   = array_merge($formFields, $shipToParams);
        }

        return $formFields;
    }

    /**
     * getter for the allow_discounted_carts
     *
     * @return bool
     */
    protected function isAvailableForDiscountedCarts()
    {
        return (bool) $this->getConfigData('allow_discounted_carts');
    }
}

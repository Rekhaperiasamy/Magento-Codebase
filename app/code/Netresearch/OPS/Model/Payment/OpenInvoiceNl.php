<?php
/**
 * \Netresearch\OPS\Model\Payment\OpenInvoiceNl
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Model\Payment;

class OpenInvoiceNl extends \Netresearch\OPS\Model\Payment\OpenInvoice\OpenInvoiceAbstract
{
    const CODE = 'ops_openInvoiceNl';

    protected $pm = 'Open Invoice NL';
    protected $brand = 'Open Invoice NL';

    /** if we can capture directly from the backend */
    protected $_canBackendDirectCapture = false;

    /** payment code */
    protected $_code = 'ops_openInvoiceNl';

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
     * Open Invoice NL is not available if quote has a coupon
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return boolean
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        /* availability depends on quote */
        if (false == $quote instanceof \Magento\Quote\Model\Quote) {
            return false;
        }

        /* not available if there is no gender or no birthday */
        if (null === $quote->getCustomerGender() || null === $quote->getCustomerDob()) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    /**
     * get some method dependend form fields
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getMethodDependendFormFields($order, $requestParams = null)
    {
        $billingAddress  = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        $street = implode(' ', $billingAddress->getStreet());
        $regexp = '/^([^0-9]*)([0-9].*)$/';
        if (!preg_match($regexp, $street, $splittedStreet)) {
            $splittedStreet[1] = $street;
            $splittedStreet[2] = '';
        }
        $formFields = parent::getMethodDependendFormFields($order, $requestParams);

        $gender = $this->eavConfig
            ->getAttribute('customer', 'gender')
            ->getSource()
            ->getOptionText($order->getCustomerGender());

        $formFields['CIVILITY']                         = $gender == 'Male' ? 'M' : 'V';
        $formFields['ECOM_CONSUMER_GENDER']             = $gender == 'Male' ? 'M' : 'V';
        $formFields['OWNERADDRESS']                     = trim($splittedStreet[1]);
        $formFields['ECOM_BILLTO_POSTAL_STREET_NUMBER'] = trim($splittedStreet[2]);
        $formFields['OWNERZIP']                         = $billingAddress->getPostcode();
        $formFields['OWNERTOWN']                        = $billingAddress->getCity();
        $formFields['OWNERCTY']                         = $billingAddress->getCountryId();
        $formFields['OWNERTELNO']                       = $billingAddress->getTelephone();

        $street = implode(' ', $shippingAddress->getStreet());
        if (!preg_match($regexp, $street, $splittedStreet)) {
            $splittedStreet[1] = $street;
            $splittedStreet[2] = '';
        }
        $formFields['ECOM_SHIPTO_POSTAL_NAME_PREFIX']   = $shippingAddress->getPrefix();
        $formFields['ECOM_SHIPTO_POSTAL_NAME_FIRST']    = $shippingAddress->getFirstname();
        $formFields['ECOM_SHIPTO_POSTAL_NAME_LAST']     = $shippingAddress->getLastname();
        $formFields['ECOM_SHIPTO_POSTAL_STREET_LINE1']  = trim($splittedStreet[1]);
        $formFields['ECOM_SHIPTO_POSTAL_STREET_NUMBER'] = trim($splittedStreet[2]);
        $formFields['ECOM_SHIPTO_POSTAL_POSTALCODE']    = $shippingAddress->getPostcode();
        $formFields['ECOM_SHIPTO_POSTAL_CITY']          = $shippingAddress->getCity();
        $formFields['ECOM_SHIPTO_POSTAL_COUNTRYCODE']   = $shippingAddress->getCountryId();

        // copy some already known values
        $formFields['ECOM_SHIPTO_ONLINE_EMAIL']         = $order->getCustomerEmail();

        if (is_array($requestParams)) {
            if (array_key_exists('OWNERADDRESS', $requestParams)) {
                $formFields['OWNERADDRESS'] = $requestParams['OWNERADDRESS'];
            }
            if (array_key_exists('ECOM_BILLTO_POSTAL_STREET_NUMBER', $requestParams)) {
                $formFields['ECOM_BILLTO_POSTAL_STREET_NUMBER'] = $requestParams['ECOM_BILLTO_POSTAL_STREET_NUMBER'];
            }
            if (array_key_exists('ECOM_SHIPTO_POSTAL_STREET_LINE1', $requestParams)) {
                $formFields['ECOM_SHIPTO_POSTAL_STREET_LINE1'] = $requestParams['ECOM_SHIPTO_POSTAL_STREET_LINE1'];
            }
            if (array_key_exists('ECOM_SHIPTO_POSTAL_STREET_NUMBER', $requestParams)) {
                $formFields['ECOM_SHIPTO_POSTAL_STREET_NUMBER'] = $requestParams['ECOM_SHIPTO_POSTAL_STREET_NUMBER'];
            }
        }

        return $formFields;
    }

    /**
     * get question for fields with disputable value
     * users are asked to correct the values before redirect to Ingenico ePayments
     *
     * @param \Magento\Sales\Model\Order $order         Current order
     * @param array                      $requestParams Request parameters
     * @return string
     */
    public function getQuestion($order, $requestParams)
    {
        return __('Please make sure that your street and house number are correct.');
    }

    /**
     * get an array of fields with disputable value
     * users are asked to correct the values before redirect to Ingenico ePayments
     *
     * @param \Magento\Sales\Model\Order $order         Current order
     * @param array                      $requestParams Request parameters
     * @return array
     */
    public function getQuestionedFormFields($order, $requestParams)
    {
        return [
            'OWNERADDRESS',
            'ECOM_BILLTO_POSTAL_STREET_NUMBER',
            'ECOM_SHIPTO_POSTAL_STREET_LINE1',
            'ECOM_SHIPTO_POSTAL_STREET_NUMBER',
        ];
    }
}

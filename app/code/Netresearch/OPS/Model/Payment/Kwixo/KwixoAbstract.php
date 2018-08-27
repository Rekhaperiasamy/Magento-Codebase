<?php
namespace Netresearch\OPS\Model\Payment\Kwixo;

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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc.
 *              DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 */
class KwixoAbstract extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    private $kwixoShippingModel = null;

    private $shippingSettings = null;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $catalogProductFactory;

    /**
     * @var \Netresearch\OPS\Model\Kwixo\Category\MappingFactory
     */
    protected $oPSKwixoCategoryMappingFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Netresearch\OPS\Model\Kwixo\Shipping\SettingFactory
     */
    protected $oPSKwixoShippingSettingFactory;

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
        \Magento\Catalog\Model\ProductFactory $catalogProductFactory,
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
        \Netresearch\OPS\Model\Kwixo\Category\MappingFactory $oPSKwixoCategoryMappingFactory,
        \Netresearch\OPS\Model\Kwixo\Shipping\SettingFactory $oPSKwixoShippingSettingFactory,
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
        $this->catalogProductFactory = $catalogProductFactory;
        $this->eavConfig = $eavConfig;
        $this->oPSKwixoCategoryMappingFactory = $oPSKwixoCategoryMappingFactory;
        $this->oPSKwixoShippingSettingFactory = $oPSKwixoShippingSettingFactory;
    }

    /**
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array                  $requestParams
     */
    public function getMethodDependendFormFields($order, $requestParams = null)
    {
        $formFields = parent::getMethodDependendFormFields(
            $order,
            $requestParams
        );
        unset($formFields['OWNERADDRESS']);
        unset($formFields['OWNERTELNO']);
        unset($formFields['ECOM_SHIPTO_POSTAL_STREET_LINE1']);
        $shippingMethod = 'none';
        $isVirtual      = true;
        if ($order->getShippingAddress()) {
            $isVirtual   = false;
            $carrierCode = $order->getShippingCarrier()->getCarrierCode();
            $this->loadShippingSettingForCarrierCode($carrierCode);
            $shippingMethod = $carrierCode;
        }

        $formFields['ECOM_ESTIMATEDELIVERYDATE']
                                            = $this->getEstimatedDeliveryDate(
                                                $this->getCode(),
                                                $order->getStoreId()
                                            );
        $formFields['RNPOFFERT']            = $this->getRnpFee(
            $this->getCode(),
            $order->getStoreId()
        );
        $formFields['ECOM_SHIPMETHODTYPE']  = $this->getShippingMethodType(
            $this->getCode(),
            $order->getStoreId(),
            $isVirtual
        );
        $formFields['ECOM_SHIPMETHODSPEED'] = $this->getShippingMethodSpeed(
            $this->getCode(),
            $order->getStoreId()
        );
        $shipMethodDetails                  = $this->getShippingMethodDetails(
            $this->getCode(),
            $order->getStoreId()
        );
        if (0 < strlen(trim($shipMethodDetails))) {
            $formFields['ECOM_SHIPMETHODDETAILS'] = $shipMethodDetails;
        }
        if (4 == $formFields['ECOM_SHIPMETHODTYPE']
            && !array_key_exists(
                'ECOM_SHIPMETHODDETAILS',
                $formFields
            )
        ) {
            $address                              = $order->getShippingAddress()
                ? $order->getShippingAddress()->toString()
                : $order->getBillingAddress()->toString();
            $formFields['ECOM_SHIPMETHODDETAILS'] = substr($address, 0, 50);
        }

        $formFields['ORDERSHIPMETH'] = $shippingMethod;

        $formFields['CIVILITY']
                    = $this->getGender($order) == 'Male' ? 'Mr' : 'Mrs';
        $formFields = array_merge(
            $formFields,
            $this->getKwixoBillToParams($order)
        );
        $formFields = array_merge(
            $formFields,
            $this->getKwixoShipToParams($order)
        );
        $formFields = array_merge(
            $formFields,
            $this->getItemParams($order)
        );

        $formFields['ORDERID'] = $this->oPSOrderHelper->getOpsOrderId(
            $order,
            false
        );
        $formFields = $this->populateFromArray($formFields, $requestParams, $order);

        return $formFields;
    }

    protected function getKwixoCategoryFromOrderItem(\Magento\Sales\Model\Order\Item $item)
    {
        $product         = $this->catalogProductFactory->create()->load(
            $item->getProductId()
        );
        $kwixoCategoryId = null;
        foreach ($product->getCategoryIds() as $categoryId) {
            $kwixoCategory = $this->oPSKwixoCategoryMappingFactory->create()
                ->loadByCategoryId($categoryId);
            if (null !== $kwixoCategory->getId()) {
                $kwixoCategoryId = $kwixoCategory->getKwixoCategoryId();
                break;
            }
        }

        return $kwixoCategoryId;
    }

    /**
     *
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array                  $requestParams
     *
     * @return array
     */
    public function getKwixoBillToParams(\Magento\Sales\Model\Order $order)
    {
        $formFields = [];
        $billingAddress
                        = $order->getBillingAddress();
        $splittedStreet = $this->splitHouseNumber(
            $billingAddress->getStreet1()
        );
        $formFields['ECOM_BILLTO_POSTAL_NAME_FIRST']
                        = $billingAddress->getFirstname();
        $formFields['ECOM_BILLTO_POSTAL_NAME_LAST']
                                    = $billingAddress->getLastname();
        $formFields['OWNERADDRESS'] = str_replace(
            "\n",
            ' ',
            $billingAddress->getStreetLine(1)
        );
        if (array_key_exists('street', $splittedStreet)) {
            $formFields['OWNERADDRESS'] = trim($splittedStreet['street']);
        }
        $streetAppendix = trim($billingAddress->getStreetLine(2));
        if (0 < strlen($streetAppendix)) {
            $formFields['OWNERADDRESS2'] = $streetAppendix;
        }
        $formFields['ECOM_BILLTO_POSTAL_STREET_NUMBER'] = '';
        if (array_key_exists('housenumber', $splittedStreet)) {
            $formFields['ECOM_BILLTO_POSTAL_STREET_NUMBER']
                = $splittedStreet['housenumber'];
        }
        $formFields['OWNERTELNO'] = $billingAddress->getTelephone();

        return $formFields;
    }

    /**
     * return the shipping parameters as array based on shipping method type
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array                  $requestParams
     *
     * @return array
     */
    public function getKwixoShipToParams(\Magento\Sales\Model\Order $order)
    {
        $formFields      = [];
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress === false) {
            $shippingAddress = $order->getBillingAddress();
        }
        $splittedStreet = $this->splitHouseNumber(
            $shippingAddress->getStreet1()
        );

        $shippingMethodType = (int)$this->getShippingMethodType(
            $this->getCode(),
            $order->getStoreId()
        );

        if (in_array(
            $shippingMethodType,
            $this->getShippingMethodTypeValues()
        )
        ) {
            if (4 === $shippingMethodType) {
                $formFields['ECOM_SHIPTO_POSTAL_NAME_PREFIX']
                    = $shippingAddress->getPrefix();
            }

            $company = trim($shippingAddress->getCompany());
            if (0 < strlen($company)) {
                $formFields['ECOM_SHIPTO_COMPANY'] = $company;
            }
            $fax = trim($shippingAddress->getFax());
            if (0 < strlen($fax)) {
                $formFields['ECOM_SHIPTO_TELECOM_FAX_NUMBER'] = $fax;
            }

            $formFields['ECOM_SHIPTO_POSTAL_STREET_LINE1']
                = $shippingAddress->getStreetLine(1);
            if (array_key_exists('street', $splittedStreet)) {
                $formFields['ECOM_SHIPTO_POSTAL_STREET_LINE1']
                    = $splittedStreet['street'];
            }
            $streetAppendix = trim($shippingAddress->getStreetLine(2));
            if (0 < strlen($streetAppendix)) {
                $formFields['ECOM_SHIPTO_POSTAL_STREET_LINE2']
                    = $streetAppendix;
            }
            $formFields['ECOM_SHIPTO_POSTAL_STREET_NUMBER'] = '';
            if (array_key_exists('housenumber', $splittedStreet)) {
                $formFields['ECOM_SHIPTO_POSTAL_STREET_NUMBER']
                    = $splittedStreet['housenumber'];
            }
            $formFields['ECOM_SHIPTO_POSTAL_POSTALCODE']
                = $shippingAddress->getPostcode();
            $formFields['ECOM_SHIPTO_POSTAL_CITY']
                = $shippingAddress->getCity();
            $formFields['ECOM_SHIPTO_POSTAL_COUNTRYCODE']
                = $shippingAddress->getCountryId();
        }
        $formFields['ECOM_SHIPTO_POSTAL_NAME_FIRST']
            = $shippingAddress->getFirstname();
        $formFields['ECOM_SHIPTO_POSTAL_NAME_LAST']
            = $shippingAddress->getLastname();
        $formFields['ECOM_SHIPTO_TELECOM_PHONE_NUMBER']
            = $shippingAddress->getTelephone();

        return $formFields;
    }

    /**
     * return item params for the order
     * for each item a ascending number will be added to the parameter name
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return array
     */
    public function getItemParams(\Magento\Sales\Model\Order $order)
    {
        $formFields = [];
        $items      = $order->getAllItems();
        $subtotal   = 0;
        if (is_array($items)) {
            $itemCounter = 1;
            foreach ($items as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }

                $subtotal += $item->getBasePriceInclTax(
                ) * $item->getQtyOrdered();
                $formFields['ITEMFDMPRODUCTCATEG' . $itemCounter]
                                                        = $this->getKwixoCategoryFromOrderItem(
                                                            $item
                                                        );
                $formFields['ITEMID' . $itemCounter]    = $item->getItemId();
                $formFields['ITEMNAME' . $itemCounter]  = substr(
                    $item->getName(),
                    0,
                    40
                );
                $formFields['ITEMPRICE' . $itemCounter] = number_format(
                    $item->getBasePriceInclTax(),
                    2,
                    '.',
                    ''
                );
                $formFields['ITEMQUANT' . $itemCounter]
                                                          = (int)$item->getQtyOrdered(
                                                          );
                $formFields['ITEMVAT' . $itemCounter]     = str_replace(
                    ',',
                    '.',
                    (string)(float)$item->getBaseTaxAmount()
                );
                $formFields['TAXINCLUDED' . $itemCounter] = 1;
                $itemCounter++;
            }
            $shippingPrice        = $order->getBaseShippingAmount();
            $shippingPriceInclTax = $order->getBaseShippingInclTax();
            $subtotal += $shippingPriceInclTax;
            $shippingTaxAmount = $shippingPriceInclTax - $shippingPrice;

            $roundingError = $order->getBaseGrandTotal() - $subtotal;
            $shippingPrice += $roundingError;
            /* add shipping item */
            $formFields['ITEMFDMPRODUCTCATEG' . $itemCounter] = 1;
            $formFields['ITEMID' . $itemCounter]              = 'SHIPPING';
            $shippingDescription
                                                              =
                0 < strlen(trim($order->getShippingDescription()))
                    ? $order->getShippingDescription() : 'shipping';
            $formFields['ITEMNAME' . $itemCounter]            = substr(
                $shippingDescription,
                0,
                30
            );
            $formFields['ITEMPRICE' . $itemCounter]           = number_format(
                $shippingPrice,
                2,
                '.',
                ''
            );
            $formFields['ITEMQUANT' . $itemCounter]           = 1;
            $formFields['ITEMVAT' . $itemCounter]             = number_format(
                $shippingTaxAmount,
                2,
                '.',
                ''
            );
        }

        return $formFields;
    }

    /**
     * splits house number and street for france addresses
     *
     * @param string $address
     *
     * @return array
     */
    public function splitHouseNumber($address)
    {
        $splittedStreet = [];
        $street         = str_replace("\n", ' ', $address);
        $regexp         = '/(?P<housenumber>[0-9]+)([,:\s])(?P<street>.+)/';
        if (!preg_match($regexp, $street, $splittedStreet)) {
            $splittedStreet[1] = $street;
            $splittedStreet[2] = '';
        }

        return $splittedStreet;
    }

    /**
     * returns the delivery date as date based on actual date and adding
     * the configurated value as days to it
     *
     * @param string $code
     * @param string $storeId
     *
     * @return bool|string
     */
    public function getEstimatedDeliveryDate($code, $storeId = null)
    {
        $dateNow      = date("Y-m-d");
        $dayValue     = (string)$this->_scopeConfig->getValue(
            'payment/' . $code . "/delivery_date",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $deliveryDate = strtotime($dateNow . "+" . $dayValue . "days");

        return date("Y-m-d", $deliveryDate);
    }

    /**
     * return the RNP Fee value
     *
     * @param string $code
     * @param int    $storeId
     *
     * @return boolean
     */
    public function getRnpFee($code, $storeId = null)
    {
        return (int)(bool)$this->_scopeConfig->getValue(
            "payment/" . $code . "/rnp_fee",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * returns the Shipping Method Type configured in backend
     *
     * @param string $code
     * @param int    $storeId
     *
     * @return string
     */
    public function getShippingMethodType(
        $code,
        $storeId = null,
        $isVirtual = false
    ) {
    
        // use download type for orders containing virtual products only
        if ($isVirtual) {
            return \Netresearch\OPS\Model\Source\Kwixo\ShipMethodType::DOWNLOAD;
        }
        $shippingMethodType = $this->getKwixoShippingModel()
            ->getKwixoShippingType();
        if (null === $shippingMethodType) {
            $shippingMethodType = $this->_scopeConfig->getValue(
                "payment/" . $code . "/ecom_shipMethodType",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        return $shippingMethodType;
    }

    /**
     * return the shipping method speed configured in backend
     *
     * @param string $code
     * @param int    $storeId
     *
     * @return int
     */
    public function getShippingMethodSpeed($code, $storeId = null)
    {
        $shippingMethodSpeed = $this->getKwixoShippingModel()
            ->getKwixoShippingMethodSpeed();
        if (null === $shippingMethodSpeed) {
            $shippingMethodSpeed = $this->_scopeConfig->getValue(
                "payment/" . $code . "/ecom_shipMethodSpeed",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        return (int)$shippingMethodSpeed;
    }

    /**
     * return the item product categories configured in backend as array
     *
     * @param string $code
     * @param int    $storeId
     *
     * @return array
     */
    public function getItemFmdProductCateg($code, $storeId = null)
    {
        return explode(
            ",",
            $this->_scopeConfig->getValue(
                "payment/" . $code . "/product_categories",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );
    }

    /**
     * return the shipping method detail text
     *
     * @param string $code
     * @param int    $storeId
     *
     * @return string
     */
    public function getShippingMethodDetails($code, $storeId = null)
    {
        $shippingMethodDetails = $this->getKwixoShippingModel()
            ->getKwixoShippingDetails();
        if (null === $shippingMethodDetails) {
            $shippingMethodDetails = $this->_scopeConfig->getValue(
                "payment/" . $code . "/shiping_method_details",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        return $shippingMethodDetails;
    }

    /**
     * get question for fields with disputable value
     * users are asked to correct the values before redirect to Ingenico ePayments
     *
     * @param \Magento\Sales\Model\Order $order         Current order
     * @param array                  $requestParams Request parameters
     *
     * @return string
     */
    public function getQuestion($order, $requestParams)
    {
        return __(
            'Please make sure that the displayed data is correct.'
        );
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
    public function getQuestionedFormFields($order, $requestParams)
    {
        $questionedFormFields = [
            'CIVILITY',
            'OWNERADDRESS',
            'ECOM_BILLTO_POSTAL_STREET_NUMBER',

        ];
        $storeId              = null;
        if ($order instanceof \Magento\Sales\Model\Order) {
            $storeId = $order->getStoreId();
        }
        $shippingMethodType = (int)$this->getShippingMethodType(
            $this->getCode(),
            $storeId
        );
        if (in_array(
            $shippingMethodType,
            $this->getShippingMethodTypeValues()
        )
        ) {
            $questionedFormFields [] = 'ECOM_SHIPTO_POSTAL_STREET_NUMBER';
            $questionedFormFields [] = 'ECOM_SHIPTO_POSTAL_STREET_LINE1';
        }

        if ($shippingMethodType === 4) {
            $questionedFormFields [] = 'ECOM_SHIPTO_TELECOM_PHONE_NUMBER';
            $questionedFormFields [] = 'ECOM_SHIPTO_POSTAL_NAME_PREFIX';
        }

        return $questionedFormFields;
    }

    /**
     * return shipping method values except for the type download
     *
     * @return array
     */
    public function getShippingMethodTypeValues()
    {
        return [1, 2, 3, 4];
    }

    /**
     * populates an array with the values from another if the keys are matching
     *
     * @param array $formFields - the array to populate
     * @param array $dataArray  - the array containing the data
     *
     * @return array - the populated array
     */
    protected function populateFromArray(
        array $formFields,
        $dataArray,
        $order
    ) {
    
        // copy some already known values, but only the ones from the questioned
        // form fields
        if (is_array($dataArray)) {
            foreach ($dataArray as $key => $value) {
                if (array_key_exists($key, $formFields)
                    && in_array(
                        $key,
                        $this->getQuestionedFormFields($order, $dataArray),
                        true
                    )
                    || $key == 'CIVILITY'
                ) {
                    $formFields[$key] = $value;
                }
            }
        }

        return $formFields;
    }

    /**
     * get gender text for customer
     *
     * @param \Magento\Sales\Model\Order $order
     */
    public function getGender(\Magento\Sales\Model\Order $order)
    {
        $gender = $this->eavConfig
            ->getAttribute('customer', 'gender')
            ->getSource()
            ->getOptionText($order->getCustomerGender());

        return $gender;
    }

    /**
     * sets the kwixo shipping setting model
     *
     * @param \Netresearch\OPS\Model\Kwixo\Shipping\Setting $kwixoShippingModel
     */
    public function setKwixoShippingModel(\Netresearch\OPS\Model\Kwixo\Shipping\Setting $kwixoShippingModel)
    {
        $this->kwixoShippingModel = $kwixoShippingModel;
    }

    /**
     * returns the kwixo shipping setting model
     *
     * @return \Netresearch\OPS\Model\Kwixo\Shipping\Setting
     */
    public function getKwixoShippingModel()
    {
        if (null === $this->kwixoShippingModel) {
            $this->kwixoShippingModel = $this->oPSKwixoShippingSettingFactory->create();
        }

        return $this->kwixoShippingModel;
    }

    /**
     * @param $carrierCode
     */
    private function loadShippingSettingForCarrierCode($carrierCode)
    {
        $this->shippingSettings = $this->getKwixoShippingModel()->load(
            $carrierCode,
            'shipping_code'
        );

        return $this->shippingSettings;
    }
}

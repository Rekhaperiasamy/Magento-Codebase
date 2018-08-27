<?php
namespace Netresearch\OPS\Block\Form;

/**
 * @package   OPS
 * @copyright 2012 Netresearch App Factory AG <http://www.netresearch.de>
 * @author    Thomas Birke <thomas.birke@netresearch.de>
 * @license   OSL 3.0
 */
class Cc extends \Netresearch\OPS\Block\Form
{
    /**
     * Cc Payment Template
     */
    const FRONTEND_TEMPLATE = 'Netresearch_OPS::ops/form/cc.phtml';

    private $aliasDataForCustomer = [];

    /**
     * @var \Netresearch\OPS\Model\Source\Cc\AliasInterfaceEnabledTypesFactory
     */
    protected $oPSSourceCcAliasEnabledTypesFactory;

    /**
     * @var \Magento\Backend\Model\Session\QuoteFactory
     */
    protected $backendSessionQuoteFactory;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * Cc constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Netresearch\OPS\Model\Config $oPSConfig
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     * @param \Netresearch\OPS\Helper\Alias $oPSAliasHelper
     * @param \Netresearch\OPS\Model\Source\Cc\AliasInterfaceEnabledTypesFactory $oPSSourceCcAliasEnabledTypesFactory
     * @param \Magento\Backend\Model\Session\QuoteFactory $backendSessionQuoteFactory
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Customer\Model\Session $customerSession,
        \Netresearch\OPS\Model\Config $oPSConfig,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Helper\Alias $oPSAliasHelper,
        \Netresearch\OPS\Model\Source\Cc\AliasInterfaceEnabledTypesFactory $oPSSourceCcAliasEnabledTypesFactory,
        \Magento\Backend\Model\Session\QuoteFactory $backendSessionQuoteFactory,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $paymentConfig,
            $checkoutSession,
            $jsonEncoder,
            $customerSession,
            $oPSConfig,
            $oPSHelper,
            $oPSAliasHelper,
            $data
        );
        $this->oPSSourceCcAliasEnabledTypesFactory = $oPSSourceCcAliasEnabledTypesFactory;
        $this->backendSessionQuoteFactory = $backendSessionQuoteFactory;
        $this->localeResolver = $localeResolver;
        $this->formKey = $formKey;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate(self::FRONTEND_TEMPLATE);
    }

    /**
     * gets all Alias CC brands
     *
     * @return array
     */
    public function getAliasBrands()
    {
        return $this->oPSSourceCcAliasEnabledTypesFactory->create()
            ->getAliasInterfaceCompatibleTypes();
    }

    /**
     * @param null $storeId
     * @param bool $admin
     *
     * @return string
     */
    public function getAliasAcceptUrl($storeId = null, $admin = false)
    {
        return $this->getConfig()->getAliasAcceptUrl($storeId, $admin, $this->getMethodCode());
    }

    /**
     * @param null $storeId
     * @param bool $admin
     *
     * @return string
     */
    public function getAliasExceptionUrl($storeId = null, $admin = false)
    {
        return $this->getConfig()->getAliasExceptionUrl($storeId, $admin);
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getAliasGatewayUrl($storeId = null)
    {
        return $this->getConfig()->getAliasGatewayUrl($storeId);
    }

    /**
     * @param null $storeId
     * @param bool $admin
     *
     * @return mixed
     */
    public function getCcSaveAliasUrl($storeId = null, $admin = false)
    {
        return $this->getConfig()->getCcSaveAliasUrl($storeId, $admin);
    }

    /**
     * checks if the 'alias' payment method (!) is available
     * no check for customer has aliases here
     * just a passthrough of the isAvailable of \Netresearch\OPS\Model\Payment\PaymentAbstract::isAvailable
     *
     * @return boolean
     */
    public function isAliasPMEnabled()
    {
        return $this->getConfig()->isAliasManagerEnabled($this->getMethodCode());
    }

    /**
     * retrieves the alias data for the logged in customer
     *
     * @return array | null - array the alias data or null if the customer
     * is not logged in
     */
    protected function getStoredAliasForCustomer()
    {
        if ($this->customerSession->isLoggedIn()
            && $this->getConfig()->isAliasManagerEnabled($this->getMethodCode())) {
            $quote = $this->getQuote();
            $aliases = $this->oPSAliasHelper->getAliasesForAddresses(
                $quote->getCustomer()->getId(),
                $quote->getBillingAddress(),
                $quote->getShippingAddress(),
                $quote->getStoreId()
            )
                ->addFieldToFilter('state', \Netresearch\OPS\Model\Alias\State::ACTIVE)
                ->addFieldToFilter('payment_method', $this->getMethodCode())
                ->setOrder('created_at', \Magento\Framework\Data\Collection::SORT_ORDER_DESC);

            foreach ($aliases as $key => $alias) {
                $this->aliasDataForCustomer[$key] = $alias;
            }
        }

        return $this->aliasDataForCustomer;
    }

    /**
     * retrieves single values to given keys from the alias data
     *
     * @param $aliasId
     * @param $key - string the key for the alias data
     *
     * @return null|string - null if key is not set in the alias data, otherwise
     * the value for the given key from the alias data
     */
    protected function getStoredAliasDataForCustomer($aliasId, $key)
    {
        $returnValue = null;
        $aliasData = [];

        if (empty($this->aliasDataForCustomer)) {
            $aliasData = $this->getStoredAliasForCustomer();
        } else {
            $aliasData = $this->aliasDataForCustomer;
        }

        if (array_key_exists($aliasId, $aliasData) && $aliasData[$aliasId]->hasData($key)) {
            $returnValue = $aliasData[$aliasId]->getData($key);
        }

        return $returnValue;
    }

    /**
     * retrieves the given path (month or year) from stored expiration date
     *
     * @param $key - the requested path
     *
     * @return null | string the extracted part of the date
     */
    public function getExpirationDatePart($aliasId, $key)
    {
        $returnValue = null;
        $expirationDate = $this->getStoredAliasDataForCustomer($aliasId, 'expiration_date');
        // set expiration date to actual date if no stored Alias is used
        if ($expirationDate === null) {
            $expirationDate = date('my');
        }

        if (0 < strlen(trim($expirationDate))
        ) {
            $expirationDateValues = str_split($expirationDate, 2);

            if ($key == 'month') {
                $returnValue = $expirationDateValues[0];
            }
            if ($key == 'year') {
                $returnValue = $expirationDateValues[1];
            }

            if ($key == 'complete') {
                $returnValue = implode('/', $expirationDateValues);
            }
        }

        return $returnValue;
    }

    /**
     * retrieves the masked alias card number and formats it in a card specific format
     *
     * @return null|string - null if no alias data were found,
     * otherwise the formatted card number
     */
    public function getAliasCardNumber($aliasId)
    {
        $aliasCardNumber = $this->getStoredAliasDataForCustomer($aliasId, 'pseudo_account_or_cc_no');
        if (0 < strlen(trim($aliasCardNumber))) {
            $aliasCardNumber = $this->oPSAliasHelper->formatAliasCardNo(
                $this->getStoredAliasDataForCustomer($aliasId, 'brand'),
                $aliasCardNumber
            );
        }

        return $aliasCardNumber;
    }

    /**
     * @return null|string - the card holder either from alias data or
     * the name from the the user who is logged in, null otherwise
     */
    public function getCardHolderName($aliasId)
    {
        $cardHolderName = $this->getStoredAliasDataForCustomer($aliasId, 'card_holder');
        if ((null === $cardHolderName || 0 === strlen(trim($cardHolderName)))
            && $this->customerSession->isLoggedIn()
            && $this->getConfig()->isAliasManagerEnabled($this->getMethodCode())
        ) {
            $cardHolderName = $this->customerSession->getCustomer()->getName();
        }

        return $cardHolderName;
    }

    /**
     * the brand of the stored card data
     *
     * @return null|string - string if stored card data were found, null otherwise
     */
    public function getStoredAliasBrand($aliasId)
    {
        $storedBrand = $this->getStoredAliasDataForCustomer($aliasId, 'brand');
        $methodCode = $this->getMethodCode();
        if (in_array($storedBrand, $this->getConfig()->getInlinePaymentCcTypes($methodCode))) {
            return $storedBrand;
        }

        return '';
    }

    /**
     * determines whether the alias hint is shown to guests or not
     *
     * @return bool true if alias feature is enabled and display the hint to
     * guests is enabled
     */
    public function isAliasInfoBlockEnabled()
    {
        return ($this->isAliasPMEnabled()
            && $this->getConfig()->isAliasInfoBlockEnabled());
    }

    /**
     * @return string[]
     */
    public function getCcBrands()
    {
        return explode(',', $this->getConfig()->getAcceptedCcTypes($this->getMethodCode()));
    }

    /**
     * @return \Netresearch\OPS\Helper\Alias
     */
    public function getAliasHelper()
    {
        return $this->oPSAliasHelper;
    }

    /**
     * @return \Magento\Backend\Model\Session\Quote
     */
    public function getBackendSessionQuote()
    {
        return $this->backendSessionQuoteFactory->create();
    }

    /**
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}

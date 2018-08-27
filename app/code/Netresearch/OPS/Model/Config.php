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

namespace Netresearch\OPS\Model;

use Magento\Config\Model\Config\ScopeDefiner;
use Magento\Framework\App\Config\ScopeCodeResolver;
use Magento\Framework\Config\Scope;
use Magento\Sales\Helper\Admin;

/**
 * Config model
 */
class Config
{
    const OPS_PAYMENT_PATH = 'payment_services/ops/';
    const OPS_CONTROLLER_ROUTE_API = 'ops/api/';
    const OPS_CONTROLLER_ROUTE_PAYMENT = 'ops/payment/';
    const OPS_CONTROLLER_ROUTE_ALIAS = 'ops/alias/';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Backend\Model\UrlInterfaceFactory
     */
    protected $backendUrlInterfaceFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Backend\Model\UrlInterfaceFactory $backendUrlInterfaceFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\App\State $appState
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->backendUrlInterfaceFactory = $backendUrlInterfaceFactory;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->encryptor = $encryptor;
        $this->appState = $appState;
    }

    /**
     * Return ops payment config information
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    public function getConfigData($path, $storeId = null)
    {
        if (!empty($path)) {
            return $this->scopeConfig->getValue(
                self::OPS_PAYMENT_PATH . $path,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }
        return false;
    }

    /**
     * Return SHA1-in crypt key from config. Setup on admin place.
     *
     * @param int $storeId
     *
     * @return string
     */
    public function getShaInCode($storeId = null)
    {
        return $this->encryptor->decrypt($this->getConfigData('secret_key_in', $storeId));
    }

    /**
     * Return SHA1-out crypt key from config. Setup on admin place.
     *
     * @param int $storeId
     *
     * @return string
     */
    public function getShaOutCode($storeId = null)
    {
        return $this->encryptor->decrypt($this->getConfigData('secret_key_out', $storeId));
    }

    /**
     * Return frontend gateway path, get from config. Setup on admin place.
     *
     * @param int $storeId
     *
     * @return string
     */
    public function getFrontendGatewayPath($storeId = null)
    {
        return $this->determineOpsUrl('frontend_gateway', $storeId);
    }

    /**
     * Return Direct Link Gateway path, get from config. Setup on admin place.
     *
     * @param int $storeId
     *
     * @return string
     */
    public function getDirectLinkGatewayPath($storeId = null)
    {
        return $this->determineOpsUrl('directlink_gateway', $storeId);
    }

    public function getDirectLinkGatewayOrderPath($storeId = null)
    {
        return $this->determineOpsUrl('directlink_gateway_order', $storeId);
    }

    /**
     * Return API User, get from config. Setup on admin place.
     *
     * @param int $storeId
     *
     * @return string
     */
    public function getApiUserId($storeId = null)
    {
        return $this->getConfigData('api_userid', $storeId);
    }

    /**
     * Return API Passwd, get from config. Setup on admin place.
     *
     * @param int $storeId
     *
     * @return string
     */
    public function getApiPswd($storeId = null)
    {
        return $this->encryptor->decrypt($this->getConfigData('api_pswd', $storeId));
    }

    /**
     * Get PSPID, affiliation name in ops system
     *
     * @param int $storeId
     *
     * @return string
     */
    public function getPSPID($storeId = null)
    {
        return $this->getConfigData('pspid', $storeId);
    }

    public function getPaymentAction($storeId = null)
    {
        return $this->getConfigData('payment_action', $storeId);
    }

    /**
     * Get paypage template for magento style templates using
     *
     * @return string
     */
    public function getPayPageTemplate()
    {
        return $this->urlBuilder->getUrl(
            self::OPS_CONTROLLER_ROUTE_PAYMENT . 'paypage',
            ['_nosid' => true, '_secure' => $this->isCurrentlySecure()]
        );
    }

    /**
     * Return url which ops system will use as accept
     *
     * @return string
     */
    public function getAcceptUrl()
    {
        return $this->urlBuilder->getUrl(
            self::OPS_CONTROLLER_ROUTE_PAYMENT . 'accept',
            ['_nosid' => true, '_secure' => $this->isCurrentlySecure()]
        );
    }

    /**
     * Return url which ops system will use as accept for alias generation
     *
     * @param null $storeId
     * @param bool $admin
     * @param string $method
     * @return string
     */
    public function getAliasAcceptUrl($storeId = null, $admin = false, $method = null)
    {
        $params = [
            '_secure' => $this->isCurrentlySecure(),
            '_nosid' => true
        ];
        if (null !== $storeId) {
            $params['_scope'] = $storeId;
        }

        if ($method) {
            $params['method'] = (string) $method;
        }

        if ($this->appState->getAreaCode() === 'adminhtml') {
            $params['_nosecret'] = true;

            return $this->backendUrlInterfaceFactory->create()->getUrl('adminhtml/alias/accept', $params);
        } else {
            return $this->urlBuilder->getUrl(self::OPS_CONTROLLER_ROUTE_ALIAS . 'accept', $params);
        }
    }

    /**
     * Return url which ops system will use as decline url
     *
     * @return string
     */
    public function getDeclineUrl()
    {
        return $this->urlBuilder->getUrl(
            self::OPS_CONTROLLER_ROUTE_PAYMENT . 'decline',
            ['_nosid' => true, '_secure' => $this->isCurrentlySecure()]
        );
    }

    /**
     * Return url which ops system will use as exception url
     *
     * @return string
     */
    public function getExceptionUrl()
    {
        return $this->urlBuilder->getUrl(
            self::OPS_CONTROLLER_ROUTE_PAYMENT . 'exception',
            ['_nosid' => true, '_secure' => $this->isCurrentlySecure()]
        );
    }

    /**
     * Return url which ops system will use as exception url for alias generation
     *
     * @param null $storeId
     * @param bool $admin
     * @return string
     */
    public function getAliasExceptionUrl($storeId = null, $admin = false)
    {
        $params = [
            '_secure' => $this->isCurrentlySecure(),
            '_nosid' => true
        ];
        if (null !== $storeId) {
            $params['_scope'] = $storeId;
        }

        if ($this->appState->getAreaCode() === 'adminhtml') {
            $params['_nosecret'] = true;

            return $this->backendUrlInterfaceFactory->create()->getUrl('adminhtml/alias/exception', $params);
        } else {
            return $this->urlBuilder->getUrl(self::OPS_CONTROLLER_ROUTE_ALIAS. 'exception', $params);
        }
    }

    /**
     * Return url which ops system will use as cancel url
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->urlBuilder->getUrl(
            self::OPS_CONTROLLER_ROUTE_PAYMENT . 'cancel',
            ['_nosid' => true, '_secure' => $this->isCurrentlySecure()]
        );
    }

    /**
     * Return url which ops system will use as continue shopping url
     *
     * @param array $redirect
     *
     * @return string
     */
    public function getContinueUrl($redirect = [])
    {
        $urlParams = ['_nosid' => true, '_secure' => $this->isCurrentlySecure()];
        if (!empty($redirect)) {
            $urlParams = array_merge($redirect, $urlParams);
        }
        return $this->urlBuilder->getUrl(self::OPS_CONTROLLER_ROUTE_PAYMENT . 'continue', $urlParams);
    }

    /**
     * Return url to redirect after confirming the order
     *
     * @return string
     */
    public function getPaymentRedirectUrl()
    {
        return $this->urlBuilder->getUrl(
            self::OPS_CONTROLLER_ROUTE_PAYMENT . 'placeform',
            ['_secure' => true, '_nosid' => true]
        );
    }

    /**
     * Return 3D Secure url to redirect after confirming the order
     *
     * @return string
     */
    public function get3dSecureRedirectUrl()
    {
        return $this->urlBuilder->getUrl(
            self::OPS_CONTROLLER_ROUTE_PAYMENT . 'placeform3dsecure',
            ['_secure' => true, '_nosid' => true]
        );
    }

    public function getGenerateHashUrl($storeId = null, $admin = false)
    {
        $params = [
            '_secure' => $this->isCurrentlySecure(),
            '_nosid' => true
        ];
        if (null !== $storeId) {
            $params['_scope'] = $storeId;
        }

        if ($this->appState->getAreaCode() === 'adminhtml') {
            $params['_nosecret'] = true;

            return $this->backendUrlInterfaceFactory->create()->getUrl('adminhtml/alias/generatehash', $params);
        } else {
            return $this->urlBuilder->getUrl(self::OPS_CONTROLLER_ROUTE_ALIAS . 'generatehash', $params);
        }
    }

    public function getRegisterDirectDebitPaymentUrl()
    {
        return $this->urlBuilder->getUrl(
            self::OPS_CONTROLLER_ROUTE_PAYMENT . 'registerDirectDebitPayment',
            ['_secure' => $this->isCurrentlySecure(), '_nosid' => true]
        );
    }

    /**
     * Checks if requests should be logged or not regarding configuration
     *
     * @return boolean
     */
    public function shouldLogRequests($storeId = null)
    {
        return $this->getConfigData('debug_flag', $storeId);
    }

    public function hasCatalogUrl()
    {
        return $this->scopeConfig
            ->getValue('payment_services/ops/showcatalogbutton', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function hasHomeUrl()
    {
        return $this->scopeConfig
            ->getValue('payment_services/ops/showhomebutton', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getAcceptedCcTypes($code)
    {
        return $this->scopeConfig
            ->getValue('payment/' . $code . '/types', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Returns the cc types for which inline payments are activated
     *
     * @param string $code
     *
     * @return string[]
     */
    public function getInlinePaymentCcTypes($code)
    {
        $redirectAll = (bool)(int)$this->scopeConfig
            ->getValue('payment/' . $code . '/redirect_all', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($redirectAll) {
            return [];
        }

        $inlineTypes = $this->scopeConfig
            ->getValue('payment/' . $code . '/inline_types', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (false == is_array($inlineTypes)) {
            $inlineTypes = explode(',', $inlineTypes);
        }

        return $inlineTypes;
    }

    public function get3dSecureIsActive($methodCode)
    {
        return $this->scopeConfig
            ->getValue('payment/' . $methodCode . '/enabled_3dsecure', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getDirectDebitCountryIds()
    {
        return $this->scopeConfig
            ->getValue('payment/ops_directDebit/countryIds', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getBankTransferCountryIds()
    {
        return $this->scopeConfig
            ->getValue('payment/ops_bankTransfer/countryIds', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getDirectEbankingBrands()
    {
        return $this->scopeConfig
            ->getValue('payment/ops_directEbanking/brands', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Returns the generated alias (hosted tokenization) url or the special url if needed by vendor
     *
     * @param null $storeId
     *
     * @return mixed|Simple_Xml|string
     */
    public function getAliasGatewayUrl($storeId = null)
    {
        $url = $this->determineOpsUrl('ops_alias_gateway', $storeId);

        if ($this->getConfigData('ops_alias_gateway_test') != '') {
            if ($this->getMode($storeId) == \Netresearch\OPS\Model\Source\Mode::TEST) {
                return $this->getConfigData('ops_alias_gateway_test');
            } elseif ($this->getMode($storeId) == \Netresearch\OPS\Model\Source\Mode::PROD) {
                $url = str_replace('ncol/prod/', '', $url);
            }
        }

        return $url;
    }

    public function getCcSaveAliasUrl($storeId = null, $admin = false)
    {
        $params = [
            '_secure' => $this->isCurrentlySecure()
        ];
        if (null !== $storeId) {
            $params['_scope'] = $storeId;
        }
        if ($admin) {
            return $this->backendUrlInterfaceFactory->create()->getUrl('ops/admin/saveAlias', $params);
        } else {
            return $this->urlBuilder->getUrl('ops/alias/save', $params);
        }
    }

    /**
     * get deeplink to transaction view at Ingenico ePayments
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     *
     * @return string
     */
    public function getOpsAdminPaymentUrl($payment)
    {
        return '';
    }

    /**
     * @return mixed
     */
    public function isCurrentlySecure()
    {
        return $this->storeManager->getStore()->isCurrentlySecure();
    }

    public function getIntersolveBrands($storeId = null)
    {
        $result = [];
        $brands = $this->scopeConfig
            ->getValue('payment/ops_interSolve/brands', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        if (null !== $brands) {
            $result = unserialize($brands);
        }

        return $result;
    }

    /**
     * @param int $storeId
     *
     * @return string[][]
     */
    public function getFlexMethods($storeId = null)
    {
        $result = [];
        $methods = $this->scopeConfig
            ->getValue('payment/ops_flex/methods', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        if (null !== $methods) {
            $result = unserialize($methods);
        }

        return $result;
    }

    public function getAllCcTypes()
    {
        return $this->getCardTypes('ops_cc');
    }

    public function getAllDcTypes()
    {
        return $this->getCardTypes('ops_dc');
    }

    public function getCardTypes($methodCode)
    {
        return explode(',', $this->scopeConfig
            ->getValue('payment/' . $methodCode . '/availableTypes', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    /**
     * get keys of parameters to be shown in scoring information block
     *
     * @return array
     */
    public function getAdditionalScoringKeys()
    {
        return [
            'AAVCHECK',
            'CVCCHECK',
            'CCCTY',
            'IPCTY',
            'NBREMAILUSAGE',
            'NBRIPUSAGE',
            'NBRIPUSAGE_ALLTX',
            'NBRUSAGE',
            'VC',
            'CARDNO',
            'ED',
            'CN'
        ];
    }

    public function getSendInvoice()
    {
        return (bool)(int)$this->scopeConfig
            ->getValue('payment_services/ops/send_invoice', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * if payment method with given code is enabled for backend payments
     *
     * @param string $code Payment method code
     *
     * @return bool
     */
    public function isEnabledForBackend($code, $storeId = 0)
    {
        return (bool)(int)$this->scopeConfig
            ->getValue(
                'payment/' . $code . '/backend_enabled',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
    }

    public function isAliasInfoBlockEnabled()
    {
        return (bool)(int)$this->scopeConfig
            ->getValue(
                'payment/ops_cc/show_alias_manager_info_for_guests',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }

    /**
     * return config value for Alias Manager enabled
     *
     * @param $code
     * @param null $storeId
     *
     * @return bool
     */
    public function isAliasManagerEnabled($code, $storeId = null)
    {
        return (bool)$this->scopeConfig
            ->getValue(
                'payment/' . $code . '/active_alias',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
    }


    //TODO: remove this if not needed anymore
    /**
     * getter for usage of order reference
     */
    public function getOrderReference($storeId = null)
    {
        return $this->getConfigData('redirectOrderReference', $storeId);
    }

    /**
     * @param int $storeId - the store id to use
     *
     * @return int whether the QuoteId should be shown in
     * the order grid (1) or not (0)
     */
    public function getShowQuoteIdInOrderGrid($storeId = null)
    {
        return $this->getConfigData('showQuoteIdInOrderGrid', $storeId);
    }

    /**
     * Check if the current environment is frontend or backend
     *
     * @return boolean
     */
    public function isFrontendEnvironment()
    {
        return (false === $this->isBackendCurrentStore());
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function isBackendCurrentStore()
    {
        return $this->appState->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE;
    }

    /**
     * getter for the accept route for payments
     *
     * @return string
     */
    public function getAcceptRedirectRoute()
    {
        return self::OPS_CONTROLLER_ROUTE_PAYMENT . 'accept';
    }

    /**
     * getter for the cancel route for payments
     *
     * @return string
     */
    public function getCancelRedirectRoute()
    {
        return self::OPS_CONTROLLER_ROUTE_PAYMENT . 'cancel';
    }

    /**
     * getter for the decline route for payments
     *
     * @return string
     */
    public function getDeclineRedirectRoute()
    {
        return self::OPS_CONTROLLER_ROUTE_PAYMENT . 'decline';
    }

    /**
     * getter for the decline route for payments
     *
     * @return string
     */
    public function getExceptionRedirectRoute()
    {
        return self::OPS_CONTROLLER_ROUTE_PAYMENT . 'exception';
    }

    public function getMethodsRequiringAdditionalParametersFor($operation)
    {
        return $this->getConfigData('additional_params_required/' . $operation);
    }

    /**
     * returns the url for the maintenance api calls
     *
     * @param null $storeId
     *
     * @return string - the url for the maintenance api calls
     */
    public function getDirectLinkMaintenanceApiPath($storeId = null)
    {
        return $this->determineOpsUrl('directlink_maintenance_api', $storeId);
    }

    /**
     * getter for the iDeal issuers
     *
     * @return array
     */
    public function getIDealIssuers()
    {
        return $this->scopeConfig
            ->getValue('payment/ops_iDeal/issuer', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * whether extra parameters needs to be passed to Ingenico ePayments or not
     *
     * @param null $storeId
     *
     * @return bool - true if it's enabled, false otherwise
     */
    public function canSubmitExtraParameter($storeId = null)
    {
        return (bool)$this->getConfigData('submitExtraParameters', $storeId);
    }

    public function getParameterLengths()
    {
        return $this->getConfigData('paramLength');
    }

    public function getFrontendFieldMapping()
    {
        return $this->getConfigData('frontendFieldMapping');
    }

    public function getValidationUrl()
    {
        return $this->urlBuilder->getUrl(
            self::OPS_CONTROLLER_ROUTE_PAYMENT . 'validate',
            ['_secure' => $this->isCurrentlySecure(), '_nosid' => true]
        );
    }

    //TODO: remove this if not needed anymore
    public function getInlineOrderReference($storeId = null)
    {
        return $this->getConfigData('inlineOrderReference', $storeId);
    }

    /**
     * Returns the mode of the store
     *
     * @param null $storeId
     *
     * @return string | mode (custom, prod, test) for the store
     * @see \Netresearch\OPS\Model\Source\Mode
     */
    public function getMode($storeId = null)
    {
        return $this->getConfigData('mode', $storeId);
    }

    protected function getOpsUrl($path)
    {
        return $this->getConfigData('url/' . $path);
    }

    /**
     * Will always return the base url (https://secure.domain.tld/ncol/[test, prod]) for the mode of the store
     *
     * @return string Url depending of the mode - will be empty for custom mode
     */
    public function getOpsBaseUrl($storeId = null)
    {
        return $this->getOpsUrl('base_' . $this->getMode($storeId));
    }

    /**
     * Returns the default url for the given gateway, depending on the mode, that is set for the the given store
     *
     * @param string $path
     * @param int    $storeId
     *
     * @return string
     */
    public function getDefaultOpsUrl($path, $storeId = null)
    {
        return $this->getOpsBaseUrl($storeId) . $this->getOpsUrl($path);
    }

    /**
     * Returns the url for the given gateway depending on the set mode for the given store
     *
     * @param      $path
     * @param null $storeId
     *
     * @return string
     */
    public function determineOpsUrl($path, $storeId = null)
    {
        if ($this->getMode($storeId) === \Netresearch\OPS\Model\Source\Mode::CUSTOM) {
            return $this->getConfigData($path, $storeId);
        } else {
            return $this->getDefaultOpsUrl($path, $storeId);
        }
    }

    public function getTemplateIdentifier($storeId = null)
    {
        return $this->getConfigData('template_identifier', $storeId);
    }

    public function getResendPaymentInfoIdentity($storeId = null)
    {
        return $this->getConfigData('resendPaymentInfo_identity', $storeId);
    }

    public function getResendPaymentInfoTemplate($storeId = null)
    {
        return $this->getConfigData('resendPaymentInfo_template', $storeId);
    }

    public function getPayPerMailTemplate($storeId = null)
    {
        return $this->getConfigData('payPerMail_template', $storeId);
    }

    public function getStateRestriction()
    {
        return $this->getConfigData('ops_state_restriction');
    }

    /**
     * Will return the state of the deviceFingerPrinting:
     * - true if activated in config
     * - false if deactivated in config
     *
     * @param int $storeId
     *
     * @return bool
     */
    public function getDeviceFingerPrinting($storeId = null)
    {
        return (bool)$this->getConfigData('device_fingerprinting', $storeId);
    }

    public function getTransActionTimeout($storeId = null)
    {
        return (int)$this->getConfigData('ops_rtimeout', $storeId);
    }

    /**
     *
     * @param int|null $storeId
     *
     * @return bool
     */
    public function getCreditDebitSplit($storeId = null)
    {
        return (bool)$this->getConfigData('creditdebit_split', $storeId);
    }

    public function getAllRecurringCcTypes()
    {
        return explode(',', $this->scopeConfig
            ->getValue('payment/ops_recurring_cc/availableTypes', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    public function getAcceptedRecurringCcTypes()
    {
        return explode(',', $this->scopeConfig
            ->getValue('payment/ops_recurring_cc/acceptedTypes', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    public function getMonthlyBillingDay($storeId = null)
    {
        return $this->scopeConfig
            ->getValue(
                self::OPS_PAYMENT_PATH . 'billing_day_month',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
    }

    public function getWeeklyBillingDay($storeId = null)
    {
        return $this->scopeConfig
            ->getValue(
                self::OPS_PAYMENT_PATH . 'billing_day_week',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
    }

    public function getSuspendSubscriptionTemplate($storeId = null)
    {
        return $this->getConfigData('suspendSubscription_template', $storeId);
    }

    public function getSuspendSubscriptionIdentity($storeId = null)
    {
        return $this->getConfigData('suspendSubscription_identity', $storeId);
    }

    /**
     * @return string
     */
    public function getConsentUrl()
    {
        return $this->urlBuilder->getUrl('ops/consent');
    }

    public function getCustomerAliasListUrl()
    {
        return $this->urlBuilder->getUrl('ops/customer/aliasList');
    }

    /**
     * @return string
     */
    public function getRedirectMessage()
    {
        return 'You will be redirected to finalize your payment.';
    }

    /**
     * return configured text for alias usage parameter for new alias creation
     *
     * @param $code
     * @param null $storeId
     * @return string
     */
    public function getAliasUsageForNewAlias($code, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            'payment/' . $code . '/alias_usage_for_new_alias',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * return configured text for alias usage parameter when using a existing alias
     *
     * @param $code
     * @param null $storeId
     *
     * @return string
     */
    public function getAliasUsageForExistingAlias($code, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            'payment/' . $code . '/alias_usage_for_existing_alias',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param array $params
     * @param null $storeId
     * @return string
     */
    public function getPaymentRetryUrl(array $params, $storeId = null)
    {
        return $this->urlBuilder->getUrl(
            null,
            ['_secure' => true, '_nosid' => true, '_query' => $params, '_scope' => $storeId,
             '_direct' => self::OPS_CONTROLLER_ROUTE_PAYMENT . 'retry']
        );
    }

    /**
     * @param string $code
     * @param int|null $storeId
     * @return mixed
     */
    public function getHtpTemplateName($code, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            'payment/' . $code . '/template_name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}

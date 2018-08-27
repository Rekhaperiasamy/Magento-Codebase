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

namespace Netresearch\OPS\Block;

class Form extends \Magento\Payment\Block\Form\Cc
{
    protected $pmLogo = null;

    protected $fieldMapping = [];

    protected $config = null;

    /**
     * Frontend Payment Template
     */
    const FRONTEND_TEMPLATE = 'Netresearch_OPS::ops/form.phtml';

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    protected $oPSConfig;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    /**
     * @var \Netresearch\OPS\Helper\Alias
     */
    protected $oPSAliasHelper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Form constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Netresearch\OPS\Model\Config $oPSConfig
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     * @param \Netresearch\OPS\Helper\Alias $oPSAliasHelper
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
        array $data = []
    ) {
        parent::__construct($context, $paymentConfig, $data);
        $this->checkoutSession = $checkoutSession;
        $this->jsonEncoder = $jsonEncoder;
        $this->customerSession = $customerSession;
        $this->oPSConfig = $oPSConfig;
        $this->oPSHelper = $oPSHelper;
        $this->oPSAliasHelper = $oPSAliasHelper;
    }

    /**
     * Init OPS payment form
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate(self::FRONTEND_TEMPLATE);
    }

    /**
     * get OPS config
     *
     * @return \Netresearch\Ops\Model\Config
     */
    public function getConfig()
    {
        if (null === $this->config) {
            $this->config =  $this->oPSConfig;
        }

        return $this->config;
    }

    /**
     * @param \Netresearch\OPS\Model\Config $config
     * @return $this
     */
    public function setConfig(\Netresearch\OPS\Model\Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    public function getCcBrands()
    {
        return explode(',', $this->getConfig()->getAcceptedCcTypes());
    }

    /**
     * @return array
     */
    public function getDirectDebitCountryIds()
    {
        return explode(',', $this->getConfig()->getDirectDebitCountryIds());
    }

    public function getBankTransferCountryIds()
    {
        return explode(',', $this->getConfig()->getBankTransferCountryIds());
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getPSPID($storeId = null)
    {
        return $this->oPSConfig->getPSPID($storeId);
    }

    /**
     * @param null $storeId
     * @param bool $admin
     * @return string
     */
    public function getGenerateHashUrl($storeId = null)
    {
        return $this->oPSConfig->getGenerateHashUrl($storeId, true);
    }

    /**
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->oPSConfig->getValidationUrl();
    }

    /**
     * @return array
     */
    public function getDirectEbankingBrands()
    {
        return explode(',', $this->getConfig()->getDirectEbankingBrands());
    }

    /**
     * wrapper for \Netresearch\OPS\Helper\Data::checkIfUserRegistering
     *
     * @return type bool
     */
    public function isUserRegistering()
    {
        return $this->oPSHelper->checkIfUserIsRegistering();
    }

    /**
     * wrapper for \Netresearch\OPS\Helper\Data::checkIfUserRegistering
     *
     * @return type bool
     */
    public function isUserNotRegistering()
    {
        return $this->oPSHelper->checkIfUserIsNotRegistering();
    }

    /**
     * @return string
     */
    public function getPmLogo()
    {
        return $this->pmLogo;
    }

    /**
     * @return mixed
     */
    protected function getFieldMapping()
    {
        return $this->getConfig()->getFrontendFieldMapping();
    }

    /**
     * returns the corresponding fields for frontend validation if needed
     *
     * @return string - the json encoded fields
     */
    public function getFrontendValidators()
    {
        $frontendFields = [];
        if ($this->getConfig()->canSubmitExtraParameter($this->getQuote()->getStoreId())) {
            $fieldsToValidate = $this->getConfig()->getParameterLengths();
            $mappedFields = $this->getFieldMapping();
            foreach ($fieldsToValidate as $key => $value) {
                if (array_key_exists($key, $mappedFields)) {
                    $frontendFields = $this->oPSHelper
                        ->getFrontendValidationFields($mappedFields, $key, $value, $frontendFields);
                }
            }
        }

        return $this->jsonEncoder->encode($frontendFields);
    }

    /**
     * @param $mappedFields
     * @param $key
     * @param $value
     * @param $frontendFields
     *
     * @return mixed
     *
     * @deprecated use \Netresearch\OPS\Helper\Data::getFrontendValidationFields instead
     */
    public function getFrontendValidationFields($mappedFields, $key, $value, $frontendFields)
    {
        return $this->oPSHelper->getFrontendValidationFields($mappedFields, $key, $value, $frontendFields);
    }

    public function getImageForBrand($brand)
    {
        $brandName = str_replace(' ', '', $brand);
        return $this->getViewFileUrl('Netresearch_OPS::images/ops/alias/brands/'. $brandName .'.png');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getRedirectMessage()
    {
        return __($this->oPSConfig->getRedirectMessage());
    }
}

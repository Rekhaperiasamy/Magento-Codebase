<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Scommerce\Gdpr\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ENABLED                 = 'scommerce_gdpr/general/enable';
    const LICENSE_KEY             = 'scommerce_gdpr/general/license_key';
    const DELETE_FRONTEND         = 'scommerce_gdpr/general/delete_enabled_on_frontend';
    const ATTENTION_MESSAGE       = 'scommerce_gdpr/general/attentionmessage';
    const SUCCESS_MESSAGE         = 'scommerce_gdpr/general/successmessage';
    const EMAIL_SENDER			  = 'scommerce_gdpr/general/sender_email_identity';
    const EMAIL_CONFIRMATION      = 'scommerce_gdpr/general/confirmation_email_template';
    const EMAIL_DELETION          = 'scommerce_gdpr/general/delete_confirmation_email_template';
    const COOKIE_ENABLED          = 'scommerce_gdpr/general/enable_cookie';
    const BLOCKED                 = 'scommerce_gdpr/general/blocked';
    const CSS_PAGE_WRAPPER_CLASS  = 'scommerce_gdpr/general/page_wrapper_class';
    const COOKIE_MESSAGE          = 'scommerce_gdpr/general/cookie_message';
    const CMS_PAGE                = 'scommerce_gdpr/general/cms_page';
    const COOKIE_LINK_TEXT        = 'scommerce_gdpr/general/cookie_link_text';
    const COOKIE_TEXT_COLOR       = 'scommerce_gdpr/general/cookie_text_color';
    const COOKIE_LINK_COLOR       = 'scommerce_gdpr/general/cookie_link_color';
    const COOKIE_BACKGROUND_COLOR = 'scommerce_gdpr/general/cookie_background_color';
    const COOKIE_MESSAGE_POSITION = 'scommerce_gdpr/general/cookie_message_position';
	
	const QUOTE_EXPIRE            = 'scommerce_gdpr/orderquote/quote_expire';
	const ORDER_EXPIRE            = 'scommerce_gdpr/orderquote/order_expire';
	const ORDER_CHUNK  	          = 'scommerce_gdpr/orderquote/order_chunk';
	const DEBUGGING		          = 'scommerce_gdpr/orderquote/debugging';

    const PRIVACY_ENABLE              = 'scommerce_gdpr/privacy/enable';
    const PRIVACY_SETTING_TEXT        = 'scommerce_gdpr/privacy/setting_text';
    const PRIVACY_ENABLE_NEWSLETTER   = 'scommerce_gdpr/privacy/enable_newsletter';
    const PRIVACY_ENABLE_CONTACT_US   = 'scommerce_gdpr/privacy/enable_contact_us';
    const PRIVACY_ENABLE_CHECKOUT     = 'scommerce_gdpr/privacy/enable_checkout';
    const PRIVACY_ENABLE_REGISTRATION = 'scommerce_gdpr/privacy/enable_registration';
	
	const LOGFILE								= "/var/log/anonymisation.log";

    /** @var \Magento\Customer\Model\CustomerFactory */
    private $customerFactory;

    /** @var \Scommerce\Gdpr\Helper\Serializer */
    private $serializer;

    /** @var \Scommerce\Gdpr\Helper\Email */
    private $email;

    /** @var \Scommerce\Gdpr\Helper\Anonymize */
    private $anonymize;

    /** @var \Scommerce\Gdpr\Helper\Source */
    private $source;

    /* @var \Scommerce\Core\Helper\Data */
    private $coreHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Scommerce\Gdpr\Helper\Serializer $serializer
     * @param \Scommerce\Gdpr\Helper\Email $email
     * @param \Scommerce\Gdpr\Helper\Anonymize $anonymize
     * @param \Scommerce\Gdpr\Helper\Source $source
     * @param \Scommerce\Core\Helper\Data $coreHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Scommerce\Gdpr\Helper\Serializer $serializer,
        \Scommerce\Gdpr\Helper\Email $email,
        \Scommerce\Gdpr\Helper\Anonymize $anonymize,
        \Scommerce\Gdpr\Helper\Source $source,
        \Scommerce\Core\Helper\Data $coreHelper
    ) {
        parent::__construct($context);
        $this->customerFactory = $customerFactory;
        $this->serializer = $serializer;
        $this->email = $email;
        $this->anonymize = $anonymize;
        $this->source = $source;
        $this->coreHelper = $coreHelper;
    }

    /**
     * Returns whether module is enabled or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        $enabled = $this->isSetFlag(self::ENABLED);
        return $this->isCliMode() ? $enabled : $enabled && $this->isLicenseValid();
    }

    /**
     * Is deletion enabled on frontend
     *
     * @return bool
     */
    public function isDeletionEnabledOnFrontend()
    {
        return $this->isSetFlag(self::DELETE_FRONTEND);
    }

    /**
     * Get attention message
     *
     * @return string
     */
    public function getAttentionMessage()
    {
        return $this->getValue(self::ATTENTION_MESSAGE);
    }

    /**
     * Get success message
     *
     * @return string
     */
    public function getSuccessMessage()
    {
        return $this->getValue(self::SUCCESS_MESSAGE);
    }

    /**
     * Get identifier of email sender
     *
     * @return string
     */
    public function getEmailSender()
    {
        return $this->getValue(self::EMAIL_SENDER);
    }

    /**
     * Get identifier of confirmation email template
     *
     * @return string
     */
    public function getConfirmationEmailTemplate()
    {
        return $this->getValue(self::EMAIL_CONFIRMATION);
    }

    /**
     * Get identifier of deletion email template
     *
     * @return string
     */
    public function getDeletionEmailTemplate()
    {
        return $this->getValue(self::EMAIL_DELETION);
    }

    /**
     * Get number of days to set personal data to NULL in quote and quote_address tables
     *
     * @return int
     */
    public function getQuoteExpire()
    {
        return $this->getValue(self::QUOTE_EXPIRE);
    }
		
	 /**
     * Get number of days to anonymise order related tables
     *
     * @return int
     */
    public function getOrderExpire()
    {
        return $this->getValue(self::ORDER_EXPIRE);
    }
	
	 /**
     * Get number of records which needs to be anonymised when cron job runs
     *
     * @return int
     */
    public function getOrderChunk()
    {
        return $this->getValue(self::ORDER_CHUNK);
    }
	
	/**
     * Is debugging enabled for order and quote anonymisation
     *
     * @return bool
     */
    public function isDebuggingEnabled()
    {
        return (bool) $this->getValue(self::DEBUGGING);
    }
		
	/**
     * log data enabled
     * @param $data|string
	 *
     * @return void
     */
    public function logData($data)
    {
		if ($this->isDebuggingEnabled()) {
			$writer = new \Zend\Log\Writer\Stream(BP . self::LOGFILE);
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($data);
		}
    }

    /**
     * Return if cookie message is enabled
     *
     * @return bool
     */
    public function isCookieEnabled()
    {
        return $this->isSetFlag(self::COOKIE_ENABLED);
    }

    /**
     * Return if blocked access to site until cookie policy is accepted
     *
     * @return bool
     */
    public function isBlocked()
    {
        return $this->isSetFlag(self::BLOCKED);
    }

    /**
     * Return css class of page wrapper
     *
     * @return string
     */
    public function getCssPageWrapperClass()
    {
        return $this->getValue(self::CSS_PAGE_WRAPPER_CLASS);
    }

    /**
     * Return cookie text message
     *
     * @return string
     */
    public function getCookieTextMessage()
    {
        return $this->getValue(self::COOKIE_MESSAGE);
    }

    /**
     * Get front url for cms page used as Information Page about cookie policy
     *
     * @return string
     */
    public function getCmsPageUrl()
    {
        return $this->_getUrl($this->getValue(self::CMS_PAGE), ['_current' => true]);
    }

    /**
     * Return coolie link text
     *
     * @return string
     */
    public function getCookieLinkText()
    {
        return $this->getValue(self::COOKIE_LINK_TEXT);
    }

    /**
     * Return cookie text color hex-code
     *
     * @return string
     */
    public function getCookieTextColor()
    {
        return $this->getValue(self::COOKIE_TEXT_COLOR);
    }

    /**
     * Return cookie link color hex-code
     *
     * @return string
     */
    public function getCookieLinkColor()
    {
        return $this->getValue(self::COOKIE_LINK_COLOR);
    }

    /**
     * Return cookie text background color hex-code
     *
     * @return string
     */
    public function getCookieBackgroundColor()
    {
        return $this->getValue(self::COOKIE_BACKGROUND_COLOR);
    }

    /**
     * Return cookie message position (top/bottom)
     *
     * @return int
     */
    public function getCookieMessagePosition()
    {
        return $this->getValue(self::COOKIE_MESSAGE_POSITION);
    }

    /**
     * Is message position set to bottom ?
     *
     * @return bool
     */
    public function isCookieMessagePositionBottom()
    {
        return (bool) $this->getCookieMessagePosition();
    }
	
	/**
     * Get cookie key to check if cookie message was closed
     *
     * @return string
     */
    public function getCookieClosedKey()
    {
        return 'cookie_closed';
    }

    /**
     * Is privacy setting enabled
     *
     * @return bool
     */
    public function isPrivacyEnabled()
    {
        return $this->isSetFlag(self::PRIVACY_ENABLE);
    }

    /**
     * Get privacy setting text
     *
     * @return string
     */
    public function getPrivacySettingText()
    {
        return $this->getValue(self::PRIVACY_SETTING_TEXT);
    }

    /**
     * Is privacy newsletter enabled
     *
     * @return bool
     */
    public function isPrivacyNewsletterEnabled()
    {
        return $this->isSetFlag(self::PRIVACY_ENABLE_NEWSLETTER);
    }

    /**
     * Is privacy contact us enabled
     *
     * @return bool
     */
    public function isPrivacyContactUsEnabled()
    {
        return $this->isSetFlag(self::PRIVACY_ENABLE_CONTACT_US);
    }

    /**
     * Get privacy source helper
     *
     * @return Source
     */
    public function getPrivacySourceHelper()
    {
        return $this->source;
    }

    /**
     * Is privacy checkout enabled
     *
     * @return bool
     */
    public function isPrivacyCheckoutEnabled()
    {
        return $this->isSetFlag(self::PRIVACY_ENABLE_CHECKOUT);
    }

    /**
     * Is privacy registration enabled
     *
     * @return bool
     */
    public function isPrivacyRegistrationEnabled()
    {
        return $this->isSetFlag(self::PRIVACY_ENABLE_REGISTRATION);
    }

    /**
     * Get front url for delete customer
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->_getUrl('scommerce_gdpr/customer/delete', ['_current' => true]);
    }

    /**
     * Send any email to customer
     *
     * @param string $template Email template identifier
     * @param \Magento\Customer\Api\Data\CustomerInterface|\Magento\Customer\Model\Data\Customer $customer
     * @param array $data Template variables
     * @throws \Exception
     */
    public function sendEmail($template, $customer, $data = [])
    {
        $customerModel = $this->createCustomer()->updateData($customer);
        $customerData = ['email' => $customer->getEmail(), 'name' => $customerModel->getName()];
        $this->email->send($template, $this->getEmailSender(), $customerData, $data);
    }

    /**
     * Send confirmation email to customer
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface|\Magento\Customer\Model\Data\Customer $customer
     * @throws \Exception
     */
    public function sendConfirmationEmail($customer)
    {
        $this->sendEmail($this->getConfirmationEmailTemplate(), $customer, ['link' => $this->getDeleteUrl()]);
    }

    /**
     * Send deletion email to customer
     *
     * @param \\Magento\Customer\Api\Data\CustomerInterface|Magento\Customer\Model\Data\Customer $customer
     * @throws \Exception
     */
    public function sendDeletionEmail($customer)
    {
        $this->sendEmail($this->getDeletionEmailTemplate(), $customer);
    }

    /**
     * Serialize data depending Magento version
     *
     * @param mixed $data
     * @return string
     */
    public function serialize($data)
    {
        return $this->serializer->serialize($data);
    }

    /**
     * Unserialize data depending Magento version
     *
     * @param string $data
     * @return mixed
     */
    public function unserialize($data)
    {
        return $this->serializer->unserialize($data);
    }

    /**
     * Get anonymize helper
     *
     * @return \Scommerce\Gdpr\Helper\Anonymize
     */
    public function getAnonymize()
    {
        return $this->anonymize;
    }

    /**
     * Helper method for create new customer model instance
     *
     * @return \Magento\Customer\Model\Customer
     */
    private function createCustomer()
    {
        return $this->customerFactory->create();
    }

    /**
     * Returns whether license key is valid or not
     *
     * @return bool
     */
    private function isLicenseValid()
    {
        $sku = strtolower(str_replace('\\Helper\\Data','',str_replace('Scommerce\\','',get_class($this))));
        return $this->coreHelper->isLicenseValid($this->getLicenseKey(), $sku);
    }

    /**
     * Returns license key administration configuration option
     *
     * @return string
     */
    private function getLicenseKey()
    {
        return $this->getValue(self::LICENSE_KEY);
    }

    /**
     * Helper method for retrieve config value by path and scope
     *
     * @param string $path The path through the tree of configuration values, e.g., 'general/store_information/name'
     * @param string $scopeType The scope to use to determine config value, e.g., 'store' or 'default'
     * @param null|string $scopeCode
     * @return mixed
     */
    private function getValue($path, $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * Helper method for retrieve config flag by path and scope
     *
     * @param string $path The path through the tree of configuration values, e.g., 'general/store_information/name'
     * @param string $scopeType The scope to use to determine config value, e.g., 'store' or 'default'
     * @param null|string $scopeCode
     * @return bool
     */
    private function isSetFlag($path, $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag($path, $scopeType, $scopeCode);
    }

    /**
     * Check if running in cli mode
     *
     * @return bool
     */
    private function isCliMode()
    {
        return php_sapi_name() === 'cli';
    }
}

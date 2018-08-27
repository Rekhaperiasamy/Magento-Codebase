<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Contact
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Contact\Block;

use Magento\Framework\View\Element\Template;

/**
 * Main contact form block
 */
class ContactForm extends \Magento\Contact\Block\ContactForm
{

    /**
     * topics config path
     */
    const XML_PATH_TOPICS = 'contact/contact/topics';

    /**
     * privacy policy url key
     */
    const PRIVACY_POLICY_PAGE_URL_KEY = 'terms-conditions';

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * ContactForm constructor.
     *
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleManager = $moduleManager;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * Newsletter module availability
     *
     * @return bool
     */
    public function isNewsletterEnabled()
    {
        return $this->moduleManager->isOutputEnabled('Magento_Newsletter');
    }

    /**
     * returns the scope config topics converted to an array
     *
     * @return array
     */
    public function getTopics()
    {
        $topics = [];
        $topicsConfig = $this->scopeConfig->getValue(
            self::XML_PATH_TOPICS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!empty($topicsConfig)) {
            foreach (explode(PHP_EOL, $topicsConfig) as $topic) {
                if ($topic = trim($topic)) {
                    $topics[$topic] = $topic;
                }
            }
        }

        return $topics;
    }

    /**
     * privacy policy url
     *
     * @return string
     */
    public function getPrivacyUrl()
    {
        return $this->getUrl(self::PRIVACY_POLICY_PAGE_URL_KEY);
    }
}

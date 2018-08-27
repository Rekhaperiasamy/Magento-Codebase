<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_AutomatedCustomerGroup
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\AutomatedCustomerGroup\Cron;

class UpgradeCustomerGroupDaily
{
    /**
     * xml path for module active
     */
    const XML_PATH_ACTIVE = 'automated_customer_group/notification/active';

    /**
     * xml path for secrue base url
     */
    const XML_PATH_SECURE_URL = 'web/secure/base_url';

    /**
     * Number of reward points customer needs to upgrade himself tp "TEA CONNSOUIRE"
     */
    const CUSTOMER_UPGRADE_POINTS = 'automated_customer_group/notification/upgrade_points';

    /**
     * Email sender config path
     */
    const XML_PATH_EMAIL_SENDER = 'automated_customer_group/notification/sender_email_identity';

    /**
     * Email template config path
     */
    const XML_PATH_EMAIL_TEMPLATE = 'automated_customer_group/notification/email_template';

    /**
     * Reward history collection
     *
     * @var \Magento\Reward\Model\ResourceModel\Reward\History\CollectionFactory
     */
    protected $automatedCustomer;

    /**
     * Email Transpoter
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;
    /**
     * Store Manager to get store information
     * @var \Magento\Store\Model\StoreManagerInterface
     */

    protected $storeManager;

    /**
     * Scope configurations
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var State
     */
    protected $state;

    /**
     * Website factory
     *
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * UpgradeCustomerGroupDaily constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Dilmah\AutomatedCustomerGroup\Model\AutomatedCustomerGroup $automatedCustomerCollection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Dilmah\AutomatedCustomerGroup\Model\AutomatedCustomerGroup $automatedCustomerCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\State $state,
        \Magento\Store\Model\WebsiteFactory $websiteFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->automatedCustomer = $automatedCustomerCollection;
        $this->state = $state;
        $this->websiteFactory = $websiteFactory;
    }

    /**
     * Cron Execute method
     *
     * @return void
     * @throws \Zend_Validate_Exception
     */
    public function execute()
    {
        // setting the area code when the process runs via console command
        $this->state->setAreaCode('crontab');

        $pointsCount = $this->scopeConfig->getValue(self::CUSTOMER_UPGRADE_POINTS);
        $websiteScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;

        $senderList = $this->automatedCustomer->getCustomersEmailsByRewardPoints($pointsCount);

        if (count($senderList) > 0) {
            foreach ($senderList as $websiteId => $recipients) {
                $website = $this->websiteFactory->create()->load($websiteId);

                $active = $this->scopeConfig->getValue(self::XML_PATH_ACTIVE, $websiteScope, $website->getCode());

                if (!$active) { // if the functionality is disabled for the website
                    continue;
                }

                $from = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $websiteScope, $website->getCode());
                $template = $this->scopeConfig->getValue(
                    self::XML_PATH_EMAIL_TEMPLATE,
                    $websiteScope,
                    $website->getCode()
                );
                $storeUrl = $this->scopeConfig->getValue(
                    self::XML_PATH_SECURE_URL,
                    $websiteScope,
                    $website->getCode()
                );

                foreach ($recipients as $recipient) {
                    if ($this->automatedCustomer->updateCustomerGroup(
                        $recipient['entity_id'],
                        $recipient['email']
                    ) !== false
                    ) {

                        $templateData = [
                            'name' => $recipient['name'],
                            'store_url' => $storeUrl,
                        ];

                        if (\Zend_Validate::is($recipient['email'], 'EmailAddress')) {
                            $transport = $this->transportBuilder
                                ->setTemplateIdentifier($template)
                                ->setTemplateOptions(
                                    [
                                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                        'store' => $recipient['store_id'],
                                    ]
                                )
                                ->setTemplateVars($templateData)
                                ->setFrom($from)
                                ->addTo($recipient['email'])
                                ->getTransport();

                            $transport->sendMessage();
                        }
                    }
                }
            }
        }
    }
}

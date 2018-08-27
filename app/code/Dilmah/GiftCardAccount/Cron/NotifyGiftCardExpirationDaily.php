<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_GiftCardAccount
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\GiftCardAccount\Cron;

class NotifyGiftCardExpirationDaily
{

    /**
     * xml path for module active
     */
    const XML_PATH_ACTIVE = 'unused_giftcard_account/notification/active';

    /**
     * Sender email config path
     */
    const XML_PATH_EMAIL_SENDER = 'unused_giftcard_account/notification/sender_email_identity';

    /**
     * Email template config path
     */
    const XML_PATH_EMAIL_TEMPLATE = 'unused_giftcard_account/notification/email_template';

    /**
     * durations
     */
    protected $notificationDurations = ["+6 Months", "+1 Months", "+21 Days", "+14 Days", "+7 Days"];

    /**
     * @var \Dilmah\GiftCardAccount\Model\GiftCardExpiration
     */
    protected $giftcardExpiration;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
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
     * NotifyGiftCardExpirationDaily constructor
     *
     * @param \Dilmah\GiftCardAccount\Model\GiftCardExpiration $giftcardExpiration
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     */
    public function __construct(
        \Dilmah\GiftCardAccount\Model\GiftCardExpiration $giftcardExpiration,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Magento\Store\Model\WebsiteFactory $websiteFactory
    ) {
        $this->giftcardExpiration = $giftcardExpiration;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->localeCurrency = $localeCurrency;
        $this->state = $state;
        $this->websiteFactory = $websiteFactory;
    }


    /**
     * Cron Execution Method
     * Generates and send emails
     *
     * @return void
     */
    public function execute()
    {
        // setting the area code when the process runs via console command
        $this->state->setAreaCode('crontab');
        $websiteScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;

        $giftcardAccountList = $this->giftcardExpiration->getEmailsNearlyToExpired($this->notificationDurations);

        if (count($giftcardAccountList) > 0) {
            foreach ($giftcardAccountList as $websiteId => $giftcardAccounts) {
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

                foreach ($giftcardAccounts as $giftcardAccount) {

                    $balance = $this->localeCurrency->getCurrency(
                        $this->storeManager->getStore($giftcardAccount['store_id'])->getBaseCurrencyCode()
                    )->toCurrency(
                        $giftcardAccount['balance']
                    );

                    $templateData = [
                        'balance' => $balance,
                        'code' => $giftcardAccount['code'],
                        'date_expires' => $giftcardAccount['date_expires'],
                        'store_obj' => $this->storeManager->getStore($giftcardAccount['store_id']),
                    ];

                    if (\Zend_Validate::is($giftcardAccount['recipient_email'], 'EmailAddress')) {
                        $transport = $this->transportBuilder
                            ->setTemplateIdentifier($template)
                            ->setTemplateOptions(
                                [
                                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                    'store' => $giftcardAccount['store_id'],
                                ]
                            )
                            ->setTemplateVars($templateData)
                            ->setFrom($from)
                            ->addTo($giftcardAccount['recipient_email'])
                            ->getTransport();

                        $transport->sendMessage();
                    }
                }

            }
        }
    }
}

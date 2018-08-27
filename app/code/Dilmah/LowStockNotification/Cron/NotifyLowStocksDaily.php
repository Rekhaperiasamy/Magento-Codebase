<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_LowStockNotification
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\LowStockNotification\Cron;

class NotifyLowStocksDaily
{
    /**
     * xml path for module active
     */
    const XML_PATH_ACTIVE = 'low_stock/notification/active';

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'low_stock/notification/emails_field';

    /**
     * Sender email config path
     */
    const XML_PATH_EMAIL_SENDER = 'low_stock/notification/sender_email_identity';

    /**
     * Email template config path
     */
    const XML_PATH_EMAIL_TEMPLATE = 'low_stock/notification/email_template';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Dilmah\LowStockNotification\Model\ProductList
     */
    protected $lowStockModel;

    /**
     * Website factory
     *
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * @var State
     */
    protected $state;

    /**
     * Store factory
     *
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * NotifyLowStocksDaily constructor.
     *
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Dilmah\LowStockNotification\Model\ProductList $lowStockModel
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     */
    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Dilmah\LowStockNotification\Model\ProductList $lowStockModel,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Framework\App\State $state,
        \Magento\Store\Model\StoreFactory $storeFactory
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->lowStockModel = $lowStockModel;
        $this->websiteFactory = $websiteFactory;
        $this->state = $state;
        $this->storeFactory = $storeFactory;
    }

    /**
     * Cron Execution Method
     * Generates and send emails
     * @return void
     */
    public function execute()
    {

        $productData = $this->lowStockModel->getLowStockProducts();
        $websiteScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;

        foreach ($productData as $websiteId => $products) {
            $website = $this->websiteFactory->create()->load($websiteId);

            $active = $this->scopeConfig->getValue(self::XML_PATH_ACTIVE, $websiteScope, $website->getCode());

            if (!$active || count($products)==0) { // if the functionality is disabled for the website
                continue;
            }

            $recipients = $this->scopeConfig->getValue(
                self::XML_PATH_EMAIL_RECIPIENT,
                $websiteScope,
                $website->getCode()
            );
            $from = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $websiteScope, $website->getCode());
            $template = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $websiteScope, $website->getCode());

            $storeCollection = $this->storeFactory->create()->getCollection()
                ->addWebsiteFilter($websiteId);

            /** @var \Magento\Store\Model\Store $store */
            foreach ($storeCollection as $store) {
                if (!$store->isActive()) {
                    continue;
                }

                foreach (explode(',', $recipients) as $recipient) {
                    if (\Zend_Validate::is(trim($recipient), 'EmailAddress')) {
                        $transport = $this->transportBuilder
                            ->setTemplateIdentifier($template)
                            ->setTemplateOptions(
                                [
                                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                    'store' => $store->getId(),
                                ]
                            )
                            ->setTemplateVars(['products' => $products])
                            ->setFrom($from)
                            ->addTo(trim($recipient))
                            ->getTransport();

                        $transport->sendMessage();
                    }
                }
            }
        }
    }

    /**
     * Setting the area code when the process runs via console command
     * @param $areaCode
     */
    public function setAreaCode($areaCode)
    {
        $this->state->setAreaCode($areaCode);
    }
}

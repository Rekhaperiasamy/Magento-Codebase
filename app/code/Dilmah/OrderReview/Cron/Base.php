<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_OrderReview
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\OrderReview\Cron;

use Magento\Framework\App\Action\Context;

class Base
{
    /**
     * xml path for module enable
     */
    const XML_PATH_ENABLE = 'order_review/settings/enabled';

    /**
     * xml path for email identity
     */
    const XML_PATH_IDENTITY = 'order_review/settings/identity';

    /**
     * xml contact email from address
     */
    const XML_PATH_CONTACT_EMAIL_RECIPIENT_EMAIL = 'contact/email/recipient_email';

    /**
     * xml path for email template
     */
    const XML_PATH_TEMPLATE = 'order_review/settings/template';

    /**
     * xml path for no. days the emails should be delayed before sending
     */
    const XML_PATH_LAPSE = 'order_review/settings/lapse';

    /**
     * xml path for order status to count the review email lapse days
     */
    const XML_PATH_ORDER_STATUS = 'order_review/settings/order_status';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Dilmah\Erp\Logger\Logger
     */
    protected $logger;

    /**
     * Base constructor.
     * @param Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Dilmah\Erp\Logger\Logger $logger
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Dilmah\Erp\Logger\Logger $logger
    ) {
        $this->objectManager = $context->getObjectManager();
        $this->scopeConfig = $scopeConfig;
        $this->resource = $resource;
        $this->logger = $logger;

        $this->settings = [
            self::XML_PATH_ENABLE => $this->scopeConfig->getValue(
                self::XML_PATH_ENABLE,
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            ),
            self::XML_PATH_IDENTITY => $this->scopeConfig->getValue(
                self::XML_PATH_IDENTITY,
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            ),
            self::XML_PATH_TEMPLATE => $this->scopeConfig->getValue(
                self::XML_PATH_TEMPLATE,
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            ),
            self::XML_PATH_LAPSE => $this->scopeConfig->getValue(
                self::XML_PATH_LAPSE,
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            ),
            self::XML_PATH_ORDER_STATUS => 'processing',//$this->scopeConfig->getValue(self::XML_PATH_ORDER_STATUS),
        ];
    }

    /**
     * get model based on the order status selected from the store config
     *
     * @return mixed|null
     */
    protected function getOrderStatusModel()
    {
        $model = null;
        switch ($this->settings[self::XML_PATH_ORDER_STATUS]) {
            case 'new':
            case 'processing':
                $model = $this->objectManager->create('Magento\Sales\Model\ResourceModel\Order\CollectionFactory');
                break;
            case 'complete':
                $model = $this->objectManager->create(
                    '\Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory'
                );
                break;
        }
        return $model;
    }
}

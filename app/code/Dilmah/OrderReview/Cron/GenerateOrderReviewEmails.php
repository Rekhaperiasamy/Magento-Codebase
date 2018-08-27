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

/**
 * Class GenerateOrderReviewEmails
 * @package Dilmah\OrderReview\Cron
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GenerateOrderReviewEmails extends Base
{
    /**
     * column name of the sales order table which says order sent to the erp
     */
    const REVIEW_EMAIL_SENT = 'review_email_sent';

    /**
     * order status used to filter the order collection
     */
    const ORDER_STATUS_TO_FILTER_ORDERS = 'complete';

    /**
     * Store factory
     *
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $orderItemCollectionFactory;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Website factory
     *
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * GenerateOrderReviewEmails constructor.
     * @param Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\Sftp $sftpAdapter
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Dilmah\Erp\Logger\Logger $logger
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\Sftp $sftpAdapter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Dilmah\Erp\Logger\Logger $logger,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\WebsiteFactory $websiteFactory
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeFactory = $storeFactory;
        $this->websiteFactory = $websiteFactory;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        parent::__construct($context, $scopeConfig, $resource, $logger);
    }

    /**
     * Prepare order data and generate files and push to ftp for the usage of ERP
     *
     * @return void
     */
    public function execute()
    {
        $websiteScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
        $orderCollection = $this->getCollection();

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orderCollection as $order) {
            $itemData = $this->getItemsCollection($order);

            if (count($itemData) > 0) {
                $website = $this->websiteFactory->create()->load($order->getStore()->getWebsiteId());
                $active = $this->scopeConfig->getValue(self::XML_PATH_ENABLE, $websiteScope, $website->getCode());
                if (!$active) { // if the functionality is disabled for the website
                    continue;
                }
                $recipient = $order->getCustomerEmail();
                //$from = $this->scopeConfig->getValue(self::XML_PATH_IDENTITY, $websiteScope, $website->getCode());
                $from = [
                    'name' => __(' Dilmah '),
                    'email' => $this->scopeConfig->getValue(
                        self::XML_PATH_CONTACT_EMAIL_RECIPIENT_EMAIL,
                        $websiteScope,
                        $website->getCode()
                    )
                ];
                $template = $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE, $websiteScope, $website->getCode());

                $store = $this->storeFactory->create()->getCollection()
                    //->addWebsiteFilter($order->getStore()->getWebsiteId())
                    ->setWithoutDefaultFilter()
                    ->addIdFilter($order->getStore()->getStoreId())
                    ->getFirstItem();

                if (!$store->isActive()) {
                    continue;
                }
                if (\Zend_Validate::is(trim($recipient), 'EmailAddress')) {
                    $transport = $this->transportBuilder
                        ->setTemplateIdentifier($template)
                        ->setTemplateOptions(
                            [
                                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                'store' => $store->getId(),
                            ]
                        )
                        ->setTemplateVars(
                            [
                                'order' => $order,
                                'items' => $itemData,
                                'store' => $store
                            ]
                        )
                        ->setFrom($from)
                        ->addTo(trim($recipient))
                        ->getTransport();

                    try {
                        $transport->sendMessage();
                        $result = true;
                    } catch (\Magento\Framework\Exception\MailException $e) {
                        $result = false;
                    }
                    if ($result) {
                        try {
                            $order->setReviewEmailSent(1);
                            $order->save();
                            echo "\n OrderID: " . $order->getIncrementId() . " - Email: " . $order->getCustomerEmail() .
                                " - sent";
                        } catch (\Exception $e) {
                            echo "\n OrderID: " . $order->getIncrementId() . " - Email: " . $order->getCustomerEmail() .
                                " - failed";
                            $this->logger->addError($e->getMessage());
                        }
                    }
                }
            }
        }
    }

    /**
     * returns the store id array only for Main Website
     *
     * @return array
     */
    public function getMainStoreIds()
    {
        $stores = [];
        $storeCollection = $this->storeFactory->create()->getCollection();


        /** @var \Magento\Store\Model\Store $store */
        foreach ($storeCollection as $store) {
            if (!$store->isActive()) {
                continue;
            }
            $stores[] = $store->getId();
        }

        return $stores;
    }

    /**
     * return order collection filtered with a pre-defined criteria
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected function getCollection()
    {
        $model = $this->getOrderStatusModel();
        if ($model) {
            $collection = $model->create();

            $collection->getSelect()->joinInner(
                ['ship' => 'sales_shipment'],
                'ship.order_id = main_table.entity_id',
                ['ship.created_at as shipment_created_at']
            );
            $storeIds = $this->getMainStoreIds(); // store ids of Main Website
            $laps = isset($this->settings[self::XML_PATH_LAPSE]) ? $this->settings[self::XML_PATH_LAPSE] : 0;
            $from = date(
                'Y-m-d',
                strtotime("-" . $laps * 2 . " days")
            );  // limits the order list by sending emails for very old orders
            $to = date('Y-m-d', strtotime("-" . $laps . " days"));
            $filters = [
                'main_table.store_id' => $storeIds,
                'main_table.status' => self::ORDER_STATUS_TO_FILTER_ORDERS,
                self::REVIEW_EMAIL_SENT => 0,
                'ship.created_at' => ['from' => $from, 'to' => $to, 'date' => true]    //['lt' => $from]
            ];
            foreach ($filters as $field => $condition) {
                $collection->addFieldToFilter($field, $condition);
            }
            return $collection->load();
        }
        return false;
    }

    /**
     * returns the items collection of the order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     */
    protected function getItemsCollection($order)
    {
        $collection = $this->orderItemCollectionFactory->create()->setOrderFilter($order);

        if ($order->getId()) {
            foreach ($collection as $item) {
                $item->setOrder($order);
            }
        }
        return $collection;
    }
}

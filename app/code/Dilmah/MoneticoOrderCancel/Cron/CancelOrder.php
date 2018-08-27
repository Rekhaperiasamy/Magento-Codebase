<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_MoneticoOrderCancel
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\MoneticoOrderCancel\Cron;

/**
 * Class CancelOrder
 * @package Dilmah\MoneticoOrderCancel\Cron
 */

class CancelOrder
{
    /**
     * @var \Dilmah\MoneticoOrderCancel\Logger\Logger
     */
    protected $_logger;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $salesOrderCollectionFactory;
    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    protected $orderManagement;

    const ORDER_STATUS_TO_FILTER_ORDERS = 'pending';

    /**
     * CancelOrder constructor.
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     * @param \Dilmah\MoneticoOrderCancel\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Dilmah\MoneticoOrderCancel\Logger\Logger $logger
    ) {
        $this->_logger = $logger;
        $this->salesOrderCollectionFactory = $salesOrderCollectionFactory;
        $this->orderManagement = $orderManagement;
    }

    /**
     * Update the order status to cancel
     * @return $this
     */

    public function execute()
    {

        $orderCollection = $this->getOrderCollection();

        if(($orderCollection->getSize() > 0)){
            foreach($orderCollection as $order){

                try {
                    $this->orderManagement->cancel($order->getEntityId());
                    $this->_logger->addInfo(__('Order Cancelled #'.$order->getEntityId()));
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->_logger->error($e->getMessage());
                } catch (\Exception $e) {
                    $this->_logger->error(__('You have not canceled the item.'));
                }

            }
        } else {
            $this->_logger->error('No Pending Order Found');
        }
        return $this;
    }

    /**
     * Prepare order collection
     * @return $this
     */

    protected function getOrderCollection()
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $salesOrderCollection */
        $salesOrderCollection = $this->salesOrderCollectionFactory->create();
        $storeIds = $this->getMainStoreIds(); // store ids of Main Website

        $filters = [
            'store_id' => $this->getMainStoreIds(),
            'status' => self::ORDER_STATUS_TO_FILTER_ORDERS,
            'created_at' => array('lteq'=> date('Y-m-d', strtotime("-1 days")))

        ];
        foreach ($filters as $field => $condition) {
            $salesOrderCollection->addFieldToFilter($field, $condition);
        }

        $salesOrderCollection->getSelect()->joinLeft('sales_order_payment', 'sales_order_payment.parent_id = main_table.entity_id',array('method'));

        $salesOrderCollection->addFieldToFilter("sales_order_payment.method","monetico_onetime");

        return $salesOrderCollection->load();
    }

    /**
     * Getting active store id of france website
     * @return array
     */

    public function getMainStoreIds(){
        $stores = [];

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \Magento\Store\Model\StoreManagerInterface|\Magento\Store\Model\StoreManager $storeManager */

        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');

        $storeData = $storeManager->getStores($withDefault = false);

        foreach($storeData as $store){
            if($store->getData("is_active")){

                if($store->getWebsite()->getCode() == "web_fr") {
                    $stores[] = $store->getId();
                }

            }

        }

        return $stores;
    }

}
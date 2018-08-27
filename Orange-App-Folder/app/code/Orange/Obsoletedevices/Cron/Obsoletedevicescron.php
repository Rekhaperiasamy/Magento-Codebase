<?php

namespace Orange\ObsoleteDevices\Cron;

use Magento\Framework\App\ObjectManager;

class Obsoletedevicescron {

    protected $_logger;

    public function __construct(\Psr\Log\LoggerInterface $logger) {
        $this->_logger = $logger;
    }

    public function execute() {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $log_mode = $this->scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        try {
            $response = 'obsolete devices cron job';
            if (isset($log_mode) && $log_mode == 1) {
                $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/onsoletedevices_response.log.log', $response);
                $this->_logger->info(__METHOD__);
            }

            $magentoDateObject = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
            $currentdate = $magentoDateObject->gmtDate();
            $current_date = date('Y-m-d', strtotime($currentdate));
            //Select Data from table
            $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');

            $collection = $productCollection->create()
                    //->addAttributeToFilter('sku', $productsku)
                    ->addAttributeToFilter('obsolete_device', '1')
                    ->addAttributeToFilter('obsolete_createddate', $current_date)
                    //->addAttributeToFilter('status', '1')
                    //->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->load();
            foreach ($collection as $product) {


                //echo 'Name  =  '.$product->getId().'<br>';
                $product_id = $product->getId();
                $productdata = $objectManager->create('Magento\Catalog\Model\ProductFactory')->create();
                $productdata->load($product_id);
                //$productdata->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                $productdata->setStatus(2);
                $productdata->setVisibility(1);
                $productdata->save();
                if (isset($log_mode) && $log_mode == 1) {
                    $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/obsoletedevices_completed.log.log', 'completed');
                }
            }
        }  //try close
        /* ---- */ catch (\Exception $e) {
            
        }
    }

}

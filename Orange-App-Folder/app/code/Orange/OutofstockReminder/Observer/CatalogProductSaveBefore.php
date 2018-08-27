<?php

namespace Orange\OutofstockReminder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;

class CatalogProductSaveBefore implements ObserverInterface {

    public function execute(\Magento\Framework\Event\Observer $observer) {


        $product = $observer->getEvent()->getProduct();
        $objectManager = ObjectManager::getInstance();
        //echo '<pre>';print_r($product->getData());die;
        if ($product->hasDataChanges()) {
            $oldValues = $product->getOrigData();
            $newValues = $product->getData();
            //echo "<pre>";print_r($newValues); die;
            $sku = $newValues['sku'];
            if (isset($oldValues['quantity_and_stock_status']['is_in_stock'])) {
                $oldStatus = $oldValues['quantity_and_stock_status']['is_in_stock'];
                $updatedStatus = $newValues['quantity_and_stock_status']['is_in_stock'];
            } else {
                $oldStatus = $product->getOrigData('quantity_and_stock_status');
                $updatedStatus = $product->getData('quantity_and_stock_status');
            }

            if (($oldStatus != $updatedStatus) && ($updatedStatus == 1)) {
                // echo 'hi';die;

                $resource = $objectManager->create('\Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
                $values = $connection->fetchAll("select * from `outofstock_reminder` where sku = '" . $sku . "' AND status = 'requested' ");
                if (!empty($values)) {
                    //echo 'not empty';die;
                    foreach ($values as $reminderData) {
                        // echo '<pre>';print_r($reminderData);die;
                        $themeTable = $resource->getTableName('outofstock_reminder');
                        $sql = "UPDATE " . $themeTable . " SET status='completed' WHERE sku= '" . $sku . "' ";
                        $connection = $resource->getConnection()->query($sql);
                        $objectManager->create('Orange\Selligent\Helper\Selligent')->stockalert($reminderData);
                        
                    }
                }
            }
        }
    }

}

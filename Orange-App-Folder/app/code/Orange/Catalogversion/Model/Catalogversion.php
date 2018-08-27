<?php

/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Catalogversion\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Exception\CatalogversionException;

/**
 * Catalogversiontab catalogversion model
 */
class Catalogversion extends AbstractModel {

     /**
      * @param \Magento\Framework\Model\Context $context
      * @param \Magento\Framework\Registry $registry
      * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
      * @param \Magento\Framework\Data\Collection\Db $resourceCollection
      * @param array $data
      */
     public function __construct(
     \Magento\Framework\Model\Context $context, \Magento\Framework\App\ResourceConnection $resourceConnection, \Magento\Framework\Registry $registry, \Magento\Catalog\Model\Category $categoryModel, \Magento\Catalog\Model\ProductFactory $_productFactory, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = []
     ) {
          $this->categoryModel = $categoryModel;
          $this->_productFactory = $_productFactory;
          $this->resourceConnection = $resourceConnection;
          parent::__construct($context, $registry, $resource, $resourceCollection, $data);
     }

     /**
      * @return void
      */
     protected function _construct() {
          $this->_init(\Orange\Catalogversion\Model\ResourceModel\Catalogversion::class);
     }

     public function getRevisionNumber($productId) {
          $newrevison = 1;
          $resources = \Magento\Framework\App\ObjectManager::getInstance()
                  ->get('Magento\Framework\App\ResourceConnection');
          $tableName = $resources->getTableName('catalogversion_catalogversion');
          $connection = $resources->getConnection();
          $select = "SELECT revision_number FROM `" . $tableName . "` WHERE productid='" . $productId . "' ORDER BY revision_number DESC LIMIT 1";
          $result = $connection->fetchAll($select);
          if ($result) {
               $revison = $result[0]['revision_number'] + $newrevison;
          } else {
               $revison = $newrevison;
          }
          return $revison;
     }
	 public function logCreate($fileName, $data) {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $this->objectManager()->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $log_mode = $scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (isset($log_mode) && $log_mode == 1) {
        $writer = new \Zend\Log\Writer\Stream(BP . "$fileName");
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($data);
        }
    }

     public function catalogver($rowData, $priceV, $storecode) {
          try {
               $storeView = 0;
               if ($storecode == 'nl') {
                    $storeView = 1;
               } else if ($storecode == 'fr') {
                    $storeView = 2;
               }

               $product_id = $rowData['entity_id'];
               $relatedProductSKU = "";
               $upsellProductSKU = "";
               $crossSellProductSKU = "";
               $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
               $timestamp = $objectManager->create('\Magento\Framework\Stdlib\DateTime\DateTime')->timestamp();
               $currentproduct = $this->_productFactory->create()->setStoreId($storeView)->load($product_id);
               $revision_number = $this->getRevisionNumber($product_id);
               $sku = $currentproduct->getData('sku');
               $name = $currentproduct->getData('name');
               $status = $currentproduct->getData('status');
               $price = $currentproduct->getData('price');
               $url_key = $currentproduct->getData('url_key');
               $meta_title = !empty($currentproduct->getData('meta_title')) ? $currentproduct->getData('meta_title') : '';
               $meta_keyword = !empty($currentproduct->getData('meta_keyword')) ? $currentproduct->getData('meta_keyword') : '';
               $meta_description = !empty($currentproduct->getData('meta_description')) ? $currentproduct->getData('meta_description') : '';
               $description = !empty($currentproduct->getData('description')) ? $currentproduct->getData('description') : '';
               $short_description = !empty($currentproduct->getData('short_description')) ? $currentproduct->getData('short_description') : '';
               $visibility = $currentproduct->getData('visibility');
               if ($currentproduct->getData('store_id')) {
                    $stroreId = $currentproduct->getData('store_id');
               } else {
                    $stroreId = 0;
               }
               $attribute_set_id = $currentproduct->getData('attribute_set_id');
               $categories = $currentproduct->getCategoryIds();
               $category = [];
               foreach ($categories as $key => $val) {
                    $categoryId = $val;
                    $categoryData = $this->categoryModel->load($categoryId)->getData();

                    $category[] = $categoryData['name'];
               }
               $categories = implode(",", $category);

               $relatedProducts = $currentproduct->getRelatedProducts();
               $upSellProducts = $currentproduct->getUpSellProducts();
               $crossSellProducts = $currentproduct->getCrossSellProducts();
               $relatedProducsArray = array();
               $upSellProducsArray = array();
               $crossProducsArray = array();

               if (!empty($relatedProducts)) {
                    foreach ($relatedProducts as $relatedProduct) {
                         $relatedProducsArray[] = $relatedProduct->getSku();
                    }
               }
               if (!empty($upSellProducts)) {
                    foreach ($upSellProducts as $upSellProduct) {
                         $upSellProducsArray[] = $upSellProduct->getSku();
                    }
               }
               if (!empty($crossSellProducts)) {
                    foreach ($crossSellProducts as $crossSellProduct) {
                         $crossProducsArray[] = $crossSellProduct->getSku();
                    }
               }

               $relatedProductSKU = serialize($relatedProducsArray);
               $upsellProductSKU = serialize($upSellProducsArray);
               $crossSellProductSKU = serialize($crossProducsArray);

               $StockAndQuantity = $currentproduct->getQuantityAndStockStatus();

               if (isset($StockAndQuantity['is_in_stock'])) {
                    $oldStatus = $StockAndQuantity['is_in_stock'];

                    if ($oldStatus) {
                         $stock = 'In Stock';
                    } else {
                         $stock = 'Out of Stock';
                    }
               } else {
                    $oldStatus = $currentproduct->getOrigData('quantity_and_stock_status');
                    if ($oldStatus) {
                         $stock = 'In Stock';
                    } else {
                         $stock = 'Out of Stock';
                    }
               }

               $quantity = $StockAndQuantity['qty'];


               $StockAndQuantity = $currentproduct->getData('quantity_and_stock_status');
               $stockStatus = $stock;

               $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
               $connection = $resource->getConnection();
               $sql = "Select * FROM eav_attribute where  entity_type_id=4 AND is_user_defined";
               $attributesCollection = $connection->fetchAll($sql);
               $attributes = [];

               foreach ($attributesCollection as $attribute) {
                    $attributeCode = $attribute['attribute_code'];
                    $attributeLabel = $attribute['frontend_label'];
                    if ($currentproduct->getData($attributeCode)) {
                         $value = '';
                         if ($attribute['frontend_input'] == 'select') {
                              $valueforOptionID = $currentproduct->getData($attributeCode);
                              $attr = $currentproduct->getResource()->getAttribute($attributeCode);
                              $optionText = $attr->getSource()->getOptionText($valueforOptionID);
                              $value = $optionText;
                         } elseif ($attribute['frontend_input'] == 'boolean') {
                              if ($currentproduct->getData($attributeCode)) {
                                   $value = 'yes';
                              } else {
                                   $value = 'No';
                              }
                         } else {
                              $value = $currentproduct->getData($attributeCode);
                         }
                         $custom_attribues[$attributeLabel] = $value;
                    }
               }


               $finalData = serialize($custom_attribues);

               //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
               $catalogVersion = $objectManager->create('Orange\Catalogversion\Model\Catalogversion');
               $catalogVersion->setProductid($product_id);
               $catalogVersion->setSku($sku);
               $catalogVersion->setName($name);
               $catalogVersion->setStatus($status);
               $catalogVersion->setPrice($price);
               $catalogVersion->setQuantity($quantity);
               $catalogVersion->setCustomAttribueInfo($finalData);
               $catalogVersion->setStock($stockStatus);
               $catalogVersion->setCategories($categories);
               $catalogVersion->setVisibility($visibility);
               $catalogVersion->setStoreId($stroreId);
               $catalogVersion->setRevisionNumber($revision_number);
               $catalogVersion->setAttributeSetId($attribute_set_id);
               $catalogVersion->setUrlkey($url_key);
               $catalogVersion->setMetatitle($meta_title);
               $catalogVersion->setMetakeyword($meta_keyword);
               $catalogVersion->setMetadescription($meta_description);
               $catalogVersion->setDescription($description);
               $catalogVersion->setShortdescription($short_description);
               $catalogVersion->setRelatedProductInfo($relatedProductSKU);
               $catalogVersion->setUpsellProductInfo($upsellProductSKU);
               $catalogVersion->setCrosssellProductInfo($crossSellProductSKU);
               $catalogVersion->setCreated($timestamp);
               $catalogVersion->save();
               if (($priceV != $currentproduct->getPrice()) && ($storecode != 'nl' && $storecode != 'fr')) {
                    $catalogPriceRevison = $objectManager->create('Orange\Catalogversion\Model\Pricerevision');
                    $price_revision_number = $this->getPriceRevisonNumber($product_id, $priceV);
                    $catalogPriceRevison->setProductName($name);
                    $catalogPriceRevison->setProductId($product_id);
                    $catalogPriceRevison->setSku($sku);
                    $catalogPriceRevison->setPrice($price);
                    $catalogPriceRevison->setRevisonNumber($price_revision_number);
                    $catalogPriceRevison->setCreated($timestamp);
                    $catalogPriceRevison->save();
               }
               return true;
          } catch (Exception $ex) {
              $this->logCreate('/var/log/catalogversion.log', $rowData.'->'.$priceV.'->'.$storecode);
          }
     }

    public function getPriceRevisonNumber($productId, $priceV) {
          $newrevison = 1;
          $resources = \Magento\Framework\App\ObjectManager::getInstance()
                  ->get('Magento\Framework\App\ResourceConnection');
          $tableName = $resources->getTableName('catalogversion_price_version');
          $connection = $resources->getConnection();
          $select = "SELECT revison_number FROM `" . $tableName . "` WHERE product_id='" . $productId . "' ORDER BY revison_number DESC LIMIT 1";
          $result = $connection->fetchAll($select);
          if ($result) {
               $revison = $result[0]['revison_number'] + $newrevison;
          } else {
               $revison = $newrevison;
          }
          return $revison;
     }

}

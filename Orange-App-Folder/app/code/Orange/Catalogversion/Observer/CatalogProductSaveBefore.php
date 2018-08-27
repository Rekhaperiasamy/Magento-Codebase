<?php
namespace Orange\Catalogversion\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\App\RequestInterface ;

class CatalogProductSaveBefore implements ObserverInterface {
	
protected $_request;
	public function __construct(
	          
	         \Magento\Framework\App\RequestInterface $request,
			\Magento\Catalog\Model\Product $productModel,
			\Magento\Catalog\Model\Category $categoryModel,
			\Magento\Framework\App\ResourceConnection $resourceConnection
	){
	    $this->_request = $request;
		$this->productModel = $productModel;
		$this->categoryModel = $categoryModel;
		$this->resourceConnection = $resourceConnection;
	}
	
public function execute(\Magento\Framework\Event\Observer $observer) {
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		 $product = $observer->getProduct();
		 $resources = \Magento\Framework\App\ObjectManager::getInstance()
			->get('Magento\Framework\App\ResourceConnection');
		 $tableName=$resources->getTableName('catalogversion_catalogversion');
		 if ($product->hasDataChanges()) {
			$orgData = $product->getOrigData();
			if(!empty($orgData['entity_id']))
			{
			    $imageURLPath="";
				$relatedProductSKU="";
				$upsellProductSKU="";
				$crossSellProductSKU="";
			    $revision_number=$this->getRevisionNumber($orgData['entity_id']);
			    $timestamp=$objectManager->create('\Magento\Framework\Stdlib\DateTime\DateTime')->timestamp();
				$productid 			= $orgData['entity_id'];
				$sku 				= $orgData['sku'];
				$name 				= $orgData['name'];
				$status 			= $orgData['status'];
				$price 				= $orgData['price'];
				$urlkey				= $orgData['url_key'];
				$metatitle          = !empty($orgData['meta_title']) ? $orgData['meta_title'] : '';
				$metakeyword		= !empty($orgData['meta_keyword']) ? $orgData['meta_keyword'] : '';
				$metadescription	= !empty($orgData['meta_description']) ? $orgData['meta_description'] : '';
				$description		= !empty($orgData['description']) ? $orgData['description'] : '';
				$shortdescription	= !empty($orgData['short_description']) ? $orgData['short_description'] : '';
				$catlogvisibility   = $orgData['visibility'];
				if(isset($orgData['store_id'])){
				   $stroreId           = $orgData['store_id'];
				}else{
				   $stroreId           = 0; 
				}
				$attributeSetId     = $orgData['attribute_set_id'];
				
				$categories = $product->getCategoryIds();
				$category = [];
				foreach($categories as $key => $val){	 
					$categoryId = $val;
					$categoryData = $this->categoryModel->load($categoryId)->getData();
					
					$category[] = $categoryData['name'];
				}
				$categories= implode(",", $category);
            	$StockAndQuantity = $orgData['quantity_and_stock_status'];
				$stockStatus=$StockAndQuantity['is_in_stock'];
				$quantity=$StockAndQuantity['qty'];
				$connection = $this->resourceConnection->getConnection();
				$sql = "Select * FROM eav_attribute where  entity_type_id=4 AND is_user_defined";
				$attributesCollection = $connection->fetchAll($sql);
				$attributes = [];
				foreach ($attributesCollection as $attribute) {
					$attributeCode = $attribute['attribute_code'];
					$attributeLabel = $attribute['frontend_label'];
					if(isset($orgData[$attributeCode]))
					{
						$value = '';
						if($attribute['frontend_input']=='select'){
						
							$valueforOptionID = $orgData[$attributeCode];
							$attr = $product->getResource()->getAttribute($attributeCode);
							$optionText = $attr->getSource()->getOptionText($valueforOptionID);
							$value = $optionText;
							
						}elseif($attribute['frontend_input']=='boolean'){
													
							if( $orgData[$attributeCode]){
								$value = 'yes';
							} else {
								$value = 'No';
							}
						}else{
							$value = $orgData[$attributeCode];
						}
						$custom_attribues[$attributeLabel] = $value;	
					}
				}
				$finalData  = serialize($custom_attribues);
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$catalogVersion = $objectManager->create('Orange\Catalogversion\Model\Catalogversion');
				$catalogVersion->setProductid($productid);
				$catalogVersion->setSku($sku);
				$catalogVersion->setName($name);
				$catalogVersion->setStatus($status);
				$catalogVersion->setPrice($price);
				$catalogVersion->setQuantity($quantity);
				$catalogVersion->setCustomAttribueInfo($finalData);
				$catalogVersion->setStock($stockStatus);
				$catalogVersion->setCategories($categories);
				$catalogVersion->setVisibility($catlogvisibility);
				$catalogVersion->setStoreId($stroreId);
				$catalogVersion->setRevisionNumber($revision_number);
				$catalogVersion->setAttributeSetId($attributeSetId);
				$catalogVersion->setUrlkey($urlkey);
				$catalogVersion->setMetatitle($metatitle);
				$catalogVersion->setMetakeyword($metakeyword);
				$catalogVersion->setMetadescription($metadescription);
				$catalogVersion->setDescription($description);
				$catalogVersion->setShortdescription($description);
				$catalogVersion->setRelatedProductInfo($relatedProductSKU);
				$catalogVersion->setUpsellProductInfo($upsellProductSKU);
				$catalogVersion->setCrosssellProductInfo($crossSellProductSKU);
				$catalogVersion->setCreated($timestamp);
				$catalogVersion->save();
				if($price != $product->getPrice()){
					$catalogPriceRevison = $objectManager->create('Orange\Catalogversion\Model\Pricerevision');
					$price_revision_number=$this->getPriceRevisonNumber($productid);
					$catalogPriceRevison->setProductName($name);
					$catalogPriceRevison->setProductId($productid);
					$catalogPriceRevison->setSku($sku);
					$catalogPriceRevison->setPrice($price);
					$catalogPriceRevison->setRevisonNumber($price_revision_number);
					$catalogPriceRevison->setCreated($timestamp);
					$catalogPriceRevison->save();
				}
			} 
		return true;
		}
	}
	
	/*
	 Retrieve product previous price revison number
	 * @param int $productId
	   @return int
   */
	function getPriceRevisonNumber($productId){
		 $newrevison=1;
		 $resources = \Magento\Framework\App\ObjectManager::getInstance()
			->get('Magento\Framework\App\ResourceConnection');
		 $tableName=$resources->getTableName('catalogversion_price_version');	
		 $connection= $resources->getConnection();
		 $select="SELECT revison_number FROM `".$tableName."` WHERE product_id='".$productId."' ORDER BY revison_number DESC LIMIT 1";
		 $result = $connection->fetchAll($select); 
		 if($result){
			$revison=$result[0]['revison_number']+$newrevison;
		 }else{
			$revison=$newrevison;
		 }
		 return $revison; 
	}
	
	/*
	 Retrieve product previous revison number
	 * @param int $productId
	   @return int
   */
    function getRevisionNumber($productId){
		 $newrevison=1;
		 $resources = \Magento\Framework\App\ObjectManager::getInstance()
			->get('Magento\Framework\App\ResourceConnection');
		 $tableName=$resources->getTableName('catalogversion_catalogversion');	
		 $connection= $resources->getConnection();
		 $select="SELECT revision_number FROM `".$tableName."` WHERE productid='".$productId."' ORDER BY revision_number DESC LIMIT 1";
		 $result = $connection->fetchAll($select); 
		 if($result){
			$revison=$result[0]['revision_number']+$newrevison;
		 }else{
			$revison=$newrevison;
		 }
		 return $revison; 
	}
}

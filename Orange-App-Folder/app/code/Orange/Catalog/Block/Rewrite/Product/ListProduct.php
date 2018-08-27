<?php
namespace Orange\Catalog\Block\Rewrite\Product;

use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Customer\Api\Data\GroupInterface;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{	
	protected $_productCount; 
	protected $_customerGroup; 


    protected function _beforeToHtml() 
	{
        try 
		{
			$toolbar = $this->getToolbarBlock();
			// called prepare sortable parameters
			$this->_customerGroup = $this->getCustomerTypeName();
			$collection = $this->_getProductCollection();		        

			// set collection to toolbar and apply sort		
			$toolbar->setCollection($collection);		
			$this->setChild('toolbar', $toolbar);
			//Core Fix for pagination when grouping products by family
			$CustomQuery = $collection->getSelect(); // Save the Query			
			$QueryData = $this->GetProductsCount($CustomQuery);//Get Datas from Query
			$this->_productCount = count($QueryData);
			$this->_eventManager->dispatch(
				'catalog_block_product_list_collection',
				['collection' => $this->_getProductCollection()]
			);
			$this->_getProductCollection()->load();		
			return parent::_beforeToHtml();
        } 
		catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		} 
    }

    protected function _getProductCollection() 
	{
        try 
		{
			if ($this->_productCollection === null) {
				$layer = $this->getLayer();
				/* @var $layer \Magento\Catalog\Model\Layer */
				if ($this->getShowRootCategory()) {
					$this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
				}

				// if this is a product view page
				if ($this->_coreRegistry->registry('product')) {
					// get collection of categories this product is associated with
					$categories = $this->_coreRegistry->registry('product')
						->getCategoryCollection()->setPage(1, 1)
						->load();
					// if the product is associated with any category
					if ($categories->count()) {
						// show products from this category
						$this->setCategoryId(current($categories->getIterator()));
					}
				}

				$origCategory = null;
				if ($this->getCategoryId()) {
					try {
						$category = $this->categoryRepository->get($this->getCategoryId());
					} catch (NoSuchEntityException $e) {
						$category = null;
					}

					if ($category) {
						$origCategory = $layer->getCurrentCategory();
						$layer->setCurrentCategory($category);
					}				
				}	
				$currentCat = $layer->getCurrentCategory();
				$customerGroup = $this->getCustomerTypeName();
				$customerGroupId = $this->getCustomerTypeId();
				/** Change Attribute Codes based on customer group **/
				if($customerGroup == 'SOHO')
				{
					$hero_attribute_code= 'is_hero_handset_soho';
					$hero_tariff = 'linked_hero_tariff_soho';
					$accHero = 'is_hero_accessory_soho';
					$query_hero_tariff = 'at_linked_hero_tariff_soho';
					$subscriptionId = $this->getProductAttributeId('subscription_amount');
				}
				else
				{
					$hero_attribute_code= 'is_hero_handset_b2c';
					$hero_tariff = 'linked_hero_tariff_b2c';
					$accHero = 'is_hero_accessory_b2c';
					$query_hero_tariff = 'at_linked_hero_tariff_b2c';
					$subscriptionId = $this->getProductAttributeId('subscription_amount');
				}
				$currentStoreId = $this->_storeManager->getStore()->getId();
				$popularityHours = $currentCat->getPopularityHours();
				if($popularityHours=='')
				{
					// No option to differentiate which category has what type of products so used Category ID
					if($currentCat->getName() == 'Accessories and connected objects')
					{ // Accessory Category
						$popularityHours = ObjectManager::getInstance()->create('Orange\Priority\Helper\Data')->getAccessoryPopularityDuration();
					}
					else
					{
						$popularityHours = ObjectManager::getInstance()->create('Orange\Priority\Helper\Data')->getDevicePopularityDuration();					
					}
				}
				$today = time();
				/**
				 * $last = $today - (60*60*24*3);//3 Days for devices
				 * $last = $today - (60*60*24*10);//10 Days for accessories
				 */						
				$last = $today - (60*60*$popularityHours);
				$from = date("Y-m-d", $last);			
				$customCollection = $layer->getProductCollection();				
				
				//$subscriptionId = $this->getProductAttributeId('subscription_amount'); //Get Attribute ID using attribute code
				$accessoryFamilyId = $this->getProductAttributeId('accessory_family');
				$handsetFamilyId = $this->getProductAttributeId('handset_family');
				$sohoPriceId = $this->getProductAttributeId('soho_price');				
				$customCollection->joinTable('catalog_category_product', 'product_id=entity_id', array('category_id'=>'category_id'), null, 'left'); //Join Category ID to collection						
				$customCollection->joinAttribute($hero_tariff, 'catalog_product/'.$hero_tariff, 'row_id', null, 'left'); //Join Tarriff attribute to collection
				$customCollection->joinAttribute($hero_attribute_code, 'catalog_product/'.$hero_attribute_code, 'row_id', null, 'left');
				$customCollection->joinAttribute($accHero, 'catalog_product/'.$accHero, 'row_id', null, 'left');
				$customCollection->joinAttribute('handset_description', 'catalog_product/handset_description', 'row_id', null, 'left'); //Join HandsetDescription attribute to collection
				//Used getSelect instead of joinAttribute to join custom attributes to perform query manipulation for priority and popularity
				$customCollection->getSelect()->joinLeft(
				  ['catalog_varchar_family'=>$customCollection->getTable('catalog_product_entity_varchar')],
				  'e.row_id = catalog_varchar_family.row_id and catalog_varchar_family.store_id = '.$currentStoreId.' and catalog_varchar_family.attribute_id ='.$accessoryFamilyId,
				  ['dev_accessory_family'=>'catalog_varchar_family.value']);
				$customCollection->getSelect()->joinLeft(
				  ['catalog_varchar_hfamily'=>$customCollection->getTable('catalog_product_entity_varchar')],
				  'e.row_id = catalog_varchar_hfamily.row_id and catalog_varchar_hfamily.store_id = '.$currentStoreId.' and catalog_varchar_hfamily.attribute_id ='.$handsetFamilyId,
				  ['dev_handset_family'=>'catalog_varchar_hfamily.value','store_id'=>'catalog_varchar_hfamily.store_id']);
				//Get Popularity count(Number of views) for each family
				$popularQuery = "(SELECT COUNT(family_popular.id) AS popularity, family_popular.family,family_popular.logged_at FROM ".$customCollection->getTable('catalog_product_family_popularity')." AS family_popular where family_popular.logged_at >= '".$from."' and family_popular.store_id=".$currentStoreId."  GROUP BY family_popular.family)";			
				$customCollection->getSelect()->joinLeft(
				['family_popularity'=> new \Zend_Db_Expr($popularQuery)],
				'catalog_varchar_family.value = family_popularity.family or catalog_varchar_hfamily.value = family_popularity.family',
				['popularity'=>'family_popularity.popularity']);
				//Joined Priority Table to get Priority order for each family
				//0- Priority for All store View
				$priorityQuery = "(CASE WHEN `family_priority`.`priority` IS NULL THEN 9999999 ELSE `family_priority`.`priority` END)";
				$customCollection->getSelect()->joinLeft(
				  ['family_priority'=>$customCollection->getTable('family_priority')],
				  'family_priority.customer_group='.$customerGroupId.' and (family_priority.store_id='.$currentStoreId.' or family_priority.store_id = 0) and (catalog_varchar_family.value = family_priority.family_name or catalog_varchar_hfamily.value = family_priority.family_name)',
				  ['priorityorder'=>'family_priority.priority','priority'=>'(CASE WHEN `family_priority`.`priority` IS NULL THEN 9999999 ELSE `family_priority`.`priority` END)']);
				//Joined product entity table to get the tariff product id associated with each product
				$customCollection->getSelect()->joinLeft(
				  ['catalog_subscription_entity'=>$customCollection->getTable('catalog_product_entity')],			  
				  $query_hero_tariff.".value = catalog_subscription_entity.sku",
				  ['subscription_id'=>'catalog_subscription_entity.row_id']);
				//Joined subscription_price attribute to perform sort,used getSelect because this price is associated with different product
				$customCollection->getSelect()->joinLeft(
				  ['catalog_varchar'=>$customCollection->getTable('catalog_product_entity_decimal')],
				  'catalog_subscription_entity.row_id = catalog_varchar.row_id and catalog_varchar.attribute_id ='.$subscriptionId,
				  ['subscription_price'=>'catalog_varchar.value']);

				$customCollection->getSelect()->joinLeft(
				  ['catalog_subscription'=>$customCollection->getTable('catalog_product_entity_decimal')],
				  'e.row_id = catalog_subscription.row_id and catalog_subscription.attribute_id ='.$subscriptionId,
				  ['soho_price' => 'catalog_subscription.value','soho_subscription_price'=> new \Zend_Db_Expr('catalog_subscription.value/1.21')]);
				
				//MIN Subsidy Price
				$contextAttributeId = $this->getProductAttributeId('context_visibility');
				$subsidyQuery = "(Select upsell_product.product_id,MIN(catalog_price.final_price) AS final_price,catalog_context.value as context_visibility FROM ".$customCollection->getTable('catalog_product_link')." AS upsell_product INNER JOIN ".$customCollection->getTable('catalog_product_entity')." AS catalog_entity ON catalog_entity.entity_id = upsell_product.linked_product_id LEFT JOIN ".$customCollection->getTable('catalog_product_entity_varchar')." AS catalog_context ON catalog_context.row_id = catalog_entity.row_id AND catalog_context.attribute_id=".$contextAttributeId." AND (catalog_context.value=".$customerGroupId." OR catalog_context.value=".GroupInterface::CUST_GROUP_ALL.") LEFT JOIN ".$customCollection->getTable('catalog_product_entity')." AS catalog_context_entity ON catalog_context_entity.row_id = catalog_context.row_id INNER JOIN ".$customCollection->getTable('catalog_product_index_price')." AS catalog_price ON catalog_context_entity.entity_id = catalog_price.entity_id WHERE upsell_product.link_type_id='4' and catalog_price.customer_group_id=".$customerGroupId." GROUP BY upsell_product.product_id ORDER BY catalog_price.final_price)";
				$customCollection->getSelect()->joinLeft(
				['cat_subsidy'=> new \Zend_Db_Expr($subsidyQuery)],
				'e.row_id = cat_subsidy.product_id',
				['min_subsidy_price'=>'cat_subsidy.final_price','context_visibility' => 'cat_subsidy.context_visibility']);
				//MIN SOHO Subsidy Price
				$sohoSubsidyQuery = "(Select upsell_product.product_id,upsell_product.linked_product_id,catalog_entity.row_id AS linked_row_id,MIN(catalog_soho_price.value) AS soho_price FROM ".$customCollection->getTable('catalog_product_link')." AS upsell_product INNER JOIN ".$customCollection->getTable('catalog_product_entity')." AS catalog_entity ON upsell_product.linked_product_id = catalog_entity.entity_id LEFT JOIN ".$customCollection->getTable('catalog_product_entity_varchar')." AS catalog_context ON catalog_context.row_id = catalog_entity.row_id AND catalog_context.attribute_id=".$contextAttributeId." AND (catalog_context.value=".$customerGroupId." OR catalog_context.value=".GroupInterface::CUST_GROUP_ALL.")  LEFT JOIN ".$customCollection->getTable('catalog_product_entity_decimal')." AS catalog_soho_price ON catalog_soho_price.attribute_id=".$sohoPriceId." AND catalog_context.row_id = catalog_soho_price.row_id WHERE upsell_product.link_type_id='4' GROUP BY upsell_product.product_id ORDER BY catalog_soho_price.value)";
				$customCollection->getSelect()->joinLeft(
				['cat_soho_subsidy'=> new \Zend_Db_Expr($sohoSubsidyQuery)],
				'e.row_id = cat_soho_subsidy.product_id',
				['min_soho_subsidy_price'=>'cat_soho_subsidy.soho_price']);
				//Group products based on family in all LOB pages
				                
				// Based on category id - we set collection values - numbers are category-id
				if ($this->getCurrentCategoryID() == 14 || $this->getCurrentCategoryID() == 17) {
					$customCollection->getSelect()->where(new \Zend_Db_Expr("catalog_varchar_hfamily.store_id = " . $currentStoreId));
					if ($customerGroup == 'SOHO') {
						$customCollection->getSelect()->where(new \Zend_Db_Expr("at_is_hero_handset_soho.value IS NULL"));
					} else {
						$customCollection->getSelect()->where(new \Zend_Db_Expr("at_is_hero_handset_b2c.value IS NULL"));
					}
				}
				//Accessories hero prodcust
				if ($this->getCurrentCategoryID() == 15 || $this->getCurrentCategoryID() == 19 || $this->getCurrentCategoryID() == 22 || $this->getCurrentCategoryID() == 21 || $this->getCurrentCategoryID() == 44 || $this->getCurrentCategoryID() == 20) {
					if ($customerGroup == 'SOHO') {
						$customCollection->getSelect()->where(new \Zend_Db_Expr("at_is_hero_accessory_soho.value IS NULL"));
					} else {
						$customCollection->getSelect()->where(new \Zend_Db_Expr("at_is_hero_accessory_b2c.value IS NULL"));
					}
				}
				$customCollection->getSelect()->group('dev_accessory_family');
				$customCollection->getSelect()->group('dev_handset_family');			
				$this->_productCollection = $customCollection;					
				$this->prepareSortableFieldsByCategory($layer->getCurrentCategory());
				if ($origCategory) {
					$layer->setCurrentCategory($origCategory);
				}
			}
			return $this->_productCollection;
        } 
		catch (\Exception $e) {
			$this->messageManager->addException($e, __('Something Went Wrong.'));
			header("Location: http://" . $_SERVER['SERVER_NAME']);
			die();
		}
    }
	
	public function getCurrentCategoryID()
	{
		$objectManager = ObjectManager::getInstance();
		$category = $objectManager->get('Magento\Framework\Registry')->registry('current_category');//get current category
		return $category->getId();
	}

	/**
	 *	Get first Upsell product of virtual products
	 */
    public function getUpSellOneProduct(\Magento\Catalog\Model\Product $product) {
        try {
			$objectManager = ObjectManager::getInstance();
			$upSellProductIds = $product->getUpSellProductIds();
			if(isset($upSellProductIds)){
				$product = $objectManager->get('Magento\Catalog\Model\Product')->load($upSellProductIds[0]);
				$product['count'] = count($upSellProductIds);
				return $product;
			}
			return null;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }

	/**
	 *	Get all upsell product of virtual products
	 */
    public function getUpSellProducts(\Magento\Catalog\Model\Product $product) {
        try {
			$objectManager = ObjectManager::getInstance();
			$upSellProductIds = $product->getUpSellProductIds();
			if (isset($upSellProductIds)) {
				$products = array();
				foreach ($upSellProductIds as $productId) {
					$product = $objectManager->get('Magento\Catalog\Model\Product')->load($productId);
					$products[] = $product;
					unset($product);
				}
				return $products;
			}
			return null;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
	/**
	 *	Get Current Product Collection
	 */
	public function getcurproductcollection()
	{
		//$categoryId = 14;
		$categoryId = $this->getCurrentCategoryID();
		//$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$objectManager = ObjectManager::getInstance();
		$category = $objectManager->create('\Magento\Catalog\Model\Category')->load($categoryId);
		$collections = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
		$collections->addAttributeToSelect('*');
		$collections->addCategoryFilter($category);
		$collections->addAttributeToFilter('status','1');
		$brands = array();
		foreach ($collections as $collection) {
				if ($collection->getBrand()) {
				$brands[] = $collection->getBrand();
			}
		}
		return $brands;
		
	}
	
	/**
	 *	Get upsell product count based on handset family
	 */
    public function getUpSellProductCount(\Magento\Catalog\Model\Product $product) 
	{
        try {
			$objectManager = ObjectManager::getInstance();
			$upSellProductIds = $product->getUpSellProductIds();
			if(isset($upSellProductIds)){
				$products = array();
				foreach($upSellProductIds as $productId) {
					$product = $objectManager->get('Magento\Catalog\Model\Product')->load($productId);
					if($product->getStatus() == 1) {						
						if($product->getContextVisibility() == 32000 || $product->getContextVisibility() == $this->getCustomerTypeId()) {
							$products[] = $product->getHandsetFamily();
						}
					}
					unset($product);
				}
				return count(array_unique($products));
			}
			return null;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }

	/**
	 *	Get MAX qty of product in handset family
	 */
    public function getHandsetFamilyProduct($handset) 
	{
        try {
			$objectManager = ObjectManager::getInstance();
			$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
			$collection = $productCollection->create()
							 ->addAttributeToSelect('*')
				->addAttributeToFilter('handset_family', $handset)
				->addAttributeToFilter('status', 1)
				->addAttributeToFilter('type_id', 'simple')
				->load();
			$StockState = $objectManager->get('\Magento\CatalogInventory\Api\StockStateInterface');
			$prd = array();
			if(count($collection) > 0) {
				foreach($collection as $_item) {
					$prd[$_item->getId()] = $StockState->getStockQty($_item->getId(), $_item->getStore()->getWebsiteId());
				}
				$productId= array_search(max($prd), $prd);
				unset($prd);
				$product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
				return $product;
			}
			else {
				return false;
			}
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}	
    }
	
	/**
	 *	Get price from simple product of appropriate  bundle products
	 */
	public function getSimpleProductOfUpSellProducts(\Magento\Catalog\Model\Product $product, $handsetId) 
	{
        try {
			$objectManager = ObjectManager::getInstance();
			$upSellProductIds = $product->getUpSellProductIds();			
			$bundlehelper = $objectManager->get('Orange\Bundle\Helper\Data');
			$customerType = $this->getCustomerTypeName();
			$nintendoPrices = array();
			if (isset($upSellProductIds)) {
				foreach ($upSellProductIds as $productId) {
					$bundleProduct = $objectManager->get('Magento\Catalog\Model\Product')->load($productId);
					$products = $this->getChildrenProduct($bundleProduct);
					//echo 'upsell:'.$productId.'-('.$products.'|'.$handsetId.')';
					if ($products == $handsetId) {	
						// echo '<br>';
						// echo 'contenxt:'.$bundleProduct->getContextVisibility();
						if($bundleProduct->getContextVisibility() == '' || $bundleProduct->getContextVisibility() == GroupInterface::CUST_GROUP_ALL || $bundleProduct->getContextVisibility() == $this->getCustomerTypeId()) { //Check context visibility
							//echo 'final:'.$bundleProduct->getId();
							$tier = $bundlehelper->getBundleTierPrice($bundleProduct->getId());
							$bundleProductPrice = $tier[0]['final_price'];
							if($customerType == 'SOHO') {
								//array_push($nintendoPrices,$bundleProduct->getSohoPrice());
								return $bundleProduct->getSohoPrice();
							}
							else {
								//array_push($nintendoPrices,$bundleProductPrice);							
								return $bundleProductPrice;
							}
						}
						
					}
					unset($bundleProduct);
				}
				//if(count($nintendoPrices) > 0) {
					//return min($nintendoPrices); //Get min price from family
				//}
			}
        } 
		catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }

    /**
	 *	Get bundle product's children products
	 */
	public function getChildrenProduct(\Magento\Catalog\Model\Product $product) 
	{
        try {
			$pId = $product->getId();
			$objectManager = ObjectManager::getInstance();
			$typeInstance = $product->getTypeInstance();
			$requiredChildrenIds = $typeInstance->getChildrenIds($pId, true);
			$productIds = array();
			foreach ($requiredChildrenIds as $ids) {
				$productIds[] = $ids;
			}
			$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
			$collection = $productCollection->create()
					->addAttributeToSelect('*')
					->addAttributeToFilter('entity_id', array('in' => $productIds))
					->addAttributeToFilter('status', 1)
					->addAttributeToFilter('type_id', 'simple')
					->load();
			foreach ($collection as $prd) {
				return $prd->getId();
			}
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
        }
    }

    /**
     * Retrieve current category model object
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory() {		
        try {
			if (!$this->hasData('current_category')) {
				$this->setData('current_category', $this->_coreRegistry->registry('current_category'));
			}
			return $this->getData('current_category');
        } 
		catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
	/**
	 * Get Hero Smartphone Devices
	 */
    public function getBannerSmartphoneDevices() 
	{
        try {
			$objectManager = ObjectManager::getInstance();	
			$customerGroup = $this->getCustomerTypeName();
			if($customerGroup == 'SOHO')
			{
				$attribute_code= 'is_hero_handset_soho';
				$hero_tariff = 'linked_hero_tariff_soho';
				$query_hero_tariff = 'at_linked_hero_tariff_soho';
				$subscriptionId = $this->getProductAttributeId('subscription_amount');
			}
			else
			{
				$attribute_code= 'is_hero_handset_b2c';
				$hero_tariff = 'linked_hero_tariff_b2c';
				$query_hero_tariff = 'at_linked_hero_tariff_b2c';
				$subscriptionId = $this->getProductAttributeId('subscription_amount');
			}
			$currentCategoryId = $this->_coreRegistry->registry('current_category')->getId();
			$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
			$collection = $productCollection->create();
			$collection->addAttributeToSelect('*');
			$collection->addAttributeToFilter('status', 1);
			$collection->addCategoriesFilter(['eq' => $currentCategoryId]);
			$collection->joinAttribute($hero_tariff, 'catalog_product/'.$hero_tariff, 'entity_id', null, 'left');			
			$collection->addAttributeToFilter($attribute_code, array('neq' => ''));
			$collection->addAttributeToSort($attribute_code, 'ASC');			
			//Get the associated tariff product using attribute
			$collection->getSelect()->joinLeft(
			  ['catalog_subscription_entity'=>$collection->getTable('catalog_product_entity')],
			  $query_hero_tariff.'.value = catalog_subscription_entity.sku',
			  ['subscription_id'=>'catalog_subscription_entity.entity_id']);
			//Get the associated tariff product price
			$collection->getSelect()->joinLeft(
			  ['catalog_varchar'=>$collection->getTable('catalog_product_entity_decimal')],
			  'catalog_subscription_entity.row_id = catalog_varchar.row_id and catalog_varchar.attribute_id ='.$subscriptionId,
			  ['subscription_price'=>'catalog_varchar.value']);	
			$customerGroupId = $this->getCustomerTypeId();
			$contextAttributeId = $this->getProductAttributeId('context_visibility');
			$sohoPriceId = $this->getProductAttributeId('soho_price');
			$handsetFamilyId = $this->getProductAttributeId('handset_family');
			$currentStoreId = $this->_storeManager->getStore()->getId();
			$collection->getSelect()->joinLeft(
				  ['catalog_varchar_hfamily'=>$collection->getTable('catalog_product_entity_varchar')],
				  'e.row_id = catalog_varchar_hfamily.row_id and catalog_varchar_hfamily.store_id = '.$currentStoreId.' and catalog_varchar_hfamily.attribute_id ='.$handsetFamilyId,
				  ['dev_handset_family'=>'catalog_varchar_hfamily.value','store_id'=>'catalog_varchar_hfamily.store_id']);
			$subsidyQuery = "(Select upsell_product.product_id,MIN(catalog_price.final_price) AS final_price,catalog_context.value as context_visibility FROM ".$collection->getTable('catalog_product_link')." AS upsell_product INNER JOIN ".$collection->getTable('catalog_product_entity')." AS catalog_entity ON catalog_entity.entity_id = upsell_product.linked_product_id LEFT JOIN ".$collection->getTable('catalog_product_entity_varchar')." AS catalog_context ON catalog_context.row_id = catalog_entity.row_id AND catalog_context.attribute_id=".$contextAttributeId." AND (catalog_context.value=".$customerGroupId." OR catalog_context.value=".GroupInterface::CUST_GROUP_ALL.") LEFT JOIN ".$collection->getTable('catalog_product_entity')." AS catalog_context_entity ON catalog_context_entity.row_id = catalog_context.row_id INNER JOIN ".$collection->getTable('catalog_product_index_price')." AS catalog_price ON catalog_context_entity.entity_id = catalog_price.entity_id WHERE upsell_product.link_type_id='4' and catalog_price.customer_group_id=".$customerGroupId." GROUP BY upsell_product.product_id ORDER BY catalog_price.final_price)";
			$collection->getSelect()->joinLeft(
				['cat_subsidy'=> new \Zend_Db_Expr($subsidyQuery)],
				'e.entity_id = cat_subsidy.product_id',
				['min_subsidy_price'=>'cat_subsidy.final_price']);
			//MIN SOHO Subsidy Price
			$sohoSubsidyQuery = "(Select upsell_product.product_id,upsell_product.linked_product_id,catalog_entity.row_id AS linked_row_id,MIN(catalog_soho_price.value) AS soho_price FROM ".$collection->getTable('catalog_product_link')." AS upsell_product INNER JOIN ".$collection->getTable('catalog_product_entity')." AS catalog_entity ON upsell_product.linked_product_id = catalog_entity.entity_id LEFT JOIN ".$collection->getTable('catalog_product_entity_varchar')." AS catalog_context ON catalog_context.row_id = catalog_entity.row_id AND catalog_context.attribute_id=".$contextAttributeId." AND (catalog_context.value=".$customerGroupId." OR catalog_context.value=".GroupInterface::CUST_GROUP_ALL.")  LEFT JOIN ".$collection->getTable('catalog_product_entity_decimal')." AS catalog_soho_price ON catalog_soho_price.attribute_id=".$sohoPriceId." AND catalog_context.row_id = catalog_soho_price.row_id WHERE upsell_product.link_type_id='4' GROUP BY upsell_product.product_id ORDER BY catalog_soho_price.value)";
			$collection->getSelect()->joinLeft(
				['cat_soho_subsidy'=> new \Zend_Db_Expr($sohoSubsidyQuery)],
				'e.row_id = cat_soho_subsidy.product_id',
				['min_soho_subsidy_price'=>'cat_soho_subsidy.soho_price']);		  
			$collection->getSelect()->group('dev_handset_family');
			$collection->getSelect()->limit(2);
			$collection->load();
			return $collection;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
	/**
	 * Get Product attribute ID using attribute CODE
	 */
    public function getProductAttributeId($attributeCode) 
	{
        try {
			$objectManager = ObjectManager::getInstance();
			$_eavAttribute = $objectManager->create('\Magento\Eav\Model\ResourceModel\Entity\Attribute');
			$attributeId = $_eavAttribute->getIdByCode('catalog_product', $attributeCode);
			return $attributeId;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
	/**
	 * Get Subscription Product
	 */
    public function getSubscription($subscriptionId) 
	{
        try {
			$objectManager = ObjectManager::getInstance();
			$subscription = $objectManager->create('Magento\Catalog\Model\Product')->load($subscriptionId);
			return $subscription;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}	
    }
	
	/**
	 * Get all families for handsets
	 */
    public function getFamilies() 
	{
        try {
			$familyAttributeId = $this->getProductAttributeId('handset_family');
			$objectManager = ObjectManager::getInstance();
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$connectionDb = $resource->getConnection();
			$tableName =$resource->getTableName('catalog_product_entity_varchar');
			$sql = "Select value FROM " . $tableName . " WHERE attribute_id = " . $familyAttributeId . ";";
			$result = $connectionDb->fetchAll($sql);
			return $result;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
		
	/**
	 * Get Category Details using ID
	 */
    public function getCategory($categoryId) 
	{
        try {
			$objectManager = ObjectManager::getInstance();
			$resource = $objectManager->get('Magento\Catalog\Model\CategoryFactory');
			$category = $resource->create();
			$category->load($categoryId);
			return $category;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
	/**
	 * Get Product Collection by Category ID
	 */
    public function getCategoryProductCollection($categoryId) 
	{
        try {
			$objectManager = ObjectManager::getInstance();
			$resource = $objectManager->get('Magento\Catalog\Model\CategoryFactory');
			$category = $resource->create();
			$category = $category->load($categoryId)->getProductCollection()->addAttributeToSelect('*');
			return $category;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}	
    }
    
    /* Soho Subsidy Tarrif Plans */
    public function getCategorySubProductCollection($categoryId,$customerType) {
        try {
            $objectManager = ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Catalog\Model\CategoryFactory');
            $category = $resource->create();
            if($customerType == 'SOHO'){
                $category = $category->load($categoryId)->getProductCollection()->addAttributeToSelect('*')->addAttributeToSort('subscription_amount', 'DESC');
            }else{
                $category = $category->load($categoryId)->getProductCollection()->addAttributeToSelect('*')->addAttributeToSort('subscription_amount', 'ASC');
            }            
            return $category;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
        }
    }
    /* end */
    
	/**
	 * Get Hero Connected Accessories products
	 */
	public function getConnectedAccessories() 
	{
		try {
			$objectManager = ObjectManager::getInstance();	
			$attribute_code= 'is_hero_accessory_c2c';
			$subscriptionId = $this->getProductAttributeId('subscription_amount');
			$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
			$collection = $productCollection->create();
			$collection->addAttributeToSelect('*');
			$collection->addAttributeToFilter($attribute_code, array('neq' => ''));
			$collection->addAttributeToSort($attribute_code, 'ASC');
			$collection->addAttributeToFilter('status', 1);
			$collection->setPageSize(2);
			$collection->load(); 
			return $collection;
        } 
		catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
	}
	
	/**
	 * Get Hero Accessories products
	 */
    public function getHeroAccessories() 
	{
        try {
			$objectManager = ObjectManager::getInstance();	
			
			$customerGroup = $this->getCustomerTypeName();
			if($customerGroup == 'SOHO')
			{
				$attribute_code= 'is_hero_accessory_soho';
			}
			else
			{
				$attribute_code= 'is_hero_accessory_b2c';
			}
			$subscriptionId = $this->getProductAttributeId('subscription_amount');
			$currentCategoryId = $this->_coreRegistry->registry('current_category')->getId();
			$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
			$collection = $productCollection->create();
			$collection->addAttributeToSelect('*');
			$collection->addAttributeToFilter($attribute_code, array('neq' => ''));
			$collection->addAttributeToSort($attribute_code, 'ASC');
			$collection->addAttributeToFilter('status', 1);
			$collection->addCategoriesFilter(['eq' => $currentCategoryId]);
			$accessorytFamilyId = $this->getProductAttributeId('accessory_family');
			$currentStoreId = $this->_storeManager->getStore()->getId();
			$collection->getSelect()->joinLeft(
				  ['catalog_varchar_hfamily'=>$collection->getTable('catalog_product_entity_varchar')],
				  'e.row_id = catalog_varchar_hfamily.row_id and catalog_varchar_hfamily.store_id = '.$currentStoreId.' and catalog_varchar_hfamily.attribute_id ='.$accessorytFamilyId,
				  ['dev_accessory_family'=>'catalog_varchar_hfamily.value','store_id'=>'catalog_varchar_hfamily.store_id']);

			$collection->getSelect()->group('dev_accessory_family');
			$collection->getSelect()->limit(2);
			$collection->load(); 
			return $collection;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }

	/**
	 *	Get MAX qty of product in accessory family
	 */
    public function getAccessoryFamilyProduct($family) 
	{
        try {
			$objectManager = ObjectManager::getInstance();
			$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
			$collection = $productCollection->create()
				->addAttributeToSelect('*')
				->addAttributeToFilter('accessory_family', $family)			
				->addAttributeToFilter('type_id', 'simple')
				->load();
			$StockState = $objectManager->get('\Magento\CatalogInventory\Api\StockStateInterface');
			$prd = array();
			foreach($collection as $_item) {
				$prd[$_item->getId()] = $StockState->getStockQty($_item->getId(), $_item->getStore()->getWebsiteId());
			}
			$productId= array_search(max($prd), $prd);
			unset($prd);
			$product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
			return $product;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
	/**
	 * Get all available colors(products) within same family
	 */
    public function getAvailableColors($family) 
	{
        try {
			$objectManager = ObjectManager::getInstance();        
			$attribute_code = 'accessory_family';//Can use condition here to get colors for handset
			$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
			$collection = $productCollection->create()
					->addAttributeToSelect('*')
					->addAttributeToFilter('type_id', 'simple')
					->addAttributeToFilter('status', 1)
					->addAttributeToFilter($attribute_code, $family)
					->load();
			return $collection;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
	/**
	 * Returns the total products count for LOB
	 * Core magento has an issue with pagination while using GROUP BY
	 * Bypassed the core issue 
	 */
    private function GetProductsCount($customQuery) 
	{
        try {
			$objectManager = ObjectManager::getInstance();
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$connectionDb = $resource->getConnection();				
			$result = $connectionDb->fetchAll($customQuery);		
			return $result;		
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }

    public function GetListingCount() 
	{
        try {
			return $this->_productCount;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
	/**
	 * Get the Popular products
	 * @Params family,familyType,storeId
	 * @return product Object
	 */
    public function getPopularProduct($family, $familyType, $storeId) 
	{
        try {
			$objectManager = ObjectManager::getInstance();                
			$popularityModel = $objectManager->create('Orange\Priority\Model\Priority');
			$popularProduct = $popularityModel->getPopularProduct($family,$familyType,$storeId);
			return $popularProduct;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
	/** 
	 * Get the product with max inventory within same family
	 * @params family,familyType
	 * @return product Object
	 */
    public function getHighStockProduct($family, $familyType) 
	{
        try {
            if ($familyType == 'accessories') {
				$attribute_code = 'accessory_family';
            } 
			else {
				$attribute_code = 'handset_family';
			}
			$objectManager = ObjectManager::getInstance();
			$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
			$collection = $productCollection->create()
				->addAttributeToSelect('*')
				->addAttributeToFilter($attribute_code, $family)			
				->addAttributeToFilter('type_id', 'simple')
				->addAttributeToFilter('status', '1');
			//Joined Stock table to get MAX stock
			$collection->getSelect()->joinLeft(
				  ['stock'=>$collection->getTable('cataloginventory_stock_item')],
				  'e.entity_id = stock.product_id',
				  ['qty'=>'stock.qty']);
			$collection->getSelect()->order('qty DESC');
			$collection->load();
			return $collection->getFirstItem();
        } 
		catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
	/**
	 * Get Current Store ID
	 */
	public function getCurrentStoreId()
	{
		return $this->_storeManager->getStore()->getId();
	}
	
	/**
	 *	Get Context Or Customer group Id	
	 *
	 */
	public function getCustomerTypeId()
	{
		$objectManager = ObjectManager::getInstance();
		$customerGroupId = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeId();		
		return $customerGroupId;		
	}
	
	/**
	 *	Get Context Name	
	 *
	 */
	public function getCustomerTypeName()
	{
		$objectManager = ObjectManager::getInstance();
		$customerGroupName = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();		
		return $customerGroupName;		
	}
	
	/**
	 *	Get Price Label based on context	
	 *
	 */
	public function getPriceLabel()
	{
		$objectManager = ObjectManager::getInstance();		
		$priceLabel = $objectManager->create('Orange\Catalog\Model\PriceLabel')->getPriceLabel($this->_customerGroup);
		return $priceLabel;		
	}
	
	/**
	 *	Get the context amount	
	 *
	 */
	public function getSubscriptionByType($product)
	{
		$customerGroup = $this->getCustomerTypeName();
		$subscriptionAmount = $product->getSubscriptionAmount();
		return $subscriptionAmount;
	}
	
	/**
	 *	Get Cashback content	
	 *
	 */
	public function getStandAloneCashBack($product)
	{
		$customerGroup = $this->_customerGroup;
		if($customerGroup == 'SOHO')
		{
			$cashbackAmount = $product->getData('cashback_stand_alone_soho');
			if($cashbackAmount ==''){
				$cashbackAmount = $product->getData('cashback_stand_alone_b2c');
			}
		}
		else
		{
			$cashbackAmount = $product->getData('cashback_stand_alone_b2c');
		}
		return $cashbackAmount;
	}
	
	/**
	 *	Get Promotion sticker	
	 *
	 */
	public function getPromotionSticker($product)
	{
		$customerGroup = $this->_customerGroup;
		if($customerGroup == 'SOHO')
		{
			//$promotionSticker = $product->getData('sticker_soho');
			$promotionStickerid = $product->getData('stickers_soho');
			$attr = $product->getResource()->getAttribute('stickers_soho');
               
            $promotionSticker = $attr->getSource()->getOptionText($promotionStickerid);
			if($promotionSticker ==''){
				$promotionStickerid = $product->getData('stickers_b2c');
				$attr = $product->getResource()->getAttribute('stickers_b2c');
				$promotionSticker = $attr->getSource()->getOptionText($promotionStickerid);
			}
		}
		else
		{
			//$promotionSticker = $product->getData('sticker_b2c');
			$promotionStickerid = $product->getData('stickers_b2c');
			$attr = $product->getResource()->getAttribute('stickers_b2c');
               
            $promotionSticker = $attr->getSource()->getOptionText($promotionStickerid);
		}
		return $promotionSticker;
	}
	
	/**
	 * @param Price (INT)
	 * @param Has Price label (BOOLEAN)
	 * @param Show price label (BOOLEAN)
	 * @param Is subscription price (BOOLEAN)
	 * @param Color of price (TEXT)
	 * @param Is Negative (BOOLEAN)
	 * @return HTML
	 */
	public function getOrangePricingHtml($price,$hasPriceLabel=false,$showPriceLabel=false,$isSubscription=false,$priceColor,$isNegative = NULL,$isSohoDiscount=NULL)
	{
		$block = $this->getLayout()->createBlock('Orange\Catalog\Block\Product\PriceView');
		$block->setTemplate('Orange_Catalog::orange_price.phtml');
		$block->setProductPrice($price);
		$block->setIsPriceLabelVisible($hasPriceLabel);
		$block->setShowPriceLabel($showPriceLabel);
		$block->setIsSubscription($isSubscription);
		$block->setIsSohoDiscount($isSohoDiscount);
		$block->setPriceColor($priceColor);
		$block->setIsNegative($isNegative);		
		return $block->toHtml();
	}
	/**
	 *	Get price from simple product of appropriate  bundle products
	 */
	public function getBannerUpSellProduct(\Magento\Catalog\Model\Product $product, $linkedHeroTariff) 
	{
        try {
			$objectManager = ObjectManager::getInstance();
			$upSellProductIds = $product->getUpSellProductIds();
			$productVirtual = array();
			if (!$upSellProductIds) {
				return $productVirtual;
			}
			$bundlehelper = $objectManager->get('Orange\Bundle\Helper\Data');
			if (isset($upSellProductIds)) {
				foreach ($upSellProductIds as $productId) {
					$bundleProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
				    if ($bundleProduct->getTypeId()!="bundle") {
						continue;
					}
					$requiredChildrens = $this->getBannerChildrenProduct($bundleProduct);
					foreach ($requiredChildrens as $requiredChildren) {
						if (strtolower($requiredChildren->getTypeId()) == "virtual" && strtolower($requiredChildren->getSku())== strtolower($linkedHeroTariff)) {
							$productVirtual['price'] = $bundleProduct->getFinalPrice();
							$productVirtual['name'] = $requiredChildren->getName();
							break;
						}
					}
				}
			}
			
			return $productVirtual;
        } 
		catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
	public function getBannerChildrenProduct(\Magento\Catalog\Model\Product $product)
    {
        $pId = $product->getId();
        $objectManager = ObjectManager::getInstance();
        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($pId, true);
        $productIds = array();
        foreach ($requiredChildrenIds as $ids) {
            $productIds[] = $ids;
        }
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $productIds))
            ->addAttributeToFilter('type_id', 'virtual')
            ->load();
        return $collection;
    }

	public function getColorOption($product)
	{
		$attr = $product->getResource()->getAttribute('color');
		$optionText = '';
		if ($attr->usesSource()) {
			$colorcode = $product->getColor();
			$optionText = $attr->getSource()->getOptionText($colorcode);
		}
		return $optionText;
	}

}

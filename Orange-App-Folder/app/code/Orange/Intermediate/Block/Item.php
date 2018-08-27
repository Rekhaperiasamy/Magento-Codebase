<?php
namespace Orange\Intermediate\Block;

use Magento\Framework\App\ObjectManager;
use Magento\Customer\Api\Data\GroupInterface;

class Item extends \Magento\Framework\View\Element\Template
{	
	protected $_storeManager;  
	protected $_categoryFactory;	
	protected $_category;
	protected $_reviewFactory;
	protected $_productRepository;
	protected $_productCollectionFactory;
	protected $_productAttributeRepository;
	protected $_intermediateCollection;
	protected $_eavAttribute;
	protected $_resources;
	protected $_bundleSelection;
	private $_parentProducts = array();
	private $_parentProductsEntity = array();
	private $_customerGroup;
	private $_customerGroupId;
	protected $_productloader;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        
		\Magento\Catalog\Model\CategoryFactory $categoryFactory, 
		\Magento\Review\Model\ReviewFactory $reviewFactory, 
		\Magento\Catalog\Model\ProductRepository $productRepository,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository,
		\Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
		\Magento\Bundle\Model\Product\Type $bundleSelection,
		\Magento\Catalog\Model\ProductFactory $_productloader
		
    )
    {        
        $this->_storeManager = $context->getStoreManager();
		$this->_categoryFactory = $categoryFactory;
		$this->_reviewFactory = $reviewFactory;
		$this->_productRepository = $productRepository;
		$this->_productAttributeRepository = $productAttributeRepository;
		$this->_productCollectionFactory = $productCollectionFactory;	
		$this->_eavAttribute = $eavAttribute;
		$this->_bundleSelection = $bundleSelection;
		$this->_customerGroup = $this->getCustomerGroup();
		$this->_customerGroupId = $this->getCustomerTypeId();
		$this->_productloader = $_productloader;
        parent::__construct($context);
    }
	
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}
	
	protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_write');
        }
        return $this->connection;
    }
	
	/**
     * Get store identifier
     *
     * @return  Integer
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
	
	/**
     * Retrieve the Intermediate Product Id
     * Virtual Product Id
     * @return Integer
     */
    private function _getId()
    {
        return $this->getIntermediateId();
    }
	
	/**
     * Get the sort order of collection
     * 
     * @return string
     */
	private function _getSort()
	{
		return $this->getSort();
	}
	
	/**
     * Get the intermediate product instance
     * 
     * @return Object
     */
	public function getIntermediateProduct()
	{
		$intermediateId = $this->_getId();
		$objectManager = ObjectManager::getInstance();
		$product = $objectManager->create('Magento\Catalog\Model\Product')->load($intermediateId);
		return $product;
	}
	public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }
	/**
     * Get the Upsell products of intermediate product
     * Bundled products
     * @return Object
     */
	public function getIntermediateUpsell()
	{
		if ($this->_intermediateCollection === null) {
			$intermediateId = $this->_getId();			
			$intermediateProduct = $this->getIntermediateProduct();		
			$upSellProductIds = $intermediateProduct->getUpSellProducts();
			$contextVisibility = $this->getProductAttributeId('context_visibility');
			$upsell = array();
			foreach($upSellProductIds as $upsellprod):
				$upsell[]= $upsellprod->getId();
			endforeach;
			if(count($upsell) > 0){							
				$collection = $this->_productCollectionFactory->create()
					->addAttributeToSelect('*')									
					->addAttributeToFilter('entity_id', array('in' => $upsell))	
					->addAttributeToFilter('handset_family', array('neq' => ''))	
					->addAttributeToFilter(
						array(							
							array('attribute'=> 'context_visibility','null' => true),
							array('attribute'=> 'context_visibility','eq' => $this->getCustomerTypeId()),
							array('attribute'=> 'context_visibility','eq' => GroupInterface::CUST_GROUP_ALL)
						)
					); 
				$this->_intermediateCollection = $collection;				
			}
		}
		return $this->_intermediateCollection;
	}
	
	/**
	 * Get all the associated products 
	 */
	public function getIntermediateCollection()
	{
		$upsellCollection = $this->getIntermediateUpsell();	//bundle products
		$subscriptionId = $this->getProductAttributeId('subscription_amount');	
		$sohoPriceId = $this->getProductAttributeId('soho_price');
		$sortorder = $this->_getSort();
		$familyProducts = array();
		$parentProducts = array();
		$parentProductsEntity = array();	
		$childs = array();		
		foreach($upsellCollection as $upsell)
		{
			
			$childProducts = $upsell->getTypeInstance(true)->getChildrenIds($upsell->getId(), false);
			foreach($childProducts as $child)
			{
				$handsets = array();
				foreach($child as $siblings => $entityVal)
				{
					$childs[]=$entityVal;
					$handsets[]=$entityVal;
				}				
			}
			if (!array_key_exists($upsell->getHandsetFamily(), $familyProducts))
			{
			    $handsetFamilys[$upsell->getHandsetFamily()][] = $upsell->getId();
				//$parentProductss[$upsell->getHandsetFamily()]=$upsell->getRowId();//Store bundled product with family in array
				//$parentProductsEntityss[$upsell->getHandsetFamily()]=$upsell->getId();//Store bundled product with family in array
			}
		}
		foreach ($handsetFamilys as $key => $value) {
		    $rowId = $this->highstockAvilableBundleProduct($key,$value);
			if (isset($rowId[0])) {
				$parentProducts[$key] = $rowId[0];
			}
			if (isset($rowId[1])) {
				$parentProductsEntity[$key]= $rowId[1];
			}
		}
		$this->_parentProducts = $parentProducts;
		$this->_parentProductsEntity = $parentProductsEntity;
		$parentIds = implode(",",array_values($parentProducts));
		$parentEntityIds = implode(",",array_values($parentProductsEntity));
		$childs = array_unique($childs); //Simple products				
		$families = $this->getFamilies();// All handset families			
		foreach($families as $family)
		{			
			$handsetfamily[] = array('like' => $family['value'].'%'); // form array to perform filter
		}
		
		
		$currentStoreId = $this->getStoreId();
		$today = time();	
		$popularityHours = ObjectManager::getInstance()->create('Orange\Priority\Helper\Data')->getDevicePopularityDuration();
		$last = $today - (60*60*$popularityHours);
		$from = date("Y-m-d", $last);
		$collection = $this->_productCollectionFactory->create()
					->addAttributeToSelect('*');		
		$collection->addAttributeToFilter('entity_id', array('in' => $childs))
					->addFieldToFilter('handset_family', $handsetfamily)
                    ->addAttributeToFilter('status', 1)
					->addAttributeToFilter('type_id', 'simple');
		//Get Popularity for each family
		$popularQuery = "(SELECT COUNT(family_popular.id) AS popularity, family_popular.family,family_popular.logged_at FROM ".$collection->getTable('catalog_product_family_popularity')." AS family_popular where family_popular.logged_at >= '".$from."' and family_popular.store_id=".$this->getStoreId()."  GROUP BY family_popular.family)";			
		$collection->getSelect()->joinLeft(
		['family_popularity'=> new \Zend_Db_Expr($popularQuery)],
		'at_handset_family.value = family_popularity.family',
		['popularity'=>'family_popularity.popularity']);
		//Get priority of each family
		$collection->getSelect()->joinLeft(
		  ['family_priority'=>$collection->getTable('family_priority')],
		  'at_handset_family.value = family_priority.family_name',
		  ['priority'=>'family_priority.priority']);
		if($parentIds) {
			//Get Parent Row ID of each simple product within same family
			$collection->getSelect()->join(
			  ['catalog_bundle'=>$collection->getTable('catalog_product_bundle_selection')],
			  'e.entity_id = catalog_bundle.product_id and catalog_bundle.parent_product_id IN('.$parentIds.')',
			  ['parent_id'=>'catalog_bundle.parent_product_id']);
			//Get Parent Entity ID of each simple product within same family
			$collection->getSelect()->join(
			  ['catalog_parent_entity'=>$collection->getTable('catalog_product_entity')],
			  'catalog_bundle.parent_product_id = catalog_parent_entity.row_id',
			  ['parent_entity_id'=>'catalog_parent_entity.entity_id']);
			//Joined price table to sort using bundled product price
			$collection->getSelect()->join(
			  ['catalog_bundle_tier_price'=>$collection->getTable('catalog_product_index_price')],
			  'catalog_bundle_tier_price.entity_id = catalog_parent_entity.entity_id and catalog_bundle_tier_price.customer_group_id ="'.$this->_customerGroupId.'"',
			  ['bundle_price'=>'catalog_bundle_tier_price.final_price']);
			//Joined entity price table to sort using bundled SOHO product price
			$collection->getSelect()->joinLeft(
			  ['catalog_bundle_soho_price'=>$collection->getTable('catalog_product_entity_decimal')],
			  'catalog_bundle_soho_price.attribute_id="'.$sohoPriceId.'" and catalog_bundle_soho_price.row_id = catalog_parent_entity.row_id and catalog_bundle_soho_price.store_id = 0',
			  ['bundle_soho_price'=>'catalog_bundle_soho_price.value']);
			$collection->getSelect()->group('handset_family');
			if($this->_getSort()=='' || strtolower($this->_getSort()) == 'popularity')
			{			
				$collection->getSelect()->order('priority ASC');
				$collection->getSelect()->order('popularity DESC');
			}
			else
			{
				if($this->getCustomerTypeName() == 'SOHO') {
					$collection->getSelect()->order('bundle_soho_price '.$this->_getSort());
				} 
				else {
					$collection->getSelect()->order('bundle_price '.$this->_getSort());
				}
			}
		}
		return $collection;
	}
	/**
     * Get the Simpled products associated with the upsell products
     * Simple products
     * @return Object
     */	
	public function getChildrenProducts(\Magento\Catalog\Model\Product $product)
	{
		$pId = $product->getId();
		$objectManager = ObjectManager::getInstance();
		$typeInstance = $product->getTypeInstance();
		$requiredChildrenIds = $typeInstance->getChildrenIds($pId, true);
		$productIds = array();
		foreach ($requiredChildrenIds as $ids) {
			$productIds[] = $ids;
		}
		$sortorder = $this->_getSort();
		$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
		$collection = $productCollection->create()
            ->addAttributeToSelect('*')
			->addAttributeToFilter('entity_id', array('in' => $productIds))
			->addAttributeToFilter('type_id', 'simple')
                           ->addAttributeToFilter('status', 1)
			->addAttributeToSort('price',$sortorder)
            ->load();		
		return $collection;
	}
	
	/**
     * Get the Stock of the product
     * 
     * @return Object
     */	
	public function getStock($product)
	{
		$objectManager = ObjectManager::getInstance();
		$StockState = $objectManager->get('\Magento\CatalogInventory\Api\StockStateInterface');
		return $StockState->getStockQty($product->getId(), $product->getStore()->getWebsiteId());
	}
	
	/**
     * Get the Ratings of the products
     * 
     * @return Object
     */		 
	public function getRatingSummary($product)
	{
		$objectManager = ObjectManager::getInstance();	
		$productId = $product->getId();
		$product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);		
		$storeId = $this->getStoreId();
		$this->_reviewFactory->create()->getEntitySummary($product, $storeId);
		$ratingSummary = $product->getRatingSummary();		
		return $ratingSummary;
	}
		
	/**
     * Get category object
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory($categoryId = false) 
    {
		if (!$categoryId) {
			$product = $this->getIntermediateProduct();
			$cats = $product->getCategoryIds();			
			$this->_category = $this->_categoryFactory->create();			
			$this->_category->load($cats[2]); 			
			return $this->_category;
		}
		else {
			$category = $this->_categoryFactory->create();
			$category->load($categoryId); 
			return $category;
		}        	
	}
		
	/**
     * Get parent category object
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getParentCategory($categoryId = false)
    {
        if ($this->_category) {
            return $this->_category->getParentCategory();
        } else {
            return $this->getCategory($categoryId)->getParentCategory();        
        }        
    }
    
    /**
     * Get parent category identifier
     *
     * @return int
     */
    public function getParentId($categoryId = false)
    {
        if ($this->_category) {
            return $this->_category->getParentId();
        } else {
            return $this->getCategory($categoryId)->getParentId();
        }
    }
    
    /**
     * Get all parent categories ids
     *
     * @return array
     */
    public function getParentIds($categoryId = false)
    {
        if ($this->_category) {
            return $this->_category->getParentIds();
        } else {
            return $this->getCategory($categoryId)->getParentIds();
        }        
    }
    
    /**
     * Get all children categories IDs
     *
     * @param boolean $asArray return result as array instead of comma-separated list of IDs
     * @return array|string
     */
    public function getAllChildren($asArray = false, $categoryId = false)
    {
        return $this->getCategory($categoryId)->getAllChildren($asArray);
    }
 
    /**
     * Retrieve children ids comma separated
     *
     * @return string
     */
    public function getChildren($categoryId = false)
    {
        return $this->getCategory($categoryId)->getChildren();       
    }  
	
	/**
     * Retrieve Product URL
     *
     * @return string
     */
	public function getProductUrl($product)
	{		
		$product = $this->_productRepository->getById($product->getId());
		return $product->getUrlModel()->getUrl($product);
	}
	
	/**
     * Retrieve Device Family
     *
     * @return String
     */
	public function getDeviceFamilyText($product)
	{
		$attribute_code = 'handset_family';
		$objectManager = ObjectManager::getInstance();	
		$productId = $product->getId();
		$product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);			
		$deviceFamilyText = $product->getResource()->getAttribute($attribute_code)->getFrontend()->getValue($product);
		//$deviceFamily = $product->getResource()->getAttribute($attribute_code)->getSource()->getOptionId($deviceFamilyText); // option id
		return $deviceFamilyText;
	}
	
	/**
     * Retrieve Available Colours
     *
     * @return Object
     */
	public function getAvailableColors($devicefamily)
	{
		$objectManager = ObjectManager::getInstance();
		$attribute_code = 'handset_family';
		$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
		$collection = $productCollection->create()
            ->addAttributeToSelect('*')			
			->addAttributeToFilter('type_id', 'simple')
                           ->addAttributeToFilter('status', 1)
			->addAttributeToFilter('handset_family', $devicefamily)			
            ->load();		
		return $collection;		
	}
	
	public function getBrands()
	{
		$brands = $this->_productAttributeRepository->get('brand')->getOptions();
		return $brands;
	}
	
	public function getFamilies()
	{
		$familyAttributeId = $this->getProductAttributeId('handset_family');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connectionDb = $resource->getConnection();
		$tableName =$resource->getTableName('catalog_product_entity_varchar');
		$sql = "Select value FROM " . $tableName . " WHERE attribute_id = " . $familyAttributeId . ";";
		$result = $connectionDb->fetchAll($sql);
		return $result;
		
	}
	
	public function getProductAttributeId($attributeCode)
	{
		$attributeId = $this->_eavAttribute->getIdByCode('catalog_product', $attributeCode);
		return $attributeId;
	}
	
	/**
	* Retrieve parent ids array by required child
	*
	* @param int|array $childId
	* @return array
	*/
	public function getParentIdsByChild($childId)
	{
		$parentIds = $this->_bundleSelection->getParentIdsByChild($childId);		
		return $parentIds;
	}
	
	public function getParentProductId($family)
	{
		$parent = $this->_parentProductsEntity;
		$parentFamily = '';
		if (array_key_exists($family, $parent))
		{
			$parentFamily = $parent[$family];		
		}
		return $parentFamily;		
	}
	
	public function getParentProduct($family)
	{
		$parentId = $this->getParentProductId($family);
		$product = '';
		if($parentId!='')
		{
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$product = $objectManager->create('Magento\Catalog\Model\Product')->load($parentId);			
		}
		return $product;
	}
	
	/**
     * For Bundled Product Colors details
     * 
     */
    public function getBundledAvailableColors($devicefamily) {
		$collection = array();
		$attribute_code = 'handset_family';				
		$intermediateId = $this->_getId();			
		$intermediateProduct = $this->getIntermediateProduct();
		$upSellProductIds = $intermediateProduct->getUpSellProductIds();						
		if(count($upSellProductIds) > 0){
			$collection = $this->_productCollectionFactory->create()
				->addAttributeToSelect('*')									
				->addAttributeToFilter('entity_id', array('in' => $upSellProductIds))
				->addAttributeToFilter($attribute_code, $devicefamily)
				->addAttributeToFilter(
                    array(                          
                        array('attribute'=> 'context_visibility','null' => true),
                        array('attribute'=> 'context_visibility','eq' => $this->getCustomerTypeId()),
                        array('attribute'=> 'context_visibility','eq' => GroupInterface::CUST_GROUP_ALL)
                    )
                );
		}
        return $collection;
    }
	
	public function getHandsetFamilyProduct($handset)
	{
		$objectManager = ObjectManager::getInstance();
		$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
		$collection = $productCollection->create()->addAttributeToSelect('*')
						->addAttributeToFilter('handset_family', $handset)
                                                ->addAttributeToFilter('status', 1)
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
	}
	
	public function getCustomerGroup()
	{
		$objectManager = ObjectManager::getInstance();
		$customerGroup = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();
		return $customerGroup;		
	}
	
	public function getCustomerTypeId()
	{
		$objectManager = ObjectManager::getInstance();
		$customerGroupId = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeId();		
		return $customerGroupId;		
	}
	
	public function getPriceLabel()
	{
		$objectManager = ObjectManager::getInstance();		
		$priceLabel = $objectManager->create('Orange\Catalog\Model\PriceLabel')->getPriceLabel($this->_customerGroup);
		return $priceLabel;		
	}
	
	public function getSubscriptionByType($product)
	{
		$customerGroup = $this->_customerGroup;
		$subscriptionAmount = $product->getSubscriptionAmount();
		return $subscriptionAmount;
	}
	public function getBeadcrumb($product)
	{
        $cats = $product->getCategoryIds();
		$collection = $this->_categoryFactory->create()->load($cats[0])->getProductCollection()
							->addAttributeToSelect('*')
							->addAttributeToFilter('is_subsidy', 1)
							->addAttributeToFilter('type_id', 'virtual');
		return $collection;
	}
	public function getSubsdidyCashBack($product)
	{
		$customerGroup = $this->_customerGroup;
		if($customerGroup == 'SOHO')
		{
			$cashbackAmount = $product->getData('cashback_subsidy_soho');
			if($cashbackAmount =='') {
				$cashbackAmount = $product->getData('cashback_subsidy_b2c');
			}
		}
		else
		{
			$cashbackAmount = $product->getData('cashback_subsidy_b2c');
		}
		return $cashbackAmount;
	}
    public function getPromotionSticker($product){
        $customerGroup = $this->_customerGroup;
        if($customerGroup == 'SOHO')
		{
			//$promotionSticker = $product->getData('sticker_soho');
			$promotionStickerid = $product->getData('stickers_soho');
			$attr = $product->getResource()->getAttribute('stickers_soho');
            $promotionSticker = $attr->getSource()->getOptionText($promotionStickerid);
			if($promotionSticker =='') {
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
	
	public function getCustomerTypeName()
	{
		$objectManager = ObjectManager::getInstance();
		$customerGroupName = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();		
		return $customerGroupName;		
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
	 * Get the product with max inventory within same family
	 * @params family,familyType
	 * @return product Object
	 */
    public function getHighStockProduct($family, $familyType) {
        try {
            if ($familyType == 'accessories') {
			$attribute_code = 'accessory_family';
            } else {
			$attribute_code = 'handset_family';
			}
			$objectManager = ObjectManager::getInstance();
			$intermediateId = $this->_getId();			
			$intermediateProduct = $this->getIntermediateProduct();		
			$upSellProductIds = $intermediateProduct->getUpSellProducts();
			$upsell = array();
			
			$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
			$collection = $productCollection->create()
				->addAttributeToSelect('*')
				->addAttributeToFilter('entity_id', array('in' => $upsell))	
				->addAttributeToFilter($attribute_code, $family)			
				->addAttributeToFilter('type_id', 'simple');
			//Joined Stock table to get MAX stock
			$collection->getSelect()->joinLeft(
				  ['stock'=>$collection->getTable('cataloginventory_stock_item')],
				  'e.entity_id = stock.product_id',
				  ['qty'=>'stock.qty']);
			$collection->getSelect()->order('qty DESC');
			$collection->load();
			return $collection->getFirstItem();
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
	public function getImage($product, $imageId, $attributes = [])
    {
		$objectManager = ObjectManager::getInstance();
		$imageBuilder = $objectManager->create('\Magento\Catalog\Block\Product\Context');
        return $imageBuilder->getImageBuilder()->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }
	
	public function highstockAvilableBundleProduct($family, $value) {
        try {
            $attribute_code = 'handset_family';
			//echo $attribute_code; exit;
			$objectManager = ObjectManager::getInstance();
			$intermediateId = $this->_getId();			
			$intermediateProduct = $this->getIntermediateProduct();		
			$upSellProductIds = $intermediateProduct->getUpSellProducts();
			$upsell = array();
			$totalBundles = array();
			$bundleCollectionIds = array();
			$childs = array();
			
			if(count($value) > 0) {
				$ids = $value;
				$bundleproductCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
				$bundleCollection = $bundleproductCollection->create()
						->addAttributeToSelect('*')
						->addAttributeToFilter('entity_id', array('in' => $ids))	
						->addAttributeToFilter($attribute_code, $family);
				$totalBundles = array();
				foreach($bundleCollection as $upsell) {
					$childProducts = $upsell->getTypeInstance(true)->getChildrenIds($upsell->getId(), false);
					foreach($childProducts as $child)
					{
						$handsets = array();
						foreach($child as $siblings => $entityVal)
						{
							$childs[] = $entityVal;
							$totalBundles[$upsell->getId()][] = $entityVal;
						}				
					}
				}
			}
			$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
			$collection = $productCollection->create()
				->addAttributeToSelect('*')
				->addAttributeToFilter('entity_id', array('in' => $childs))	
				->addAttributeToFilter($attribute_code, $family)			
				->addAttributeToFilter('type_id', 'simple');
			//Joined Stock table to get MAX stock
			$collection->getSelect()->joinLeft(
				  ['stock'=>$collection->getTable('cataloginventory_stock_item')],
				  'e.entity_id = stock.product_id',
				  ['qty'=>'stock.qty']);
			$collection->getSelect()->order('qty DESC');
			$collection->load();
			$stockItemId = $collection->getFirstItem()->getId();
			$bundleId = '';
			foreach($totalBundles as $key=>$values) {
				foreach($values as $val) {
					if ($val == $collection->getFirstItem()->getId()) {
						$bundleId = $key;
					}
				}
			}
			$bundleCollectionIds = array();
			if ($bundleId) {
				$bundle = $this->getLoadProduct($bundleId);
				$bundleCollectionIds[0] = $bundle->getRowId();
				$bundleCollectionIds[1] = $bundle->getId();
			}
			return $bundleCollectionIds;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
}
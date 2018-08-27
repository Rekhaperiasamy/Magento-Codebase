<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Orange\Catalog\Block\Product;

use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Catalog\Model\Product;
/**
 * Product View block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomView extends AbstractProduct {

    protected $_customerGroup;
    protected $storeManager;
	protected $_productRepository;
	protected $_productFactory;
    public function __construct(
	ProductFactory $productFactory,
		\Magento\Catalog\Model\ProductRepository $productRepository,
		  \Magento\Catalog\Block\Product\Context $context ,	
	  
            array $data = array()) {
       		$this->_productRepository = $productRepository;
		$this->_productFactory = $productFactory;
		 $this->storeManager = $context->getStoreManager();
		parent::__construct($context, $data);
		
		
    }
	
    
    public function _prepareLayout() {


        if ($this->getRequest()->getControllerName() == "product") {

            $this->getLayout()->createBlock('Magento\Catalog\Block\Breadcrumbs');
            $product = $this->getProduct();
            if (!$product) {
                return parent::_prepareLayout();
            }

            $url = $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]);
            $objectManager = ObjectManager::getInstance();
            $currproduct = $objectManager->get('Magento\Framework\Registry')->registry('current_product');

            if ($currproduct->getTypeId() == 'bundle') {
                $urlInterface = $objectManager->get('Magento\Framework\UrlInterface');
                $url = $urlInterface->getCurrentUrl();
                $this->pageConfig->addRemotePageAsset(
                        $url, 'canonical', ['attributes' => ['rel' => 'canonical']]
                );

                return $this;
            } else {
                $simpleURL = $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]);
                if ($this->getCustomerTypeId() == 4) {
                    if (strpos($simpleURL, '/nl/') !== false) {
                        $simpleURL = str_replace("/nl/", "/nl/zelfstandigen/", $simpleURL);
                        $this->pageConfig->addRemotePageAsset(
                                $simpleURL, 'canonical', ['attributes' => ['rel' => 'canonical']]
                        );
                    } else if (strpos($simpleURL, '/fr/') !== false) {
                        $simpleURL = str_replace("/fr/", "/fr/independants/", $simpleURL);
                        $this->pageConfig->addRemotePageAsset(
                                $simpleURL, 'canonical', ['attributes' => ['rel' => 'canonical']]
                        );
                    }
                } else {
                    $this->pageConfig->addRemotePageAsset(
                            $simpleURL, 'canonical', ['attributes' => ['rel' => 'canonical']]
                    );
                }

                return $this;
            }
        }
    }
	
	public  function getStoreName() {
        return $this->storeManager->getStore()->getName();
    }
	public  function getStoreCode() {
        return $this->storeManager->getStore()->getCode();
    }
	public  function getStoreUrl() {
		return $this->storeManager->getStore()->getBaseUrl();
	}
	 public function getGalleryImages()
    {
	
        $product = $this->getProduct();
        $images = $product->getMediaGalleryImages();
	   //$images = $product->aroundGetMediaGalleryImages();
        if ($images instanceof \Magento\Framework\Data\Collection) {
           // foreach ($images as $image) {
			foreach ($images as $key => $image) {
			if ($image->getMediaType() == 'image') {
                /* @var \Magento\Framework\DataObject $image */
                $image->setData(
                    'small_image_url',
                    $this->_imageHelper->init($product, 'product_page_image_small')
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
                $image->setData(
                    'medium_image_url',
                    $this->_imageHelper->init($product, 'product_page_image_medium')
                        ->constrainOnly(true)->keepAspectRatio(true)->keepFrame(false)
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
                $image->setData(
                    'large_image_url',
                    $this->_imageHelper->init($product, 'product_page_image_large')
                        ->constrainOnly(true)->keepAspectRatio(true)->keepFrame(false)
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
				}
				// if ($image->getMediaType() != 'image') {
				// $images->removeItemByKey($key);
				// }
            }
        }

        return $images;
    }
    public function getAttributeSetName() {
        $attributeSet = ObjectManager::getInstance()
                ->create('\Magento\Eav\Api\AttributeSetRepositoryInterface');

        $attributeSetRepository = $attributeSet->get($this->getProduct()->getAttributeSetId());
        return $attributeSetRepository->getAttributeSetName();
    }
	
	public function getUrlRewrite($availId,$nintendoUrl) {
		$connection = ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION');
		return $connection->fetchAll("SELECT * FROM url_rewrite where entity_id = $availId AND request_path like '%$nintendoUrl%'");
	}
	
    public function getProduct() {
        if (!$this->hasData('product')) {
            if ($this->_coreRegistry->registry('product')->getTypeId() == 'bundle') {
                $simple = $this->simpleProductDetails($this->_coreRegistry->registry('product')->getId());
                $this->setData('product', $simple);
            } else {
                $this->setData('product', $this->_coreRegistry->registry('product'));
            }
        }
        return $this->getData('product');
    }
	
	   public function getClickandReserve() {
       $clickandreserve = $this->getProduct()->getData('click_and_reserve');
	  return $clickandreserve;
    }

    public function simpleProductDetails($id) {
        $objectManager = ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($id);
        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($id, true);
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
        return $collection->getFirstItem();
    }

    /**
     * Retrieve Product Rating Summary
     *
     * @return Object
     */
    public function getRatingSummary() {
        $objectManager = ObjectManager::getInstance();
        $productId = $this->getProduct()->getId();
        $product = $this->getProduct();
        $RatingOb = $objectManager->create("Magento\Review\Model\Review");
        $RatingOb = $objectManager->get("Magento\Review\Model\ReviewFactory");
        $RatingOb->create()->getEntitySummary($product, 1);
        return $product->getRatingSummary();
    }

    /**
     * Retrieve Available Colours
     *
     * @return Object
     */
	public function getPromoDescription($CustomerGroupId,$family)
	{
		$connection = ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION');
		$tableName =  $connection->getTableName('promodescription_promotiondescription');
		$product = $this->getProduct();
		$productType =  $product->getMetaProductTitle();
		$sku         =  $product->getSku();
		$storeId     = $this->getStoreid();
		$sqlPromopriority1 = 'select * from ' . $tableName.' where (sku = "'.$sku.'" and family = "'.$family.'"  and lob_product = "'.$productType.'")  and store_id = '.$storeId.' and (customer_group = "-1" or customer_group = "'.$CustomerGroupId.'" ) order by sku asc,family asc,lob_product asc limit 0,1 ';
		$result = $connection->fetchAll($sqlPromopriority1);
		if(count($result)< 1){
			$sqlPromopriority1 = 'select * from ' . $tableName.' where (sku = "'.$sku.'" and family IS NULL  and lob_product = "'.$productType.'")  and store_id = '.$storeId.' and (customer_group = "-1" or customer_group = "'.$CustomerGroupId.'" ) order by sku asc,family asc,lob_product asc limit 0,1 ';
			$result = $connection->fetchAll($sqlPromopriority1);
		}
		if(count($result)< 1){
		    $sqlPromopriority1 = 'select * from ' . $tableName.' where (sku = "'.$sku.'" and family IS NULL and lob_product IS NULL)  and store_id = '.$storeId.' and (customer_group = "-1" or customer_group = "'.$CustomerGroupId.'" ) order by sku asc,family asc,lob_product asc limit 0,1 ';
		    $result = $connection->fetchAll($sqlPromopriority1);
		}
		if(count($result)< 1){
				$sqlPromopriority1 = 'select * from ' . $tableName.' where (family = "'.$family.'" and sku IS NULL  and lob_product = "'.$productType.'")  and store_id = '.$storeId.' and (customer_group = "-1" or customer_group = "'.$CustomerGroupId.'" ) order by sku asc,family asc,lob_product asc limit 0,1 ';
				$result = $connection->fetchAll($sqlPromopriority1);
		}
		if(count($result)< 1){
		    $sqlPromopriority1 = 'select * from ' . $tableName.' where (sku = "'.$sku.'" and family = "'.$family.'" and lob_product IS NULL)  and store_id = '.$storeId.' and (customer_group = "-1" or customer_group = "'.$CustomerGroupId.'" ) order by sku asc,family asc,lob_product asc limit 0,1 ';
		    $result = $connection->fetchAll($sqlPromopriority1);
		}
		if(count($result)< 1){
			$sqlPromopriority1 = 'select * from ' . $tableName.' where ( family = "'.$family.'" and sku IS NULL and lob_product IS NULL)  and store_id = '.$storeId.' and (customer_group = "-1" or customer_group = "'.$CustomerGroupId.'" ) order by sku asc,family asc,lob_product asc limit 0,1 ';
		    $result = $connection->fetchAll($sqlPromopriority1);
		}
		if(count($result)< 1){
			$sqlPromopriority1 = 'select * from ' . $tableName.' where (lob_product = "'.$productType.'" and family IS NULL and sku IS NULL)  and store_id = '.$storeId.' and (customer_group = "-1" or customer_group = "'.$CustomerGroupId.'" ) order by sku asc,family asc,lob_product asc limit 0,1 ';
			$result = $connection->fetchAll($sqlPromopriority1);
		}
		if(count($result)< 1){
			$sqlPromopriority1 = "select * FROM " . $tableName." where ( family IS NULL and sku IS NULL and lob_product IS NULL) and store_id = ".$storeId." and (customer_group = '-1' or customer_group = '".$CustomerGroupId."' ) order by sku desc,family desc,lob_product desc limit 0,1 ";
			$result = $connection->fetchAll($sqlPromopriority1); 
		} 
		return $result;
	
	}
    public function getAvailableColors() {
        $objectManager = ObjectManager::getInstance();

        if ($this->getAttributeSetName() == "Accessories") {
            $devicefamily = $this->getProduct()->getAccessoryFamily();
            $attribute_code = 'accessory_family';
        } else {
            $devicefamily = $this->getProduct()->getHandsetFamily();
            $attribute_code = 'handset_family';
        }
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('type_id', 'simple')
                ->addAttributeToFilter('status', 1)
                ->addAttributeToFilter($attribute_code, $devicefamily)
                ->load();
        return $collection;
    }

    /**
     * Get the Upsell products
     * Bundled products
     * @return Object
     */
    public function getUpsellProducts() {
        $objectManager = ObjectManager::getInstance();
        $upSellProductIds = $this->getProduct()->getUpSellProductIds();
        $sortorder = 'position';
        if (count($upSellProductIds) > 0) {
            $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
            $collection = $productCollection->create()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id', array('in' => $upSellProductIds))
                    ->addAttributeToFilter('status', 1)
                    ->addAttributeToFilter(
                        array(                          
                            array('attribute'=> 'context_visibility','null' => true),
                            array('attribute'=> 'context_visibility','eq' => $this->getCustomerTypeId()),
                            array('attribute'=> 'context_visibility','eq' => GroupInterface::CUST_GROUP_ALL)
                        )
                    )
                    ->addAttributeToSort($sortorder, 'ASC');
             $customerGroupId = $this->getCustomerTypeId();
            $contextAttributeId = $this->getProductAttributeId('context_visibility');
            $sohoPriceId = $this->getProductAttributeId('soho_price');
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

            return $collection;
        }
		return false;
    }

    /**
     * Get the Subscription product
     * Virtual product
     * @return Object
     */
    public function getSubscriptionProduct($bundledProduct) {
        $objectManager = ObjectManager::getInstance();
        $typeInstance = $bundledProduct->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($bundledProduct->getId(), true);
        $productIds = array();
        foreach ($requiredChildrenIds as $ids) {
            $productIds[] = $ids;
        }
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $productIds))
                ->addAttributeToFilter('status', 1)
                ->addAttributeToFilter('type_id', 'virtual')
                ->load();
        return $collection;
    }

    /**
     * Get the Device product
     * Simple product
     * @return Object
     */
    public function getMobileDeviceProduct($bundledProduct) {
        $objectManager = ObjectManager::getInstance();
        $typeInstance = $bundledProduct->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($bundledProduct->getId(), true);
        $productIds = array();
        foreach ($requiredChildrenIds as $ids) {
            $productIds[] = $ids;
        }
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $productIds))
                ->addAttributeToFilter('status', 1)
                ->addAttributeToFilter('type_id', 'simple');
        $collection->getSelect()->joinLeft(
              ['stock'=>$collection->getTable('cataloginventory_stock_item')],
              'e.entity_id = stock.product_id',
              ['qty'=>'stock.qty','is_in_stock'=>'stock.is_in_stock']);
        $collection->load();
        return $collection;
    }
	

    /**
     * Get the Custom cart url
     * To add all options of bundled product to cart
     * @return Object
     */
    public function getCustomCartUrl($productId) {
        if($productId != ''){
           return $this->getUrl('customcatalog/cart/add/', array('id' => $productId)); 
        }else{
            return "Product ID is Empty";
        }
    }
	
	public function getAddCartUrl($sku) {
        if($sku != ''){
           return $this->getUrl('checkout/cart/add/', array('sku' => $sku)); 
           exit;
        }else{
            return "Product ID is Empty";
        }
    }
	
    public function getAccessoriesCartUrl($productId) {
        if($productId != ''){
        return $this->getUrl('customcatalog/cart/simple/', array('id' => $productId));
        exit;
        }else{
            return "Product ID is Empty";         
        }
    }

    public function getRelatedProducts() 
    {
        $objectManager = ObjectManager::getInstance();
        $relatedProductIds = $this->getProduct()->getRelatedProductIds();
        $sortorder = 'position';
        if (count($relatedProductIds) > 0) {
            $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
            $collection = $productCollection->create()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id', array('in' => $relatedProductIds))
                    ->addAttributeToFilter('status', 1)
                    ->addAttributeToSort($sortorder, 'ASC');

            $customerGroupId = $this->getCustomerTypeId();
            $contextAttributeId = $this->getProductAttributeId('context_visibility');
            $sohoPriceId = $this->getProductAttributeId('soho_price');
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
            return $collection;
        }
    }

    public function getStartingSubscriptionPrice($deviceId) {
        $upsellProducts = $this->getRelatedUpsellProducts($deviceId);
        if (count($upsellProducts) > 0) {
            $subscriptionAmounts = array();
            foreach ($upsellProducts as $product):
                $subscriptions = $this->getSubscriptionProduct($product);
                foreach ($subscriptions as $subscriptionItem):
                    $subscriptionAmounts[] = $subscriptionItem->getSubscriptionAmount();
                endforeach;
            endforeach;
            sort($subscriptionAmounts, SORT_NUMERIC); // sorts from least subscription amount
            return $subscriptionAmounts;
        }
        return false;
    }

    public function getWithSubscriptionPrice($deviceSKU, $virtualSKU) {

        $sku = $virtualSKU . ' + ' . $deviceSKU;
        $objectManager = ObjectManager::getInstance();
        $_helper = $objectManager->create('Orange\Bundle\Helper\Data');
        $prodCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $prodCollection->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', 1)
                ->addFieldToFilter('sku', $sku);

        if (count($collection)) {
            foreach ($collection as $product) {
                $product['name'] = $product->getName();
                $product['id'] = $product->getId();
                //$product['price'] = $product->getPrice();
				if($this->getCustomerTypeName() == 'SOHO') {
					$product['price'] = $product->getSohoPrice();
                }
				else {
					$tierprice = $_helper->getBundleTierPrice($product->getId());
					$product['price'] = $tierprice[0]['tier_price'];
				}
                
            }
            return $product;
        }
    }

    /**
     * Get the RelatedUpsellProducts products
     * Simplle products
     * @return Object
     */
    public function getRelatedUpsellProducts($productId) {
        $objectManager = ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        $upSellProductIds = $product->getUpSellProductIds();
        $sortorder = 'position';
        if (count($upSellProductIds) > 0) {
            $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
            $collection = $productCollection->create()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id', array('in' => $upSellProductIds))
                    ->addAttributeToFilter('status', 1)
                    ->addAttributeToSort($sortorder, 'ASC');
            return $collection;
        }
    }

    /**
     * Get all the Subscription of a device
     * @return Object
     */
    public function getSubscriptions($deviceId) {
        $objectManager = ObjectManager::getInstance();
        $upsellProducts = $this->getRelatedUpsellProducts($deviceId);
        if (count($upsellProducts) > 0) {
            $subscriptions = array();
            foreach ($upsellProducts as $product):
                $subscription = $this->getSubscriptionProduct($product);
                foreach ($subscription as $subscriptionItem):
                    $subscriptions[] = $subscriptionItem->getId();
                endforeach;
            endforeach;
            if (count($subscriptions) > 0) {
                $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
                $collection = $productCollection->create()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('entity_id', array('in' => $subscriptions))
                        ->addAttributeToFilter('status', 1)
                        ->addAttributeToSort('subscription_amount', 'ASC');
                return $collection;
            }
        }
        return false;
    }

    /**
     * For Bundled Product Colors details
     * 
     */
	public function getBundleId() {
		$bundleId = $this->_coreRegistry->registry('product')->getId();
		return $bundleId;
	}
	public function getIntermediateId() {
		$bundleId = $this->_coreRegistry->registry('product')->getId();
		$objectManager = ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($bundleId);
        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($bundleId, true);
        $bundleProductIds ="";
		$intermediateId ="";
        foreach ($requiredChildrenIds as $ids) {
            $bundleProductIds = $ids;
        }
		foreach ($bundleProductIds as $prdIds) {
			 $virProduct = $product = $objectManager->get('Magento\Catalog\Model\Product')->load($prdIds);			
			if($virProduct->getTypeId() == "virtual") {
				$intermediateId = $prdIds;
			}
        }
		return $intermediateId;
	}
    public function getBundledAvailableColors($devicefamily) {
		$intermediateId = $this->getIntermediateId();
		$collection = array();
		$attribute_code = 'handset_family';
		$intermediateProduct = $this->getIntermediateProduct($intermediateId);
		$upSellProductIds = $intermediateProduct->getUpSellProductIds();						
		if(count($upSellProductIds) > 0){
			$objectManager = ObjectManager::getInstance();
			$collection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory')->create()
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
	public function getIntermediateProduct($intermediateId)
	{
		$objectManager = ObjectManager::getInstance();
		$product = $objectManager->create('Magento\Catalog\Model\Product')->load($intermediateId);
		return $product;
	}
    public function getCustomerTypeId() {
        $objectManager = ObjectManager::getInstance();
        $this->_customerGroup = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeId();
        return $this->_customerGroup;
    }

    public function getCustomerTypeName() {
        $objectManager = ObjectManager::getInstance();
        $customerGroupName = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();
        return $customerGroupName;
    }
   
    public function getPriceLabel() {
        $objectManager = ObjectManager::getInstance();
        $customerGroup = $this->getCustomerTypeName();
        $priceLabel = $objectManager->create('Orange\Catalog\Model\PriceLabel')->getPriceLabel($customerGroup);
        return $priceLabel;
    }
	public function getStoreid()
	{
	$objectManager = ObjectManager::getInstance();
//creating instance of store manager
    $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
//getting current store id
   $storeId = $storeManager->getStore()->getId();
   return $storeId; 
	}

    /**
     * Get Subscription Amount based on customer Group
     */
    public function getDeviceSubscriptionAmount($product) {
        $customerGroup = $this->getCustomerTypeName();
        $subscriptionAmount = $product->getSubscriptionAmount();
        return $subscriptionAmount;
    }

    /*
     * for reevoo server side integeration
     */

    public function getReevooReviewServer() {
        /* $objectManager = ObjectManager::getInstance();
          $reevoo = $objectManager->get('Reevoo_ReevooMark')->UnitTest();
          return $reevoo; */
        //$reevoo = new \Reevoo_ReevooMark();            
        //$reevoo->UnitTest(); 
        /* $reevooMark->cssAssets();
          $reevooMark->javascriptAssets(); */
        //return 'ki';
    }
	public function getHighStockProduct($family,$familyType)
	{
		if($familyType=='accessories')
		{
			$attribute_code = 'accessory_family';
		}
		else
		{
			$attribute_code = 'handset_family';
		}
		$objectManager = ObjectManager::getInstance();
		$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
		$collection = $productCollection->create()
            ->addAttributeToSelect('*')
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
	}
	public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }
	public function getStandAloneCashBack($product)
	{
		$customerGroup = $this->_customerGroup;
		if($customerGroup == 'SOHO')
		{
			$cashbackAmount = $product->getData('cashback_stand_alone_soho');
			if($cashbackAmount =='') {
				$cashbackAmount = $product->getData('cashback_stand_alone_b2c');
			}
		}
		else
		{
			$cashbackAmount = $product->getData('cashback_stand_alone_b2c');
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
	public function getSohoDiscountAmount() {
        $objectManager = ObjectManager::getInstance();
        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		$discountSohoPrice = $scopeConfig->getValue('soho/soho_configuration/soho_discount', $storeScope);
		return $discountSohoPrice;
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

}

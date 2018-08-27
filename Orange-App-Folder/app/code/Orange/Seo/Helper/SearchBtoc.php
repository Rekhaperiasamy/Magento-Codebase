<?php

namespace Orange\Seo\Helper;

class SearchBtoc extends \Magento\Framework\App\Helper\AbstractHelper {
    protected $scopeConfig;
    protected $_storeManager;
    protected $_categoryFactory;
    protected $_productFactory;
    protected $_productCollectionFactory;
    protected $productAttributeRepository;
    protected $_messageManager;
	protected $attributeSet;
	protected $stItem;
	protected $_logHelper;
	
	const REPORTPATH = BP.DIRECTORY_SEPARATOR.'pub'.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'searchreports'.DIRECTORY_SEPARATOR;
    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
            \Magento\Framework\Message\ManagerInterface $messageManager,
			\Magento\Framework\App\Filesystem\DirectoryList $directory_list,
            \Magento\Customer\Model\Session $session, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\File\Csv $csvProcessor, \Magento\Catalog\Model\CategoryFactory $categoryFactory,\Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Catalog\Model\ProductRepository $productRepository, \Magento\Catalog\Model\Category $categoryModel, \Magento\Catalog\Model\CategoryRepository $categoryRepository, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
			\Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
			\Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
			\Orange\Upload\Helper\Data $logHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_categoryFactory = $categoryFactory;
        $this->_productFactory = $productFactory;
        $this->_session = $session;
        $this->directory_list = $directory_list;
        $this->scopeConfig = $context->getScopeConfig();
        $this->inlineTranslation = $inlineTranslation;
        $this->csvProcessor = $csvProcessor;
        $this->categoryModel = $categoryModel;
        $this->_messageManager = $messageManager;
        $this->categoryRepository = $categoryRepository;
        $this->_productAttributeRepository = $productAttributeRepository;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productRepository = $productRepository;
		$this->attributeSet = $attributeSet;
		$this->stItem = $stockRegistry;
		//$this->_searchLog 			= $data;
		//$this->_logFile 			= '/var/log/search_b2c.log';
		$this->_logHelper = $logHelper;
		
        parent::__construct($context);
    }    
    
    public function getFeedsFr() {
		/* latest modification */
		$fr_store_id = 2;
		$nl_store_id = 1;
		/* end */
		$baseDir = $this->directory_list->getRoot();
		$date = date("Y-m-d");
		$time_format = date("H:i:s");
		$time_format = str_replace(":", "-", $time_format);
		$filename_format = $date.'-'.$time_format;
		
		$stores_fr = 'fr';
		$languagefr = "french";
		
		
		$stores_nl = 'nl';
		$languagenl = "dutch";
		
		
		$attribute_ids = $this->scopeConfig->getValue('googlefeed_sec/googlefeed_configuration/attributelist', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $attribute_set_id = explode(',', $attribute_ids);
		
		$collection = $this->_productCollectionFactory->create();
		$collection->addAttributeToFilter('attribute_set_id', array("in" => $attribute_set_id));
		$collection->addAttributeToFilter('type_id', array('neq' => 'virtual'));
		$collection->addAttributeToFilter('type_id', array('neq' => 'bundle'));
		$collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
		$collection->addAttributeToSelect('*')->load();
		
		$mediaPath = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		
		$baseurl = $this->_storeManager->getStore()->getBaseUrl();
		$baseurl_fr = $this->_storeManager->getStore($fr_store_id)->getBaseUrl();
		$baseurl_nl = $this->_storeManager->getStore($nl_store_id)->getBaseUrl();
		
		$xml = new \SimpleXMLElement('<products xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="mosse.xsd"></products>');
		$item = $xml->addChild('country','obel');
		foreach ($collection as $prod){
			//$store = $this->_storeManager->getStore();
			$productid = $prod->getId();
			//$stockStatus  = $prod->isAvailable();
			
			//$clear = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($des))))));
				
			   //$productType = $product->getTypeId();
			   //if ($productType != "bundle") 
			   //{
				$product = $this->_productFactory->create()->load($productid);
				$productType = $product->getResource()->getAttribute('product_icon_image')->getFrontend()->getValue($product);
				
			
				$image = $product->getImage();
				$pics = $mediaPath . "catalog/product" . $image;
				/**Replace Pub for PSH Server **/
				if(stripos($pics, '/pub') == true)
				{
				$pics = str_replace("/pub/media", "/media", $pics);
				}
			     //if($stores_fr == 'fr')
			      //{
				   $product_fr = $this->_productFactory->create()->setStoreId($fr_store_id)->load($productid);
				   $product_nl = $this->_productFactory->create()->setStoreId($nl_store_id)->load($productid);
				   /***Product Url with Category ****/
					$status 		= $product->getStatus();
					$category 		= $product->getCategoryIds();
					$url 			= '';
					$productType    = $product->getAttributeText('product_icon_image');
					$category_id ='';
					$cid = '';
					if ($productType == "mobile-wifi") {
						$cid = "26";
					}
					if ($productType == "tablet") {
						$cid = "17";
					}
					if ($productType == "smartphone") {
						$cid = "14";
					}
					if ($productType == "shop-accessories") {
						$cid = "15";
					}
					if ($productType == "connection") {
						$cid = "27";
					}
					if ($productType == "modem") {
						$cid = "26";
					}
					if (in_array($cid, $category)) {
					   $category_id  = $cid;
					}
				   $objectManager 	= \Magento\Framework\App\ObjectManager::getInstance();
				   $nlstore             = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($nl_store_id);
				   $frstore             = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($fr_store_id);
					$resource 		= $objectManager->get('Magento\Framework\App\ResourceConnection');
					$connectionDb 	= $resource->getConnection();
					$tableName 		= $resource->getTableName('url_rewrite');
					if($category_id != ''){
					$targetPath = "catalog/product/view/id/".$productid."/category/".$category_id."";
					$sql = "Select request_path,store_id FROM " . $tableName . " WHERE (entity_type = 'product' and store_id IN (".$fr_store_id.",".$nl_store_id.") and target_path='$targetPath') ;";
					$result = $connectionDb->fetchAll($sql);
	
					if (!empty($result)) {
					   foreach($result as $newresult)
					   {
					    if($newresult['store_id'] == 2)
						{
						$frUrl = $frstore->getBaseUrl().$newresult['request_path'];
						}
						else if($newresult['store_id'] == 1)
						{
						$nlUrl = $nlstore->getBaseUrl().$newresult['request_path'];
						}
                       }						
					} else {			  
					$nlUrl = $nlstore->getBaseUrl().$targetPath;
					$frUrl = $frstore->getBaseUrl().$targetPath;
					}
					}else{			  
					$nlUrl = $product->setStoreId($nl_store_id)->getProductUrl();
					$frUrl = $product->setStoreId($fr_store_id)->getProductUrl();
					}
				   //$cats = $product->getCategoryIds();
				   $stQty = $this->stItem->getStockItem($product_nl->getId());
				   $stat = $stQty->getData('is_in_stock');
				   if($stat == '1') { 
						$stk = "on-stock";
					} else {
						$stk ="out-of-stock";
					}
				   
				   $category = 'mobile';
				   
				   $attributeSetRepository = $this->attributeSet->get($product->getAttributeSetId());
				   $subcat = strtolower($attributeSetRepository->getAttributeSetName());
				   
				   if($subcat =='accessories'){
					   $subcat = 'accessory';
				   }
				   if($productType == 'tablet'){
						$subcat =  '';
				   }
	  
	   		        
				    $item = $xml->addChild('product');
				    $item->addChild('idProduct', $product->getSku()); 
				    
					$langfr = $item->addChild('language');
					$langfr->addAttribute('value',$languagefr);
					
					$langnl = $item->addChild('language');
					$langnl->addAttribute('value',$languagenl);
					
					$itemfr = $item->addChild('productName');
					$itemfr->addAttribute('language',$languagefr);
					$itemfr->addAttribute('name',$product_fr->getName());
					
					$itemnl = $item->addChild('productName');
					$itemnl->addAttribute('language',$languagenl);
					$itemnl->addAttribute('name',$product_nl->getName());
					
					//$item->addChild('language', $languagefr);
					//$item->addChild('language', $languagenl);
				    //$item->addChild('productNamelanguage', $languagefr);
				    //$item->addChild('productName language="french"', $product_fr->getName());
					//$item->addChild('productName language="dutch"', $product_nl->getName());
					
					if (!empty($product->getBrand())) {
						$brandName = $product->getResource()->getAttribute('brand')->getFrontend()->getValue($product);
					} else {
						$brandName = ""; //if no brand is specified then null
					}
					
					$item->addChild('productBrand', $brandName);
					$item->addChild('marketSegment', 'residential');				
					
					$item->addChild('category',$category);
					$item->addChild('subCategory',$subcat);
					
					//$item->addChild('marketSegment', htmlspecialchars(strip_tags($product->getMarketingDescription())));
					//$item->addChild('productShortDescription language="french"', htmlspecialchars(strip_tags($product_fr->getMarketingDescription())));
					//$item->addChild('productShortDescription language="dutch"', htmlspecialchars(strip_tags($product_nl->getMarketingDescription())));
					
					$itemfr_desc = $item->addChild('productShortDescription');
					$itemfr_desc->addAttribute('language',$languagefr);
					$itemfr_desc->addAttribute('description',htmlspecialchars(strip_tags($product_fr->getMarketingDescription())));
					
					$itemnl_desc = $item->addChild('productShortDescription');
					$itemnl_desc->addAttribute('language',$languagenl);
					$itemnl_desc->addAttribute('description',htmlspecialchars(strip_tags($product_nl->getMarketingDescription())));
					
					//$item->addChild('productUrl language="french"', $product_fr->getProductUrl());
					//$item->addChild('productUrl language="dutch"', $product_nl->getProductUrl());
					$product_fr_Url = $baseurl_fr.$product_fr->getUrlKey();
					$itemfr_url = $item->addChild('productUrl');
					$itemfr_url->addAttribute('language',$languagefr);
					$itemfr_url->addAttribute('url',$frUrl);
					
					$product_nl_Url = $baseurl_nl.$product_nl->getUrlKey();
					$itemnl_url = $item->addChild('productUrl');
					$itemnl_url->addAttribute('language',$languagenl);
					$itemnl_url->addAttribute('url',$nlUrl);
					
					$item->addChild('shopUrl',$baseurl);
					
					$item->addChild('productPictureUrl ',$pics);
		/** Check Product has Subsidy if Subsidy then Show Lowest Price of subsidy **/
					$upSellProductIds = $product->getUpSellProductIds();
				  if(count($upSellProductIds) <= 0)
				  {
					$price = $product->getPrice();
				  }
				  else
				  {
				    $uproductsprice = array();
					foreach ($upSellProductIds as $uproductId) {
					$uproduct = $objectManager->get('Magento\Catalog\Model\Product')->load($uproductId);
					$uproductsprice[] = $uproduct->getPrice();;
					unset($uproduct);
					}
					$price = min($uproductsprice);
				  }
					$item->addChild('basketUrl','');
					$item->addChild('price',str_replace('.',',',number_format($price,2)));
					$item->addChild('initialPrice','');
					$item->addChild('referencePrice','');
					$item->addChild('currencyPrice','€');
					$item->addChild('taxPrice','TRUE');
					$item->addChild('reviewGrade','');
					$item->addChild('availabilityStartDate','');
					$item->addChild('availabilityEndDate','');
					$item->addChild('stockLevel',$stk);
					$item->addChild('devicePromotion','');
					$item->addChild('devicePromotionType','');
					$item->addChild('deviceRelativePromotion','');
					$item->addChild('deviceFixedPromotion','');
					$item->addChild('deviceNewPromotionPrice','');
					$item->addChild('devicePromotionStartDate','');
					$item->addChild('devicePromotionEndDate','');
					$item->addChild('planPromotionType','');
					$item->addChild('planFreePeriod','');
					$item->addChild('planRelativeDiscountValue','');
					$item->addChild('planFixedDiscoundValue','');
					$item->addChild('planPromotionStartDate','');
					$item->addChild('planPromotionEndDate','');
					$item->addChild('keywords','');	   
		        //}  
		    //}
    }

	 $name = strftime('obel_shop_production_'.$filename_format.'.xml'); 
	 $path=self::REPORTPATH.$name;
     $content=$xml->asXML();
	 	
	 $realpath = self::REPORTPATH;
	 if (!is_dir($realpath)) {
		mkdir($realpath);
	 }
	
     file_put_contents($path,$content);
     header('Content-disposition: attachment; filename="'.$name.'"');
     header('Content-type: "text/xml"; charset="utf8"');	 
	 echo $content;
	 //header("Expires: 0");
	 $this->ftpPushProduct($path);
	 exit;

	}
     //For Nl xml
	public function getFeedsNl() {
   		$baseDir = $this->directory_list->getRoot();
		$date = date("Y-m-d H:i:s");
	 	$stores = 'nl';
		$languagenl = "dutch";
		$collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*')->load();
		header('Content-Type: text/xml; charset=utf-8', true); //set document header content type to be XML
        $xml = new \SimpleXMLElement('<products xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="mosse.xsd"></products>');   
		$item = $xml->addChild('country','obel');
		foreach ($collection as $product){
			$store = $this->_storeManager->getStore();
			$productid = $product->getId();
			$stockStatus  = $product->isAvailable();
			//$clear = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($des))))));
			if($stockStatus) { 
			$stk = "on-stock";
			 } else {
			$stk ="out-of-stock";
			}
			$productType = $product->getTypeId();
			if ($productType != "bundle") {
			if($stores == 'nl')
			{
			  $product = $this->_productFactory->create()->load($productid);
				$cats = $product->getCategoryIds();
					if(count($cats) ){
						$firstCategoryId = $cats[0];
						$category1 = $this->_categoryFactory->create()->load($firstCategoryId);
							 
					}
				$item = $xml->addChild('product');
				$item->addChild('idProduct', $product->getSku()); 
				$item->addChild('languagevalue', $languagenl);
				$item->addChild('productNamelanguage', $languagenl);
				$item->addChild('name', $product->getName());
				if(isset($category1)){
				$item->addChild('category',$category1->getName());
				}
				$item->addChild('productBrand', $product->getAttributeText('brand'));
				$item->addChild('marketSegment', htmlspecialchars(strip_tags($product->getMarketingDescription())));
				$item->addChild('productShortDescription', htmlspecialchars(strip_tags($product->getShortDescription())));
				$item->addChild('productUrl', $product->getProductUrl());
				$item->addChild('productPictureUrl',$imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage());
				$item->addChild('basketUrl','');
				$item->addChild('price',$product->getPrice());
				$item->addChild('initialPrice','');
				$item->addChild('referencePrice','');
				$item->addChild('currencyPrice','€');
				$item->addChild('taxPrice','TRUE');
				$item->addChild('reviewGrade','');
				$item->addChild('availabilityStartDate','');
				$item->addChild('availabilityEndDate','');
				$item->addChild('stockLevel',$stk);
				$item->addChild('devicePromotion','');
				$item->addChild('devicePromotionType','');
				$item->addChild('deviceRelativePromotion','');
				$item->addChild('deviceFixedPromotion','');
				$item->addChild('deviceNewPromotionPrice','');
				$item->addChild('devicePromotionStartDate','');
				$item->addChild('devicePromotionEndDate','');
				$item->addChild('planPromotionType','');
				$item->addChild('planFreePeriod','');
				$item->addChild('planRelativeDiscountValue','');
				$item->addChild('planFixedDiscoundValue','');
				$item->addChild('planPromotionStartDate','');
				$item->addChild('planPromotionEndDate','');
				$item->addChild('keywords',''); 
   
			}  
		}
		}
		$name = strftime('search_b2c_'.$stores.'.xml'); 
	    $path=self::REPORTPATH.$name;
        $content=$xml->asXML();
		
		$realpath = self::REPORTPATH;
		if (!is_dir($realpath)) {
			mkdir($realpath,0777);
		}
		
        file_put_contents($path,$content);	
        header('Content-disposition: attachment; filename="'.$name.'"');
        header('Content-type: "text/xml"; charset="utf8"');	 
	    echo $content;
	    exit;
    }
	
	public function getFeedsCron() {

		/* latest modification */
		$fr_store_id = 2;
		$nl_store_id = 1;
		/* end */
		$baseDir = $this->directory_list->getRoot();
		$date = date("Y-m-d");
		$time_format = date("H:i:s");
		$time_format = str_replace(":", "-", $time_format);
		$filename_format = $date.'-'.$time_format;
		
		$stores_fr = 'fr';
		$languagefr = "french";
		
		$stores_nl = 'nl';
		$languagenl = "dutch";		
		
		$log_mode = $this->scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		
		$attribute_ids = $this->scopeConfig->getValue('googlefeed_sec/googlefeed_configuration/attributelist', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $attribute_set_id = explode(',', $attribute_ids);
		
		$collection = $this->_productCollectionFactory->create();
		$collection->addAttributeToFilter('attribute_set_id', array("in" => $attribute_set_id));
		$collection->addAttributeToFilter('type_id', array('neq' => 'virtual'));
		$collection->addAttributeToFilter('type_id', array('neq' => 'bundle'));
		$collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
		$collection->addAttributeToSelect('*')->load();
		
		$mediaPath = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$baseurl = $this->_storeManager->getStore()->getBaseUrl();
		$baseurl_fr = $this->_storeManager->getStore($fr_store_id)->getBaseUrl();
		$baseurl_nl = $this->_storeManager->getStore($nl_store_id)->getBaseUrl();
		
		$xml = new \SimpleXMLElement('<products xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="mosse.xsd"></products>');
		$item = $xml->addChild('country','obel');
		foreach ($collection as $prod){
			//$store = $this->_storeManager->getStore();
			$productid = $prod->getId();
			//$stockStatus  = $prod->isAvailable();
			//$clear = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($des))))));
				
			   //$productType = $product->getTypeId();
			   //if ($productType != "bundle") 
			   //{
				$product = $this->_productFactory->create()->load($productid);
				$productType = $product->getResource()->getAttribute('product_icon_image')->getFrontend()->getValue($product);
				
				$image = $product->getImage();
				$pics = $mediaPath . "catalog/product" . $image;
				/***Remove Pub  for PSH server  ******/
				if(stripos($pics, '/pub') == true)
				{
				$pics = str_replace("/pub/media", "/media", $pics);
				}
			     //if($stores_fr == 'fr')
			      //{
				   $product_fr = $this->_productFactory->create()->setStoreId($fr_store_id)->load($productid);
				   $product_nl = $this->_productFactory->create()->setStoreId($nl_store_id)->load($productid);
				   /***Product Url with Category ****/
					$status 		= $product->getStatus();
					$category 		= $product->getCategoryIds();
					$url 			= '';
					$productType    = $product->getAttributeText('product_icon_image');
					$category_id ='';
					$cid = '';
					if ($productType == "mobile-wifi") {
						$cid = "26";
					}
					if ($productType == "tablet") {
						$cid = "17";
					}
					if ($productType == "smartphone") {
						$cid = "14";
					}
					if ($productType == "shop-accessories") {
						$cid = "15";
					}
					if ($productType == "connection") {
						$cid = "27";
					}
					
					if ($productType == "modem") {
						$cid = "26";
					}
				
					if (in_array($cid, $category)) {
					   $category_id  = $cid;
					}
				   $objectManager 	= \Magento\Framework\App\ObjectManager::getInstance();
				   $nlstore             = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($nl_store_id);
				   $frstore             = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($fr_store_id);
					$resource 		= $objectManager->get('Magento\Framework\App\ResourceConnection');
					$connectionDb 	= $resource->getConnection();
					$tableName 		= $resource->getTableName('url_rewrite');
					if($category_id != ''){
					$targetPath = "catalog/product/view/id/".$productid."/category/".$category_id."";
					 $sql = "Select request_path,store_id FROM " . $tableName . " WHERE (entity_type = 'product' and store_id IN (".$fr_store_id.",".$nl_store_id.") and target_path='$targetPath') ;";
					$result = $connectionDb->fetchAll($sql);
	
					if (!empty($result)) {
					   foreach($result as $newresult)
					   {
					    if($newresult['store_id'] == 2)
						{
						$frUrl = $frstore->getBaseUrl().$newresult['request_path'];
						}
						else if($newresult['store_id'] == 1)
						{
						$nlUrl = $nlstore->getBaseUrl().$newresult['request_path'];
						}
                       }						
					} else {			  
					$nlUrl = $nlstore->getBaseUrl().$targetPath;
					$frUrl = $frstore->getBaseUrl().$targetPath;
					}
					}else{			  
					$nlUrl = $product->setStoreId($nl_store_id)->getProductUrl();
					$frUrl = $product->setStoreId($fr_store_id)->getProductUrl();
					}
				   $stQty = $this->stItem->getStockItem($product_nl->getId());
				   $stat = $stQty->getData('is_in_stock');
				   if($stat == '1') { 
						$stk = "on-stock";
					} else {
						$stk ="out-of-stock";
					}
				   
				   $category = 'mobile';
				   
				   //$cats = $product->getCategoryIds();
				   
				   $category = 'mobile';
				   
				   $attributeSetRepository = $this->attributeSet->get($product->getAttributeSetId());
				   $subcat = strtolower($attributeSetRepository->getAttributeSetName());
				   
				   if($subcat=='accessories'){
					   $subcat = 'accessory';
				   }
				   if($productType == 'tablet'){
						$subcat =  '';
				   }
				   
					//if(count($cats) )
					//{
						//if(isset($cats[0]))
						//{
							//$firstCategoryId = $cats[0];
							//$category1 = $this->_categoryFactory->create()->load($firstCategoryId);
						//}						
						//if(isset($cats[1]))
						//{
							//$secondCategoryId = $cats[1];
							//$category2 = $this->_categoryFactory->create()->load($secondCategoryId);
						//}						 
					//}  
	   		        
				    $item = $xml->addChild('product');
				    $item->addChild('idProduct', $product->getSku()); 
				    
					$langfr = $item->addChild('language');
					$langfr->addAttribute('value',$languagefr);
					
					$langnl = $item->addChild('language');
					$langnl->addAttribute('value',$languagenl);
					
					$itemfr = $item->addChild('productName');
					$itemfr->addAttribute('language',$languagefr);
					$itemfr->addAttribute('name',$product_fr->getName());
					
					$itemnl = $item->addChild('productName');
					$itemnl->addAttribute('language',$languagenl);
					$itemnl->addAttribute('name',$product_nl->getName());
					
					//$item->addChild('language', $languagefr);
					//$item->addChild('language', $languagenl);
				    //$item->addChild('productNamelanguage', $languagefr);
				    //$item->addChild('productName language="french"', $product_fr->getName());
					//$item->addChild('productName language="dutch"', $product_nl->getName());
					
					if (!empty($product->getBrand())) {
						$brandName = $product->getResource()->getAttribute('brand')->getFrontend()->getValue($product);
					} else {
						$brandName = ""; //if no brand is specified then null
					}
					
					$item->addChild('productBrand', $brandName);
					$item->addChild('marketSegment', 'residential');
					
					$item->addChild('category',$category);
					
					$item->addChild('subCategory',$subcat);
					
					//$item->addChild('marketSegment', htmlspecialchars(strip_tags($product->getMarketingDescription())));
					//$item->addChild('productShortDescription language="french"', htmlspecialchars(strip_tags($product_fr->getMarketingDescription())));
					//$item->addChild('productShortDescription language="dutch"', htmlspecialchars(strip_tags($product_nl->getMarketingDescription())));
					
					$itemfr_desc = $item->addChild('productShortDescription');
					$itemfr_desc->addAttribute('language',$languagefr);
					$itemfr_desc->addAttribute('description',htmlspecialchars(strip_tags($product_fr->getMarketingDescription())));
					
					$itemnl_desc = $item->addChild('productShortDescription');
					$itemnl_desc->addAttribute('language',$languagenl);
					$itemnl_desc->addAttribute('description',htmlspecialchars(strip_tags($product_nl->getMarketingDescription())));
					
					//$item->addChild('productUrl language="french"', $product_fr->getProductUrl());
					//$item->addChild('productUrl language="dutch"', $product_nl->getProductUrl());
					$product_fr_Url = $baseurl_fr.$product_fr->getUrlKey();
					$itemfr_url = $item->addChild('productUrl');
					$itemfr_url->addAttribute('language',$languagefr);
					$itemfr_url->addAttribute('url',$frUrl);
					
					$product_nl_Url = $baseurl_fr.$product_nl->getUrlKey();
					$itemnl_url = $item->addChild('productUrl');
					$itemnl_url->addAttribute('language',$languagenl);
					$itemnl_url->addAttribute('url',$nlUrl);
					
					$item->addChild('shopUrl',$baseurl);
					
					$item->addChild('productPictureUrl ',$pics);
					
					/** Check Product has Subsidy if Subsidy then Show Lowest Price of subsidy **/
					$upSellProductIds = $product->getUpSellProductIds();
				  if(count($upSellProductIds) <= 0)
				  {
					$price = $product->getPrice();
				  }
				  else
				  {
				    $uproductsprice = array();
					foreach ($upSellProductIds as $uproductId) {
					$uproduct = $objectManager->get('Magento\Catalog\Model\Product')->load($uproductId);
					$uproductsprice[] = $uproduct->getPrice();;
					unset($uproduct);
					}
					$price = min($uproductsprice);
				  }
					
					$item->addChild('basketUrl','');
					$item->addChild('price',str_replace('.',',',number_format($price,2)));
					$item->addChild('initialPrice','');
					$item->addChild('referencePrice','');
					$item->addChild('currencyPrice','€');
					$item->addChild('taxPrice','TRUE');
					$item->addChild('reviewGrade','');
					$item->addChild('availabilityStartDate','');
					$item->addChild('availabilityEndDate','');
					$item->addChild('stockLevel',$stk);
					$item->addChild('devicePromotion','');
					$item->addChild('devicePromotionType','');
					$item->addChild('deviceRelativePromotion','');
					$item->addChild('deviceFixedPromotion','');
					$item->addChild('deviceNewPromotionPrice','');
					$item->addChild('devicePromotionStartDate','');
					$item->addChild('devicePromotionEndDate','');
					$item->addChild('planPromotionType','');
					$item->addChild('planFreePeriod','');
					$item->addChild('planRelativeDiscountValue','');
					$item->addChild('planFixedDiscoundValue','');
					$item->addChild('planPromotionStartDate','');
					$item->addChild('planPromotionEndDate','');
					$item->addChild('keywords','');	   
		        //}  
		    //}
    }
	 $name = strftime('obel_shop_production_'.$filename_format.'.xml'); 
	 $path=self::REPORTPATH.$name;
     $content=$xml->asXML();
	 
	 $realpath = self::REPORTPATH;
	 if (!is_dir($realpath)) {
		mkdir($realpath,0777);
	 }
	 
	 file_put_contents($path,$content);
     header('Content-disposition: attachment; filename="'.$name.'"');
     header('Content-type: "text/xml"; charset="utf8"');	 
	 chmod($path,0777);
	 file_get_contents($path);	 
	 //echo $content;
	 //header("Expires: 0");
	 //$this->ftpPushProduct($path);
	 
	 try{
			$info = $this->ftpDetails();
			
			$ftp_server = $info['ftp_server'];
			$ftp_user_name = $info['ftp_user_name'];
			$ftp_user_pass = $info['ftp_user_pass'];
			$ftp_path = $info['ftp_path'];
			
			
			// set up basic connection
			$conn_id = ftp_connect($ftp_server);
			if (false === $conn_id) {
				throw new Exception("FTP connection error!");
			}            
			// login with username and password
			$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
				
			// Switch to passive mode.
			ftp_pasv($conn_id, TRUE);
				
			ftp_chdir($conn_id, $ftp_path);
			// upload a file
			$file_name = basename($path);
			if(isset($log_mode) && $log_mode==1){			
				$this->_logHelper->logCreate('/var/log/SearchReport_B2C.log','Search Report file:'.$file_name);
			}
			if (ftp_put($conn_id, basename($path), $path, FTP_ASCII)) {
				//echo "successfully uploaded $file\n";				
			} else {
				//echo "There was a problem while uploading $file\n";
				if(isset($log_mode) && $log_mode==1){
					$this->_logHelper->logCreate('/var/log/SearchReport_B2C_Error.log','There was a problem while uploading'.$file_name);
				}
			}
			// close the connection		
			ftp_close($conn_id);
		}catch(\Exception $e){
			if(isset($log_mode) && $log_mode==1){
				$this->_logHelper->logCreate('/var/log/SearchReport_B2C_Error.log','There was a Exception:'.$e->getMessage());
			}
	 }
	 
	 exit;
	}
	
	private function ftpPushProduct($file){
        
            $info = $this->ftpDetails();
         
            $ftp_server = $info['ftp_server'];
            $ftp_user_name = $info['ftp_user_name'];
            $ftp_user_pass = $info['ftp_user_pass'];
			$ftp_path = $info['ftp_path'];
            
            // set up basic connection
            $conn_id = ftp_connect($ftp_server);
            if (false === $conn_id) {
                throw new Exception("FTP connection error!");
            }            
            // login with username and password
            $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
			
			// Switch to passive mode.
			ftp_pasv($conn_id, TRUE);
			
			ftp_chdir($conn_id, $ftp_path);
            // upload a file
			if (ftp_put($conn_id, basename($file), $file, FTP_ASCII)) {
				//echo "successfully uploaded $file\n";
				
            } else {
				//echo "There was a problem while uploading $file\n";
				
			}
			// close the connection
			ftp_close($conn_id); 
		
	}
		
	public function ftpDetails() {
		$Credentials = array();
		$Credentials['ftp_server'] = $this->scopeConfig->getValue('productsearch_ftpdetails/productsearch__configuration/productsearch_ftp_server', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$Credentials['ftp_user_name'] = $this->scopeConfig->getValue('productsearch_ftpdetails/productsearch__configuration/productsearch_user_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$Credentials['ftp_user_pass'] = $this->scopeConfig->getValue('productsearch_ftpdetails/productsearch__configuration/productsearch_user_pass', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$Credentials['ftp_path'] = $this->scopeConfig->getValue('productsearch_ftpdetails/productsearch__configuration/productsearch_ftp_path', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		return $Credentials;
	}
	
}
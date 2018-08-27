<?php

namespace Orange\Upload\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterfac
     */
    protected $_scopeConfig;
    protected $_request;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_request = $request;
    }

    public function logCreate($fileName, $data) {
        $log_mode = $this->_scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if(isset($log_mode) && $log_mode==1){
            $writer = new \Zend\Log\Writer\Stream(BP . "$fileName");
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $log_mode = $this->_scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if(isset($log_mode) && $log_mode==1){
                $logger->info($data);
            }
        }
    }

    public function storeInfo() {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $manager = $om->get('Magento\Store\Model\StoreManagerInterface');
        return $manager;
    }

     public function getChangeURL() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $urlDetails = array();
       //if ($this->_request->getFullActionName() == 'catalog_product_view') {
         //  $product = $objectManager->get('Magento\Framework\Registry')->registry('current_product'); //get current product
		  // $category = $objectManager->get('Magento\Framework\Registry')->registry('current_category');
          // $urlDetails['id'] = $product->getId();
		   //if($category) {		   
				//$urlDetails['categoryid'] = $category->getId();
		   //}
          // $urlDetails['type'] = 'product';           
       
           // return $urlDetails;
       // }else 
	   if ($this->_request->getFullActionName() == 'catalog_category_view') {
            $category = $objectManager->get('Magento\Framework\Registry')->registry('current_category');
            $urlDetails['id'] = $category->getId();
            $urlDetails['type'] = 'category';            
            return $urlDetails;
        }elseif($this->_request->getFullActionName() == 'catalog_product_view'){
		    $product = $objectManager->get('Magento\Framework\Registry')->registry('current_product');
		    $urlDetails['id'] = $product->getId();
            $urlDetails['type'] = 'product';
		    return $urlDetails;
        }
		else {
            return $urlDetails = false;
		}
       
    }

    public function urlRewriteTable($storeName) {
        $dataValue = $this->getChangeURL();
		$storeKey = '';
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	    $data = $this->storeIdChanger($storeName);
        $url = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($data['id'])
               ->getBaseUrl();
	   if($this->getCustomerGroup() == 'SOHO'){
			if($data['code'] == 'nl') {
				$storeKey = 'zelfstandigen';//NL Store keyword
			} 
			else {
				$storeKey = 'independants';//FR Store Keyword
			}
		}
        if (is_array($dataValue)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connectionDb = $resource->getConnection();
            $tableName = $resource->getTableName('url_rewrite');
            // if($dataValue['type'] == 'product' && $dataValue['categoryid']) {
				// $sql = "Select request_path FROM " . $tableName . " WHERE (entity_type = '" . $dataValue['type'] . "' and store_id = '" . $storeName['id'] . "' and target_path = 'catalog/product/view/id/".$dataValue['id']."/category/".$dataValue['categoryid']."' ) AND (entity_id = " . $dataValue['id'] . ") limit 1;";
			// }
			// else {
				// $sql = "Select request_path FROM " . $tableName . " WHERE (entity_type = '" . $dataValue['type'] . "' and store_id = " . $storeName['id'] . ") AND (entity_id = " . $dataValue['id'] . ") limit 1;";
				
			// }
			$sql = "Select request_path FROM " . $tableName . " WHERE (entity_type = '" . $dataValue['type'] . "' and store_id = " . $storeName['id'] . ") AND (entity_id = " . $dataValue['id'] . ") limit 1;";
              $result = $connectionDb->fetchAll($sql);
			  $path = $result[0];
			  $val = array();
			  if($storeKey){
				 $val['request_path'] = $url.$storeKey.'/'.$path['request_path'];			
			  }else{
				  $val['request_path'] = $url.$path['request_path'];			 
			  }
            return $val;
        }
    }
    public function getSohoChange($storeId){
	//echo $storeId;
	     $om = \Magento\Framework\App\ObjectManager::getInstance();
		 $resource =$om->get('Magento\Framework\App\ResourceConnection');
         $connectionDb = $resource->getConnection();
         $tableName = $resource->getTableName('url_rewrite');
		 $urlInterface=$om->get('Magento\Framework\UrlInterface');
         $currentUrl=$urlInterface->getCurrentUrl();
		 $baseUrl=$om->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore($storeId)
            ->getBaseUrl();
		 if($storeId==1){
		   $url=str_replace('nl','fr',$baseUrl);
		 }else{
		   $url=str_replace('fr','nl',$baseUrl);
		 }
		 $part=str_replace($baseUrl,'',$currentUrl);	
		 $registry = $om->get('Magento\Framework\Registry');
		 $inetrmidiateid=$registry->registry('inetrmidiateid');
		 $actualurl='intermediate/listing/item/id/'.$inetrmidiateid.'/';
		 $manager = $om->get('Magento\Store\Model\StoreManagerInterface');
         $stores = $manager->getStores($withDefault = false);
         $data = array();
         foreach ($stores as $store) {
            if ($store->getId() != $storeId) {
                $storeId1 = $store->getStoreId();
				$storeCode=$store->getCode();
            }
         }
		$sql = "Select request_path FROM " . $tableName . " WHERE target_path='".$actualurl."' AND store_id!='".$storeId."'";
		$result = $connectionDb->fetchAll($sql);
		if($this->getCustomerGroup() == 'SOHO'){
		    if($storeCode == 'nl') {
				$storeKey = 'zelfstandigen';//NL Store keyword
			} 
			else {
				$storeKey = 'independants';//FR Store Keyword
			}
		 }else{
		   $storeKey="";
		 }
		 foreach($result as $key=>$val){
		    $value=$val['request_path'];
			$valuearr=explode('/',$value);
		    if($storeKey==""){
			  if((!in_array('zelfstandigen',$valuearr)) || (!in_array('independants',$valuearr))){
			     $url=$url.$val['request_path'];
				 break;
			  }
			}else{
			  if(in_array($storeKey,$valuearr)){
			     $url=$url.$val['request_path'];
				 break;
			  }
			}
		 }
		// echo $url;die;
		return $url;
	}
	function getCmsPageChangeUrl($storeId){
	  $om = \Magento\Framework\App\ObjectManager::getInstance();
	  $url="";
	  $urlInterface=$om->get('Magento\Framework\UrlInterface');
      $currentUrl=$urlInterface->getCurrentUrl();
	  $baseUrl=$om->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore($storeId)
            ->getBaseUrl();
	  $manager = $om->get('Magento\Store\Model\StoreManagerInterface');
         $stores = $manager->getStores($withDefault = false);
         $data = array();
         foreach ($stores as $store) {
            if ($store->getId() != $storeId) {
                $storeId1 = $store->getStoreId();
				$storeCode=$store->getCode();
            }
         }		
	  $explode=explode('/',$currentUrl);
	  //print_r($explode);die;
	  if(in_array('activer',$explode) || in_array('activeren',$explode) ){
	  //die('sdsadsa');
	   if($storeId=='1'){
		   $url=str_replace('nl','fr',$baseUrl);
		   $identifier='activer';
	  }else{
		   $url=str_replace('fr','nl',$baseUrl);
		   $identifier='activeren';
	  }
	
	  $part=str_replace($baseUrl,'',$currentUrl);	
	  $storeKey="";
	   if($this->getCustomerGroup() == 'SOHO'){
	     if($storeCode == 'nl') {
				$storeKey = 'zelfstandigen/';//NL Store keyword
			} 
			else {
				$storeKey = 'independants/';//FR Store Keyword
		    }
		  //$identifier=trim(str_replace($storeKey,'',$part));
		 // $identifier=$storeKey;
	   }else{
	      $storeKey="";
	   }
	   
	     $url=$url.$storeKey.$identifier;
	   }
	   return $url;
	
	}
    public function storeIdChanger($storeCode) {
        $dataValue = $this->getChangeURL();
        if (is_array($dataValue)) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $manager = $om->get('Magento\Store\Model\StoreManagerInterface');
            $stores = $manager->getStores($withDefault = false);
            $data = array();
            foreach ($stores as $store) {
                if ($store->getCode() != $storeCode) {
                    $data['code'] = $store->getCode();
                    $data['id'] = $store->getStoreId();
                }
            }
            return $data;
        }else{
            return false;
        }
        
    }
    function getProductUrl($storeName){
	     //print_r($storeName);
		  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	      $data=$this->storeIdChanger($storeName);
          $url=$objectManager->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore($data['id'])
            ->getBaseUrl();
		  $dataValue = $this->getChangeURL();
		  //print_r($dataValue);die;
		  $product = $objectManager->get('Magento\Framework\Registry')->registry('current_product');
		  $urlDetails['id'] = $product->getId();
          $urlDetails['type'] = 'product';
		  $category = $product->getCategoryIds();
		  //print_r($category);
		  //$category=end(asort($category));
		  $current_category = $objectManager->get('Magento\Framework\Registry')->registry('current_category');
		  
		  if($current_category){
			$category_id  = $current_category->getId();
		  }else {
			asort($category);
			$category_id = end($category);  
		  }
		  
		   
		  $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
          $connectionDb = $resource->getConnection();
          $tableName = $resource->getTableName('url_rewrite');
		  if($this->getCustomerGroup() == 'SOHO'){
		    if($data['code'] == 'nl') {
				$storeKey = 'zelfstandigen';//NL Store keyword
			} 
			else {
				$storeKey = 'independants';//FR Store Keyword
			}
		  }
		  $sql = "Select * FROM " . $tableName . " WHERE (entity_type = '" . $dataValue['type'] . "' and store_id = " . $storeName['id'] . ") AND (entity_id = " . $dataValue['id'] . ") ;";
		  $result = $connectionDb->fetchAll($sql);
		  foreach($result as $key=>$val){
		    $unserialized=unserialize($val['metadata']); 
			if($unserialized['category_id']==$category_id){
			  $path=$val['request_path'];
			  if(isset($storeKey)){
			     $url=$url.$storeKey.'/'.$path ;			
			  }else{
			      $url=$url.$path;			 
			  }
			  
			  break;
			}
		  }
		  
		  return $url;
		  
		 
    }
	private function getCustomerGroup()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerGroup = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();
		return $customerGroup;		
	}
    function checkSubs($catId){
	}
    public function productPage() {
        if ($this->_request->getFullActionName() == 'catalog_category_view') {
            $data = 'category'; 
            return $data;
        } if ($this->_request->getFullActionName() == 'catalog_product_view') {
            $data = 'product'; 
            return $data;
        } else {
            $data = 'others';  
            return $data;
        }        
    }

}
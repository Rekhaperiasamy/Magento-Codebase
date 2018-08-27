<?php

namespace Orange\Seo\Helper;

class SamsungData extends \Magento\Framework\App\Helper\AbstractHelper {
    protected $_scopeConfig;
    protected $_storeManager;
    protected $_categoryFactory;
    protected $_productCollectionFactory;
    protected $productAttributeRepository;
    protected $_messageManager;
	const REPORTPATH = BP.DIRECTORY_SEPARATOR.'pub'.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'searchreports'.DIRECTORY_SEPARATOR;
	
    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
              \Magento\Framework\Message\ManagerInterface $messageManager,
			  \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
			 \Magento\Customer\Model\Session $session, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\File\Csv $csvProcessor, \Magento\Catalog\Model\CategoryFactory $categoryFactory, \Magento\Catalog\Model\ProductRepository $productRepository, \Magento\Catalog\Model\Category $categoryModel, \Magento\Catalog\Model\CategoryRepository $categoryRepository, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_categoryFactory = $categoryFactory;
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
		parent::__construct($context);
    }        
    public function getSamsungFeedsFr() {
        $stores = 'fr';
		$store_id = 2;
        $collection = $this->getProductCollection($stores);     
		$xml = new \SimpleXMLElement('<products />');                  
         $query = array(
        'ac_caid'=>'samsung',
        'ac_med'=>'samsung',
        'ac_src'=>'samsung',
        );         
        foreach ($collection as $key => $cols) {            
            if (!empty($cols->getBrand())) { //converting the brand from option id to option value
                $brandName = $cols->getResource()->getAttribute('brand')->getFrontend()->getValue($cols);
            } else {
                $brandName = ""; //if no brand is specified then null
            } 

            //$productUrl = $product->getUrlModel()->getUrl($product).'?ac_caid=samsung&amp;ac_med=samsung&amp;ac_src=samsung';
			
			$product = $this->_productRepository->getById($cols->getId());
			$baseurl = $this->_storeManager->getStore()->getBaseUrl();
			$baseurl = substr($baseurl, 0, -3);
			$productUrl = $baseurl.$product->setStoreId($store_id)->getUrlKey().'?ac_caid=samsung&amp;ac_med=samsung&amp;ac_src=samsung';
			
			$Product_name = $product->setStoreId($store_id)->getName();
			
						
			if ($cols->getStatus() == '1') {
				$stockData = $product->getData('quantity_and_stock_status');
				$stockStatus = $stockData['is_in_stock'];
							
            if ($stockStatus) {
                $status = 'Y';
            } else {
                $status = 'N';
            }                                                   
            $item = $xml->addChild('product');            
            $item->addChild('url', $productUrl);            
            $item->addChild('sku', $cols->getSku());
            $item->addChild('product_name', $Product_name);
            $item->addChild('manufacturer', $brandName);  
            $item->addChild('mnp', '');  
            $item->addChild('inc_vat_price', $cols->getPrice());  
            $item->addChild('in_stock', $status); 
			}			
        }
        $name = strftime('mobistar_productfeed_'.$stores.'.xml'); 
	    $path=self::REPORTPATH.$name;
        $content=$xml->asXML();
        file_put_contents($path,$content);	
        header('Content-disposition: attachment; filename="'.$name.'"');
        header('Content-type: "text/xml"; charset="utf8"');	 
	    echo $content;
	    exit;
		echo $xml->asXML();
        }
     public function getSamsungFeedsNL() {
    	 
        $stores = 'nl';
		$store_id = 1;
        $collection = $this->getProductCollection($stores); 
		$xml = new \SimpleXMLElement('<products />');                  
         $query = array(
        'ac_caid'=>'samsung',
        'ac_med'=>'samsung',
        'ac_src'=>'samsung',
        );         
        foreach ($collection as $key => $cols) {            
            if (!empty($cols->getBrand())) { //converting the brand from option id to option value
                $brandName = $cols->getResource()->getAttribute('brand')->getFrontend()->getValue($cols);
            } else {
                $brandName = ""; //if no brand is specified then null
            }                        
            //$product = $this->_productRepository->getById($cols->getId());
			//$productUrl = $product->getUrlModel()->getUrl($product).'?ac_caid=samsung&amp;ac_med=samsung&amp;ac_src=samsung';
			
			$product 		= $this->_productRepository->getById($cols->getId());
			$baseurl 		= $this->_storeManager->getStore()->getBaseUrl();
			$baseurl 		= substr($baseurl, 0, -3);
			$productUrl 	= $baseurl.$product->setStoreId($store_id)->getUrlKey().'?ac_caid=samsung&amp;ac_med=samsung&amp;ac_src=samsung';
			$Product_name 	= $product->setStoreId($store_id)->getName();
			
            if ($cols->getStatus() == '1') {
                $stockData = $product->getData('quantity_and_stock_status');
				$stockStatus = $stockData['is_in_stock'];
							
            if ($stockStatus) {
                $status = 'Y';
            } else {
                $status = 'N';
            }                                     
            $item = $xml->addChild('product');            
            $item->addChild('url', $productUrl);            
            $item->addChild('sku', $cols->getSku());
            $item->addChild('product_name', $Product_name);
            $item->addChild('manufacturer', $brandName);  
            $item->addChild('mnp', '');  
            $item->addChild('inc_vat_price', $cols->getPrice());  
            $item->addChild('in_stock', $status);
			}			
        }
		$name = strftime('mobistar_productfeed_'.$stores.'.xml'); 
	    $path=self::REPORTPATH.$name;
        $content=$xml->asXML();
        file_put_contents($path,$content);	
        header('Content-disposition: attachment; filename="'.$name.'"');
        header('Content-type: "text/xml"; charset="utf8"');	 
	    echo $content;
	    exit;
        
        
    } 
	public function getCronSamsungFeedsNL() {    	 
        $stores = 'nl';
        $collection = $this->getProductCollection($stores); 
		$xml = new \SimpleXMLElement('<products />');                  
         $query = array(
        'ac_caid'=>'samsung',
        'ac_med'=>'samsung',
        'ac_src'=>'samsung',
        );         
        foreach ($collection as $key => $cols) {            
            if (!empty($cols->getBrand())) { //converting the brand from option id to option value
                $brandName = $cols->getResource()->getAttribute('brand')->getFrontend()->getValue($cols);
            } else {
                $brandName = ""; //if no brand is specified then null
            }                        
            $product = $this->_productRepository->getById($cols->getId());
            $productUrl = $product->getUrlModel()->getUrl($product).'?ac_caid=samsung&amp;ac_med=samsung&amp;ac_src=samsung';
            if ($cols->getStatus() == '1') {
                $status = 'Y';
            } else {
                $status = 'N';
            }                                                    
            $item = $xml->addChild('product');            
            $item->addChild('url', $productUrl);            
            $item->addChild('sku', $cols->getSku());
            $item->addChild('product_name', $cols->getName());
            $item->addChild('manufacturer', $brandName);  
            $item->addChild('mnp', '');  
            $item->addChild('inc_vat_price', $cols->getPrice());  
            $item->addChild('in_stock', $status);  
        }
		$name = strftime('mobistar_productfeed_'.$stores.'.xml'); 
	    $path=self::REPORTPATH.$name;
        $content=$xml->asXML();
        file_put_contents($path,$content);	
        header('Content-disposition: attachment; filename="'.$name.'"');
        header('Content-type: "text/xml"; charset="utf8"');	 
	    $this->ftpPushProduct($path);
	} 
	    public function getCronSamsungFeedsFr() {
        $stores = 'fr';
        $collection = $this->getProductCollection($stores);     
		$xml = new \SimpleXMLElement('<products />');                  
         $query = array(
        'ac_caid'=>'samsung',
        'ac_med'=>'samsung',
        'ac_src'=>'samsung',
        );         
        foreach ($collection as $key => $cols) {            
            if (!empty($cols->getBrand())) { //converting the brand from option id to option value
                $brandName = $cols->getResource()->getAttribute('brand')->getFrontend()->getValue($cols);
            } else {
                $brandName = ""; //if no brand is specified then null
            }                        
            $product = $this->_productRepository->getById($cols->getId());
            $productUrl = $product->getUrlModel()->getUrl($product).'?ac_caid=samsung&amp;ac_med=samsung&amp;ac_src=samsung';
			
            if ($cols->getStatus() == '1') {
                $status = 'Y';
            } else {
                $status = 'N';
            }                                                    
            $item = $xml->addChild('product');            
            $item->addChild('url', $productUrl);            
            $item->addChild('sku', $cols->getSku());
            $item->addChild('product_name', $cols->getName());
            $item->addChild('manufacturer', $brandName);  
            $item->addChild('mnp', '');  
            $item->addChild('inc_vat_price', $cols->getPrice());  
            $item->addChild('in_stock', $status);  
        }
        $name = strftime('mobistar_productfeed_'.$stores.'.xml'); 
	    $path=self::REPORTPATH.$name;
        $content=$xml->asXML();
        file_put_contents($path,$content);	
        header('Content-disposition: attachment; filename="'.$name.'"');
        header('Content-type: "text/xml"; charset="utf8"');	 
	    $this->ftpPushProduct($path);
		
	   exit;
        }
   /*  private function ftpPushNl($file,$remote_file){
        
            $info = $this->ftpDetails();
        
//             $ftp_server="ftp.e-tale.co.uk";          
//            $ftp_user_name="mobistar.be";
//            $ftp_user_pass="M0B1sT@ar@be";  
            $ftp_server = $info['ftp_server'];
            $ftp_user_name = $info['ftp_user_name'];
            $ftp_user_pass = $info['ftp_user_pass'];
            
            $file = 'sites/default/files/export/samsung_export/mobistar_productfeed_nl.xml';
            $remote_file = 'sites/default/files/export/samsung_export/mobistar_productfeed_nl.xml';
            // set up basic connection
            $conn_id = ftp_connect($ftp_server);
            if (false === $conn_id) {
                throw new Exception("FTP connection error!");
            }            
            // login with username and password
            $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
            // upload a file
            if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
             echo "successfully uploaded $file\n";
            } else {
             echo "There was a problem while uploading $file\n";
            }
            // close the connection
            ftp_close($conn_id); 
    }        
 */
  private function ftpPushProduct($file){
        
            $info = $this->ftpDetails();
         
            $ftp_server = $info['ftp_server'];
            $ftp_user_name = $info['ftp_user_name'];
            $ftp_user_pass = $info['ftp_user_pass']; 
		/* 	 $ftp_server="ftp.e-tale.co.uk";          
            $ftp_user_name="mobistar.be";
            $ftp_user_pass="M0B1sT@ar@be";   */
			//$ftp_path = $info['ftp_path'];
            $ftp_path = 'retailerfeed';
            //$file = 'sites/default/files/export/samsung_export/mobistar_productfeed_nl.xml';
            //$remote_file = 'sites/default/files/export/samsung_export/mobistar_productfeed_nl.xml';
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
				echo "successfully uploaded $file\n";
            } else {
				echo "There was a problem while uploading $file\n";
			}
			// close the connection
			ftp_close($conn_id); 
	}

   private function getCategory($categoryId) {
        $category = $this->_categoryFactory->create();
        $category->load($categoryId);
        return $category->getName();
    }

    private function getProductCollection($stores) {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->setStore($stores);      
        //setting the brand as Samsung
        $collection->addFieldToFilter('brand', array('eq' => '120'));
        $collection->addFieldToFilter('type_id', array('eq' => 'simple'));
        $collection->load();        
        return $collection;
    }

    private function getCustomerGroup() {
        $customerGroup = $this->_session->getCustomerTypeName();
        return $customerGroup;
    }

    private function getStoreCode() {
        return $this->_storeManager->getStore()->getCode();
    }

    private function getStores() {
        return $this->_storeManager->getStore();
    }
    
    public function ftpDetails() {
        $Credentials = array();
        $Credentials['ftp_server'] = $this->scopeConfig->getValue('samsung_ftpdetails/samsungdetail__configuration/ftp_server', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['ftp_user_name'] = $this->scopeConfig->getValue('samsung_ftpdetails/samsungdetail__configuration/ftp_user_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['ftp_user_pass'] = $this->scopeConfig->getValue('samsung_ftpdetails/samsungdetail__configuration/ftp_user_pass', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $Credentials;
    }
}

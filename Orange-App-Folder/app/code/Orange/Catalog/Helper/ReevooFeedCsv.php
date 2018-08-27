<?php
namespace Orange\Catalog\Helper;
class ReevooFeedCsv extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_scopeConfig;
    protected $_storeManager;
    protected $_categoryFactory;
    protected $_productCollectionFactory;
    protected $productAttributeRepository;
    //const REPORTPATH = BP.DIRECTORY_SEPARATOR.'pub'.DIRECTORY_SEPARATOR.'common-header'.DIRECTORY_SEPARATOR;
	const REPORTPATH = BP.DIRECTORY_SEPARATOR.'pub'.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'reevoo'.DIRECTORY_SEPARATOR;
	
	//$dir = BP . '/common-header/salespadreport/';
    public function __construct(
    \Magento\Framework\App\Helper\Context $context, 
	\Magento\Store\Model\StoreManagerInterface $storeManager, 
	\Magento\Customer\Model\Session $session, \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\File\Csv $csvProcessor,
	 \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
	\Magento\Catalog\Model\CategoryFactory $categoryFactory, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
         $this->_storeManager = $storeManager; 
        $this->_categoryFactory = $categoryFactory;
		$this->directory_list = $directory_list;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_session = $session;
        $this->inlineTranslation = $inlineTranslation;
        $this->csvProcessor = $csvProcessor;
        $this->_productAttributeRepository = $productAttributeRepository;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context);
    }    
    /*
     * Reevoo Csv File Generation 
     */
    public function generatesFeeds() {
        $StoreNames = array("nl", "fr");
        foreach ($StoreNames as $stores) { //looping upon count of store views
            $collection = $this->getProductCollection($stores); //getting product collection                                   
            $csvAppenderFlag = '1'; //for deleting contents of file first time then change flag and write from begining
            $dataArray = array();
			$reevooProducts = array();
            foreach ($collection as $key => $cols) { //collection loops here				
                if (!empty($cols->getBrand())) { //converting the brand from option id to option value
                    $brandName = $cols->getResource()->getAttribute('brand')->getFrontend()->getValue($cols);
                } else {
                    $brandName = ""; //if no brand is specified then null
                }
                ob_start(); //flusing the ob  file writer object 
                $modelName = $cols->getHandsetFamily();                
                $dataArray['brand'] = $brandName;
                $dataArray['family'] = $cols->getHandsetFamily();                
                $dataArray['sku'] = $cols->getSku();
                $dataArray['name'] = $cols->getName();
                $image = $cols->getImage(); //getting product image
                $mediaPath = $this->getStores()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $dataArray['image'] = $imagePath = $mediaPath . "catalog/product" . $image;                                               
                foreach ($cols->getCategoryIds() as $catIds) { //getting category name of product
                    $dataArray['product_category'] = $this->getCategory($catIds);
                }                
                $dataArray['ean'] = ''; //empty for now
                $dataArray['description'] = ''; //empty for now
                $dataArray['mpn'] = ''; //empty for now
				$reevooProducts[]=$dataArray;
            }           
            if ($stores == 'nl') { //printing into store language file MBD for dutch and MBF for french
                $pFileName = 'MBD_product.csv';
            } else {
                $pFileName = 'MBF_product.csv';
            }            
            $csvHeader1 = $this->getHeader(); //getting header 
            
			/*
            $dir = 'sites/default/files/export/reevoo/'; //specifiying the directory
            if (!is_dir($dir)) { //creating directory if not exists with full RWX permission
                umask(0777);
                mkdir('sites/default/files/export/reevoo/', 0777, true);
            }*/
            
			$baseDir = $this->directory_list->getRoot(); 
			//$reportFolder = $baseDir.'/sites/default/files/export/reevoo/';
			$reportFolder = self::REPORTPATH;
			$dir = $reportFolder;
			$filename = $pFileName;
             if(!is_dir($reportFolder)){
				mkdir($reportFolder, 0777, TRUE);   
			}
            
            if (is_file($dir . $pFileName)) {
                //if file exists 
                // unlink($dir);
                if ($csvAppenderFlag == "1") {  //flag for deleting content 
                    $fc = fopen($dir . $pFileName, 'w+'); //open file and delete content and write in begining
                } else {
                    $fc = fopen($dir . $pFileName, 'a+'); //open file and append content based on changed flag of csvappenderflag
                }
            } else {
                $fc = fopen($dir . $pFileName, 'w+'); // opening newly created  file in write mode 
            }
                
            fputcsv($fc, $csvHeader1);
            foreach($reevooProducts as $values){
                fputcsv($fc, $values);
            }
            fclose($fc); //closing the file writer
            ob_flush(); //flushing the writer object
            
        }
    }
    private function getHeader() {
        $header = array("manufacturer", "model", "sku", "name", "image_url", "product_category", "ean", "description", "mpn");
        return $header;
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
        //ignoring postpaid ,prepaid,simcard and accessory products
        $collection->addAttributeToFilter('attribute_set_id', array("eq" => "12"));
        $collection->addAttributeToFilter('status', 1);
        //$collection->setPageSize(1); // fetching only 3 products
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
}

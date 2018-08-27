<?php

namespace Orange\Seo\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
    protected $_scopeConfig;
    protected $_storeManager;
    protected $_categoryFactory;
    protected $_productCollectionFactory;
    protected $productAttributeRepository;
    protected $_messageManager;
    protected $_productRepository;
    protected $_attributeSet;	
    
    const REPORTPATH = BP.DIRECTORY_SEPARATOR.'pub'.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'googlefeed'.DIRECTORY_SEPARATOR;
	public function __construct(
            \Magento\Framework\App\Helper\Context $context,
            \Magento\Framework\Message\ManagerInterface $messageManager,
            \Magento\Customer\Model\Session $session,
            \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
            \Magento\Store\Model\StoreManagerInterface $storeManager, 
            \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository, 
            \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, 
            \Magento\Framework\File\Csv $csvProcessor, 
            \Magento\Catalog\Model\CategoryFactory $categoryFactory, 
            \Magento\Catalog\Model\ProductRepository $productRepository, 
            \Magento\Catalog\Model\Category $categoryModel, 
            \Magento\Catalog\Model\CategoryRepository $categoryRepository, 
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
            \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
    ) {
        $this->_storeManager = $storeManager;
        $this->_categoryFactory = $categoryFactory;
        $this->_session = $session;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->inlineTranslation = $inlineTranslation;
        $this->csvProcessor = $csvProcessor;
		$this->directory_list = $directory_list;
        $this->categoryModel = $categoryModel;
        $this->_messageManager = $messageManager;
        $this->categoryRepository = $categoryRepository;
        $this->_productAttributeRepository = $productAttributeRepository;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productRepository = $productRepository;
        $this->_attributeSet = $attributeSet;
		
        parent::__construct($context);
    }    
    public function entraCredentials() {
        $Credentials = array();
        $Credentials = $this->scopeConfig->getValue('google_seo/seo_configuration/seo_confi_attribute', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $Credentials;
    }
    
    public function getFeeds() {
        $attribute_ids = $this->_scopeConfig->getValue('googlefeed_sec/googlefeed_configuration/attributelist', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $attribute_set_id = explode(',', $attribute_ids);
        
        $store_id = 2;
        $stores = 'fr';
        $product_ext = 'chez Orange';
        
        $product_type ='';
        $plan = '';
        $tarrif_plan = '';
        $subsidy_duration = '';
        $cashback = '';
        $brandName = '';
        $attribute_name = '';
        $internalMemory = '';
        $sim_type = '';
        $os_detail = '';
        $processorType = '';
        $processorSpeed = '';
        $ram ='';
        $mobileNetwork ='';
        $twoGband ='';
        $threeGband = '';
        $fourGband = '';
        $bluetooth = '';
        $wifi = '';
        $usb = '';
        $wireless = '';
        $nfc = '';
        $hdVoice ='';
        $primaryCamera = '';
        $secondaryCamera = '';
        $mplayer ='';
        $fmRadio = '';
        $desc = '';
        $abonnement = 'standalone';
        $color = '';
        
        $collection = $this->getProductCollection($stores,$attribute_set_id);
        $baseurl = $this->_storeManager->getStore($store_id)->getBaseUrl();
        
        $xml = new \SimpleXMLElement('<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0" ></rss>');
        $xml->addChild('channel');
        $xml->channel->addChild('title', 'fr_orange');
        $xml->channel->addChild('link', 'https://eshop.orange.be/');
        $xml->channel->addChild('description', 'All products for the fr orange feed');
        // add item element for each article                        
        $ns = "http://base.google.com/ns/1";           
        foreach ($collection as $key => $cols) {
            $image = $cols->getImage(); //getting product image
            
            $attributeSetRepository = $this->_attributeSet->get($cols->getAttributeSetId());
            $attribute_name = $attributeSetRepository->getAttributeSetName();
            
            $mediaPath = $this->getStores()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            //$imagePath = $mediaPath . "catalog/product" . $image;
            $pics = $mediaPath . "catalog/product" . $image;
            if (!empty($cols->getBrand())) { //converting the brand from option id to option value
                $brandName = $cols->getResource()->getAttribute('brand')->setStoreId($store_id)->getFrontend()->getValue($cols);
				$brandName = htmlspecialchars(strip_tags($brandName));
            } else {
                $brandName = ""; //if no brand is specified then null
            }
            $desc = htmlspecialchars(strip_tags($cols->getMarketingDescription()));            
            
            if($attribute_name != "Accessories"){
                
                $desc = $desc.", Taille de l'écran:".$cols->getData('screen_size').", Résolution de l'écran:".$cols->getData('screen_resolution');
                if($cols->getLength() !='' && $cols->getWidth()!='' && $cols->getThicknessHeight()!=''){
                    $desc = $desc.", Dimensions:".floatval($cols->getLength())." x ".floatval($cols->getWidth())." x ".floatval($cols->getThicknessHeight())."mm";
                }
                
                $desc = $desc.", Poids:".floatval($cols->getWeight())." g".", Capacité de la batterie:".$cols->getBatteryCharge();
                if($cols->getRemovableBattery() == 1){
                    $desc = $desc.", Batterie amovible: Oui";
                }else{
                    $desc = $desc.", Batterie amovible: Non";
                }
                
                $internalMemory = $cols->getResource()->getAttribute('internal_memory')->setStoreId($store_id)->getFrontend()->getValue($cols);
                
                if($internalMemory !=''){
                    $desc = $desc.", Mémoire interne:".$internalMemory;
                }
                $sim_type = $cols->getResource()->getAttribute('sim_type')->setStoreId($store_id)->getFrontend()->getValue($cols);
                
                if($sim_type!=''){
                    $desc = $desc. ", Format de carte SIM:". $sim_type;
                }
                $os_detail = $cols->getResource()->getAttribute('os_details')->setStoreId($store_id)->getFrontend()->getValue($cols);
                
                if($os_detail!=''){
                    $desc = $desc. ", Système d'exploitation:". $os_detail;
                }
                //$processorType = $cols->getResource()->getAttribute('processor')->setStoreId($store_id)->getFrontend()->getValue($cols);
                $processorType = $cols->getProcessor();
                
                $processorSpeed = $cols->getResource()->getAttribute('processor_speed')->setStoreId($store_id)->getFrontend()->getValue($cols); 
                
                $desc = $desc. ", Processeur:".$processorType." x ".$processorSpeed;
                
                $ram = $cols->getResource()->getAttribute('ram')->setStoreId($store_id)->getFrontend()->getValue($cols);
                
                if($ram!=''){
                    $desc = $desc.",  RAM:".$ram;
                }
                
                $mobileNetwork = $cols->getResource()->getAttribute('mobile_network_type')->setStoreId($store_id)->getFrontend()->getValue($cols);
                
                if($mobileNetwork!=''){
                   $desc = $desc.", Accès internet mobile:".$mobileNetwork; 
                }
                $twoGband = $cols->getResource()->getAttribute('bands_2g')->setStoreId($store_id)->getFrontend()->getValue($cols);
                $threeGband = $cols->getResource()->getAttribute('bands_3g')->setStoreId($store_id)->getFrontend()->getValue($cols);
                //$fourGband = $cols->getResource()->getAttribute('bands_4g')->setStoreId($store_id)->getFrontend()->getValue($cols);
                $fourGband = $cols->getBands4g();
                
                if($twoGband!=''){
                    $desc = $desc.", Fréquences2G (MHz):". $twoGband;
                }
                if($threeGband!=''){
                    $desc = $desc.", Fréquences3G (MHz):". $threeGband;
                }
                if($fourGband!=''){
                    $desc = $desc.", Fréquences4G (MHz):". $fourGband;
                }
                $bluetooth = $cols->getBluetooth();
                if($bluetooth==1){
                    $desc = $desc.", Bluetooth: Oui";
                }
                $wifi = $cols->getWifi();
                if($wifi==1){
                    $desc = $desc.", WIFI: Oui";
                }
                $gps = $cols->getGps();
                if($wifi==1){
                    $desc = $desc.", GPS: Oui";
                }
                $usb = $cols->getUsb();
                if($usb==1){
                    $desc = $desc.", USB: Oui";
                }
                $wireless = $cols->getWirelessCharging();
                if($wireless==1){
                    $desc = $desc.", Recharge sans fil: Oui";
                }
                $nfc = $cols->getNfc();
                if($nfc==1){
                    $desc = $desc.", NFC: Oui";
                }
                $hdVoice = $cols->getHdVoice(); 
                if($hdVoice==1){
                    $desc = $desc.", HD Voice: Oui";
                }
                
                $primaryCamera = $cols->getResource()->getAttribute('primary_camera_resolution')->setStoreId($store_id)->getFrontend()->getValue($cols);
                $desc = $desc.", Appareil photo:".$primaryCamera;
                $flash = $cols->getFlash();
                if($flash==1){
                    $desc = $desc.", Flash: Oui";
                }
                $secondaryCamera = $cols->getResource()->getAttribute('secondary_camera_resolution')->setStoreId($store_id)->getFrontend()->getValue($cols);
                $desc = $desc.", Appareil photo avant:".$secondaryCamera;
                $video = $cols->getVideoCapture();
                if($video == 1){
                    $desc = $desc.", VidéoHD:Oui";
                }
                $mplayer = $cols->getData('mp3_player'); 
                if($mplayer == 1){
                    $desc = $desc.", MP3:Oui";
                }
                $fmRadio = $cols->getFmRadio();
                if($fmRadio == 1){
                    $desc = $desc.", Radio FM:Oui";
                }
                if($cols->getSarDarDetails()!=''){
                    $desc = $desc.", Valeur DAS (Débit d'absorption spécifique):".$cols->getSarDarDetails();
                }
            }
            $desc = ltrim($desc, ',');
            $desc = substr($desc,0,1000);
            
            //$product = $this->_productRepository->getById($cols->getId());
            //$productUrl = $product->getUrlModel()->getUrl($product);
            //$productUrl = $product->setStoreId($store_id)->getProductUrl();
            $productUrl = $baseurl.$cols->getUrlKey();
            $productName = $cols->getName();
            
            if ($cols->getStatus() == '1') {
                $status = 'in stock';
            } else {
                $status = 'out of stock';
            }
            $productSku = $cols->getSku().''.$stores;
            $productSku = str_replace(' ', '', $productSku);
            if(!empty($cols->getProductIconImage())){
                $productType = $cols->getResource()->getAttribute('product_icon_image')->getFrontend()->getValue($cols);
                if($productType=="smartphone"){
                    $product_type = "Mobile > Hardware > Smartphones and GSM > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Communications > Telephony > Mobile Phones > Smartphones";
                }else if($productType=="shop-accessories"){
                    $product_type = "Mobile > Hardware > Accessories > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Communications > Telephony > Mobile Phone Accessories";
                }else if($productType=="tablet"){
                    $product_type = "Mobile > Hardware > Tablets > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Computers > Tablet Computers";
                }else if($productType=="modem"){
                    $product_type = "Mobile > Hardware > Modems > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Networking > Modems";
                }
                else if($productType=="connection"){
                    $product_type = "Mobile > Hardware > Connected devices > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Electronics Accessories";
                }
            }
            
            $color = $cols->getResource()->getAttribute('color')->setStoreId($store_id)->getFrontend()->getValue($cols);
			$color = htmlspecialchars(strip_tags($color));
            if(empty($color)){
                  $color = ""; 
            }
            
            $ean = $cols->getData('european_article_number_1');
            
            if(!empty($cols->getData('cashback_stand_alone_b2c'))){
                $cashback = 'Yes';
            }else {
                $cashback = 'No';
            }
            
            $nintendo_ids = $cols->getUpSellProductIds();
            
            if (count($nintendo_ids) > 0){
                $nintendo_product = $this->_productRepository->getById($nintendo_ids[0]);
                
                if($productType=="smartphone"){
                    $product_type = "Mobile > Subscriptions with Smartphone > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Communications > Telephony > Mobile Phones > Smartphones";
                }
                $typeInstance = $nintendo_product->getTypeInstance(true);
                $requiredChildrenIds = $typeInstance->getChildrenIds($nintendo_ids[0], false);
                $price = ROUND($nintendo_product->getPrice(),2);
                foreach($requiredChildrenIds as $bundle_item_ids => $item_ids){ 
                    foreach($item_ids as $item_id){
                       $bundle_item = $this->_productRepository->getById($item_id);
                       //$price = ROUND($bundle_item->getPrice(),2);                       
                        if($bundle_item->getTypeId()=='virtual'){
                            $plan = $bundle_item->getName(); 
                            $tarrif_plan = ROUND($bundle_item->getSubscriptionAmount(),2);
                            $subs_duration = $bundle_item->getSubsidyDuration();
                            if(!empty($subs_duration)){
                                $subsidy_duration = $subs_duration.' months';
                            } else {
                                $subsidy_duration = '24 months';
                            }
                        }
                    }                    
                }
                if(!empty($tarrif_plan)){
                    $tarrif_plan = $tarrif_plan.' EUR';
                }
                $productName = $productName.' avec '.$plan.' '.$product_ext;
                $productName = substr($productName,0,150);
                $abonnement = 'abonnement';
            }else {
                $price = ROUND($cols->getPrice(),2);
                $productName = $productName.' '.$product_ext;
                $productName = substr($productName,0,150);
                $abonnement = 'standalone';
                $plan = '';
                $tarrif_plan = '';
                $subsidy_duration = '';
                $cashback = '';
            }
            
            if(!empty($cols->getData('handset_family'))){
                $item_group_id = $cols->getData('handset_family');
            }else if(!empty($cols->getData('accessory_family'))){
                $item_group_id = $cols->getData('accessory_family');
            }
            
            //$productName = $productName.' '.$product_ext;
            
//            foreach ($cols->getCategoryIds() as $key => $val) { //getting category name of product
//                $categoryId = $val;                
//                $category = $this->categoryModel->load($categoryId);
//                //$parentName = $category->getId();
//                $Name = $category->getName().'>'.$Name;
//                
////                $categoryObj = $this->categoryRepository->get($category->getId());
////                $subcategories = $categoryObj->getChildrenCategories();
////                foreach ($subcategories as $subcategorie) {
////                    $raw = $subcategorie->getName();
////                }
//            }
            //rtrim($Name,'>');
            //$Name = 'Electronics > Communications > Telephony > Mobile Phones';
            $item = $xml->channel->addChild('item');            
            $item->addChild('g:ID', $productSku, $ns);
            $item->addChild('title', htmlspecialchars(strip_tags($productName)));
            $item->addChild('description', htmlspecialchars(strip_tags($desc)));
            $item->addChild('g:item_group_id', htmlspecialchars(strip_tags($item_group_id)), $ns);            
            $item->addChild('link', $productUrl);
            $item->addChild('g:image_link', $pics, $ns);
            $item->addChild('g:google_product_category', $google_product_cat, $ns);
            $item->addChild('g:condition', 'new', $ns);
            $item->addChild('g:availability', $status, $ns);
            $item->addChild('g:price', $price.' EUR', $ns);
            $item->addChild('g:brand', $brandName, $ns); 
            $item->addChild('g:product_type',$product_type,$ns);
            $item->addChild('g:gtin', $ean,$ns);
            $item->addChild('g:color', $color,$ns);
            $item->addChild('g:shipping', '0 EUR', $ns);
            $item->addChild('g:abonnement', $abonnement, $ns);
            $item->addChild('g:type_abonnement', $plan, $ns);
            $item->addChild('g:prix_abonnement', $tarrif_plan, $ns);
            $item->addChild('g:durée_abonnement', $subsidy_duration, $ns); 
            $item->addChild('g:cashback', $cashback, $ns);
        }   
        
	    $name = strftime($stores.'_mobistar.xml');
		$path=self::REPORTPATH;
        $WPAfile = $path . $name;
        $content=$xml->asXML();        	
        header('Content-disposition: attachment; filename="'.$name.'"');
        header('Content-type: "text/xml"; charset="utf8"');
		
		if (is_dir($path) === false) {
			mkdir($path);
			chmod($path,0777);
		}
		
        file_put_contents($WPAfile,$content);
        //file_put_contents('var/google_feed/',$content);
	//echo $content;
        
	    exit;
  }
  public function getFeedsNL() { 
        $attribute_ids = $this->_scopeConfig->getValue('googlefeed_sec/googlefeed_configuration/attributelist', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $attribute_set_id = explode(',', $attribute_ids); 
        
        $stores = 'nl';
        $store_id = 1;
        $product_ext = 'bij Orange';
        
        $product_type ='';
        $plan = '';
        $tarrif_plan = '';
        $subsidy_duration = '';
        $cashback = '';
        $brandName = '';
        $attribute_name = '';
        $internalMemory = '';
        $sim_type = '';
        $os_detail = '';
        $processorType = '';
        $processorSpeed = '';
        $ram ='';
        $mobileNetwork ='';
        $twoGband ='';
        $threeGband = '';
        $fourGband = '';
        $bluetooth = '';
        $wifi = '';
        $usb = '';
        $wireless = '';
        $nfc = '';
        $hdVoice ='';
        $primaryCamera = '';
        $secondaryCamera = '';
        $mplayer ='';
        $fmRadio = '';
        $desc = '';
        $abonnement = 'standalone';
        $color = '';
        
        $collection = $this->getProductCollection($stores, $attribute_set_id);
        $baseurl = $this->_storeManager->getStore($store_id)->getBaseUrl();
        
        $xml = new \SimpleXMLElement('<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0" ></rss>');
        $xml->addChild('channel');
        $xml->channel->addChild('title', 'nl_orange');
        $xml->channel->addChild('link', 'https://eshop.orange.be/');
        $xml->channel->addChild('description', 'All products for the nl orange feed');
        // add item element for each article                        
        $ns = "http://base.google.com/ns/1";           
        foreach ($collection as $key => $cols) {
            $image = $cols->getImage(); //getting product image
            
            $attributeSetRepository = $this->_attributeSet->get($cols->getAttributeSetId());
            $attribute_name = $attributeSetRepository->getAttributeSetName();
            
            $mediaPath = $this->getStores()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            //$imagePath = $mediaPath . "catalog/product" . $image;
            $pics = $mediaPath . "catalog/product" . $image;
            
            if (!empty($cols->getBrand())) { //converting the brand from option id to option value
                $brandName = $cols->getResource()->getAttribute('brand')->setStoreId($store_id)->getFrontend()->getValue($cols);
				$brandName = htmlspecialchars(strip_tags($brandName));
            } else {
                $brandName = ""; //if no brand is specified then null
            }
            $desc = htmlspecialchars(strip_tags($cols->getMarketingDescription()));            
            
            if($attribute_name != "Accessories"){
                
                $desc = $desc.", Schermgrootte:".$cols->getData('screen_size').", Schermresolutie:".$cols->getData('screen_resolution');
                if($cols->getLength() !='' && $cols->getWidth()!='' && $cols->getThicknessHeight()!=''){
                    $desc = $desc.", Afmeting:".floatval($cols->getLength())." x ".floatval($cols->getWidth())." x ".floatval($cols->getThicknessHeight())."mm";
                }
                
                $desc = $desc.", Gewicht:".floatval($cols->getWeight())." g".", Capaciteit van de batterij:".$cols->getBatteryCharge();
                if($cols->getRemovableBattery() == 1){
                    $desc = $desc.", Verwijderbare batterij: Ja";
                }else{
                    $desc = $desc.", Verwijderbare batterij: Non";
                }
                
                $internalMemory = $cols->getResource()->getAttribute('internal_memory')->setStoreId($store_id)->getFrontend()->getValue($cols);
                
                if($internalMemory !=''){
                    $desc = $desc.", Intern geheugen:".$internalMemory;
                }
                $sim_type = $cols->getResource()->getAttribute('sim_type')->setStoreId($store_id)->getFrontend()->getValue($cols);
                
                if($sim_type!=''){
                    $desc = $desc. ", Simkaartformaat:". $sim_type;
                }
                $os_detail = $cols->getResource()->getAttribute('os_details')->setStoreId($store_id)->getFrontend()->getValue($cols);
                
                if($os_detail!=''){
                    $desc = $desc. ", Besturingssysteem:". $os_detail;
                }
                //$processorType = $cols->getResource()->getAttribute('processor')->setStoreId($store_id)->getFrontend()->getValue($cols);
                $processorType = $cols->getProcessor();
                
                $processorSpeed = $cols->getResource()->getAttribute('processor_speed')->setStoreId($store_id)->getFrontend()->getValue($cols); 
                
                $desc = $desc. ", BewerkerQuad :".$processorType." x ".$processorSpeed;
                
                $ram = $cols->getResource()->getAttribute('ram')->setStoreId($store_id)->getFrontend()->getValue($cols);
                
                if($ram!=''){
                    $desc = $desc.",  RAM:".$ram;
                }
                
                $mobileNetwork = $cols->getResource()->getAttribute('mobile_network_type')->setStoreId($store_id)->getFrontend()->getValue($cols);
                
                if($mobileNetwork!=''){
                   $desc = $desc.", Mobiele internetverbinding:".$mobileNetwork; 
                }
                $twoGband = $cols->getResource()->getAttribute('bands_2g')->setStoreId($store_id)->getFrontend()->getValue($cols);
                $threeGband = $cols->getResource()->getAttribute('bands_3g')->setStoreId($store_id)->getFrontend()->getValue($cols);
                //$fourGband = $cols->getResource()->getAttribute('bands_4g')->setStoreId($store_id)->getFrontend()->getValue($cols);
                $fourGband = $cols->getBands4g();
                
                if($twoGband!=''){
                    $desc = $desc.", Frequenties2G (MHz):". $twoGband;
                }
                if($threeGband!=''){
                    $desc = $desc.", Frequenties3G (MHz):". $threeGband;
                }
                if($fourGband!=''){
                    $desc = $desc.", Frequenties4G (MHz):". $fourGband;
                }
                $bluetooth = $cols->getBluetooth();
                if($bluetooth==1){
                    $desc = $desc.", Bluetooth: Ja";
                }
                $wifi = $cols->getWifi();
                if($wifi==1){
                    $desc = $desc.", WIFI: Ja";
                }
                $gps = $cols->getGps();
                if($wifi==1){
                    $desc = $desc.", GPS: Ja";
                }
                $usb = $cols->getUsb();
                if($usb==1){
                    $desc = $desc.", USB: Ja";
                }
                $wireless = $cols->getWirelessCharging();
                if($wireless==1){
                    $desc = $desc.", Draadloos opladen: Ja";
                }
                $nfc = $cols->getNfc();
                if($nfc==1){
                    $desc = $desc.", NFC: Ja";
                }
                $hdVoice = $cols->getHdVoice(); 
                if($hdVoice==1){
                    $desc = $desc.", HD Voice: Ja";
                }
                
                $primaryCamera = $cols->getResource()->getAttribute('primary_camera_resolution')->setStoreId($store_id)->getFrontend()->getValue($cols);
                $desc = $desc.", Camera:".$primaryCamera;
                $flash = $cols->getFlash();
                if($flash==1){
                    $desc = $desc.", Flash: Ja";
                }
                $secondaryCamera = $cols->getResource()->getAttribute('secondary_camera_resolution')->setStoreId($store_id)->getFrontend()->getValue($cols);
                $desc = $desc.", Front camera:".$secondaryCamera;
                $video = $cols->getVideoCapture();
                if($video == 1){
                    $desc = $desc.", VideoHD: Ja";
                }
                $mplayer = $cols->getData('mp3_player'); 
                if($mplayer == 1){
                    $desc = $desc.", MP3: Ja";
                }
                $fmRadio = $cols->getFmRadio();
                if($fmRadio == 1){
                    $desc = $desc.", Radio FM: Ja";
                }
                if($cols->getSarDarDetails()!=''){
                    $desc = $desc.", SAT waarde (Specifiek absorptie tempo):".$cols->getSarDarDetails();
                }
            }
            $desc = ltrim($desc, ',');
            $desc = substr($desc,0,1000);
            
            //$product = $this->_productRepository->getById($cols->getId());
            //$productUrl = $product->getUrlModel()->getUrl($product);
            //$productUrl = $product->setStoreId($store_id)->getProductUrl();
            $productUrl = $baseurl.$cols->getUrlKey();
            
            $productName = $cols->getName();
            
            if ($cols->getStatus() == '1') {
                $status = 'in stock';
            } else {
                $status = 'out of stock';
            }
            $productSku = $cols->getSku().''.$stores;
            $productSku = str_replace(' ', '', $productSku);
            if(!empty($cols->getProductIconImage())){
                $productType = $cols->getResource()->getAttribute('product_icon_image')->getFrontend()->getValue($cols);
                if($productType=="smartphone"){
                    $product_type = "Mobile > Hardware > Smartphones and GSM > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Communications > Telephony > Mobile Phones > Smartphones";
                }else if($productType=="shop-accessories"){
                    $product_type = "Mobile > Hardware > Accessories > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Communications > Telephony > Mobile Phone Accessories";
                }else if($productType=="tablet"){
                    $product_type = "Mobile > Hardware > Tablets > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Computers > Tablet Computers";
                }else if($productType=="modem"){
                    $product_type = "Mobile > Hardware > Modems > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Networking > Modems";
                }
                else if($productType=="connection"){
                    $product_type = "Mobile > Hardware > Connected devices > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Electronics Accessories";
                }
            }
            $color = $cols->getResource()->getAttribute('color')->setStoreId($store_id)->getFrontend()->getValue($cols);
			$color = htmlspecialchars(strip_tags($color));
            if(empty($color)){
                  $color = ""; 
            }
            
            $ean = $cols->getData('european_article_number_1');
            
            if(!empty($cols->getData('cashback_stand_alone_b2c'))){
                $cashback = 'Yes';
            }else {
                $cashback = 'No';
            }
            
            $nintendo_ids = $cols->getUpSellProductIds();
            
            if (count($nintendo_ids) > 0){
                $nintendo_product = $this->_productRepository->getById($nintendo_ids[0]);
                
                if($productType=="smartphone"){
                    $product_type = "Mobile > Subscriptions with Smartphone > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Communications > Telephony > Mobile Phones > Smartphones";
                }
                $typeInstance = $nintendo_product->getTypeInstance(true);
                $requiredChildrenIds = $typeInstance->getChildrenIds($nintendo_ids[0], false);
                $price = ROUND($nintendo_product->getPrice(),2);
                foreach($requiredChildrenIds as $bundle_item_ids => $item_ids){ 
                    foreach($item_ids as $item_id){
                       $bundle_item = $this->_productRepository->getById($item_id);
                       //$price = ROUND($bundle_item->getPrice(),2);                       
                        if($bundle_item->getTypeId()=='virtual'){
                            $plan = $bundle_item->getName(); 
                            $tarrif_plan = ROUND($bundle_item->getSubscriptionAmount(),2);
                            $subs_duration = $bundle_item->getSubsidyDuration();
                            if(!empty($subs_duration)){
                                $subsidy_duration = $subs_duration.' months';
                            } else {
                                $subsidy_duration = '24 months';
                            }
                        }
                    }                    
                }
                if(!empty($tarrif_plan)){
                    $tarrif_plan = $tarrif_plan.' EUR';
                }
                $productName = $productName.' met '.$plan.' '.$product_ext;
                $productName = substr($productName,0,150);
                $abonnement = 'abonnement';
            }else {
                $price = ROUND($cols->getPrice(),2);
                $abonnement = 'standalone';
                $plan = '';
                $tarrif_plan = '';
                $subsidy_duration = '';
                $cashback = '';
                $productName = $productName.' '.$product_ext;
                $productName = substr($productName,0,150);
            }
            
            if(!empty($cols->getData('handset_family'))){
                $item_group_id = $cols->getData('handset_family');
            }else if(!empty($cols->getData('accessory_family'))){
                $item_group_id = $cols->getData('accessory_family');
            }
            
            //$productName = $productName.' '.$product_ext;
            
//            foreach ($cols->getCategoryIds() as $key => $val) { //getting category name of product
//                $categoryId = $val;                
//                $category = $this->categoryModel->load($categoryId);
//                //$parentName = $category->getId();
//                $Name = $category->getName().'>'.$Name;
//                
////                $categoryObj = $this->categoryRepository->get($category->getId());
////                $subcategories = $categoryObj->getChildrenCategories();
////                foreach ($subcategories as $subcategorie) {
////                    $raw = $subcategorie->getName();
////                }
//            }
//            rtrim($Name,'>');
            //$Name = 'Electronics > Communications > Telephony > Mobile Phones';
            $item = $xml->channel->addChild('item');            
            $item->addChild('g:ID', $productSku, $ns);
            $item->addChild('title', htmlspecialchars(strip_tags($productName)));
            $item->addChild('description', htmlspecialchars(strip_tags($desc)));
            $item->addChild('g:item_group_id', htmlspecialchars(strip_tags($item_group_id)), $ns);            
            $item->addChild('link', $productUrl);
            $item->addChild('g:image_link', $pics, $ns);
            $item->addChild('g:google_product_category', $google_product_cat, $ns);
            $item->addChild('g:condition', 'new', $ns);
            $item->addChild('g:availability', $status, $ns);
            $item->addChild('g:price', $price.' EUR', $ns);
            $item->addChild('g:brand', $brandName, $ns); 
            $item->addChild('g:product_type',$product_type,$ns);
            $item->addChild('g:gtin', $ean,$ns);
            $item->addChild('g:color', $color,$ns);
            $item->addChild('g:shipping', '0 EUR', $ns);
            $item->addChild('g:abonnement', $abonnement, $ns);
            $item->addChild('g:type_abonnement', $plan, $ns);
            $item->addChild('g:prix_abonnement', $tarrif_plan, $ns);
            $item->addChild('g:durée_abonnement', $subsidy_duration, $ns); 
            $item->addChild('g:cashback', $cashback, $ns);           
        }
            $name = strftime($stores.'_mobistar.xml'); 
            $path=self::REPORTPATH;
            $content=$xml->asXML();
            $WPAfile = $path . $name;            	
            header('Content-disposition: attachment; filename="'.$name.'"');
            header('Content-type: "text/xml"; charset="utf8"');
			
			if (is_dir($path) === false) {
				mkdir($path);
				chmod($path,0777);
			}
			
            file_put_contents($WPAfile,$content);
            //echo $content;
	    exit;
 }  


    private function getCategory($categoryId) {
        $category = $this->_categoryFactory->create();
        $category->load($categoryId);
        return $category->getName();
    }

    private function getProductCollection($stores, $attribute_set_id) {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->setStore($stores);
        $collection->addAttributeToFilter('attribute_set_id', array("in" => $attribute_set_id));
        $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->addAttributeToFilter('type_id', array('neq' => 'virtual'));
        $collection->addAttributeToFilter('type_id', array('neq' => 'bundle'));
        //ignoring postpaid ,prepaid,simcard and accessory products        
        //foreach ($attribute_set_id as $key => $val){        
    // $collection->addAttributeToFilter('attribute_set_id', array("eq" => "12"));
        //}
        //$collection->addAttributeToFilter('status', 1);
        //$collection->setPageSize(1); // fetching only 3 products
         /*$collection->addAttributeToFilter(
                array(
            'attribute_set_id'
                ), array(
            array('eq' => "12")
        ));*/
        //$collection->addAttributeToFilter(array('attribute_set_id'), array(array("eq" => "12")));
        //$collection->addAttributeToFilter(array('attribute' => 'attribute_set_id','eq' => '12'));
        /*$collection->addAttributeToFilter(array(
                                   array(
                                       'attribute' => 'attribute_set_id',
                                       'eq' => '12'
                                   ),
                                   array(
                                     'attribute' => 'attribute_set_id',
                                       'eq' => '4'
                                   )                                    
                    )); */
       /* if($attribute_set_id != ''){
             
            foreach ($attribute_set_id as $key => $val){ 
               $orCondition[] = array(
                                       'attribute' => 'attribute_set_id',
                                       'eq' => $val
                                   );                    
            }
        }  */        
        //print_r($collection->getSelect()->__toString());exit;
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
    
    public function getDownloadableFeeds() {
        
        $attribute_ids = $this->_scopeConfig->getValue('googlefeed_sec/googlefeed_configuration/attributelist', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $attribute_set_id = explode(',', $attribute_ids);
        
        $store_id = 2;
        $stores = 'fr';
        $product_ext = 'chez Orange';
        
        $product_type ='';
        $plan = '';
        $tarrif_plan = '';
        $subsidy_duration = '';
        $cashback = '';
        $brandName = '';
        $attribute_name = '';
        $internalMemory = '';
        $sim_type = '';
        $os_detail = '';
        $processorType = '';
        $processorSpeed = '';
        $ram ='';
        $mobileNetwork ='';
        $twoGband ='';
        $threeGband = '';
        $fourGband = '';
        $bluetooth = '';
        $wifi = '';
        $usb = '';
        $wireless = '';
        $nfc = '';
        $hdVoice ='';
        $primaryCamera = '';
        $secondaryCamera = '';
        $mplayer ='';
        $fmRadio = '';
        $desc = '';
        $abonnement = 'standalone';
        $color = '';
        
        $collection = $this->getProductCollection($stores,$attribute_set_id);
        $baseurl = $this->_storeManager->getStore($store_id)->getBaseUrl();
        
        
        header('Content-Type: text/xml; charset=utf-8', true);
        $xml = new \SimpleXMLElement('<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0"></rss>');
        $xml->addChild('channel');
        $xml->channel->addChild('title', 'fr_orange');
        $xml->channel->addChild('link', 'https://eshop.orange.be/');
        $xml->channel->addChild('description', 'All products for the fr orange feed');
        // add item element for each article                        
        $ns = "http://base.google.com/ns/1";  
        foreach ($collection as $key => $cols) {
            $image = $cols->getImage(); //getting product image
            
            $attributeSetRepository = $this->_attributeSet->get($cols->getAttributeSetId());
            $attribute_name = $attributeSetRepository->getAttributeSetName();
            
            $mediaPath = $this->getStores()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            //$imagePath = $mediaPath . "catalog/product" . $image;
            $pics = $mediaPath . "catalog/product" . $image;
            if (!empty($cols->getBrand())) { //converting the brand from option id to option value
                $brandName = $cols->getResource()->getAttribute('brand')->setStoreId($store_id)->getFrontend()->getValue($cols);
				$brandName = htmlspecialchars(strip_tags($brandName));
            } else {
                $brandName = ""; //if no brand is specified then null
            }
            $desc = htmlspecialchars(strip_tags($cols->getMarketingDescription()));            
            
            if($attribute_name != "Accessories"){
                
					$desc = $desc.", Taille de l'écran:".$cols->getData('screen_size').", Résolution de l'écran:".$cols->getData('screen_resolution');
					if($cols->getLength() !='' && $cols->getWidth()!='' && $cols->getThicknessHeight()!=''){
						$desc = $desc.", Dimensions:".floatval($cols->getLength())." x ".floatval($cols->getWidth())." x ".floatval($cols->getThicknessHeight())."mm";
					}
					
					$desc = $desc.", Poids:".floatval($cols->getWeight())." g".", Capacité de la batterie:".$cols->getBatteryCharge();
					if($cols->getRemovableBattery() == 1){
						$desc = $desc.", Batterie amovible: Oui";
					}else{
						$desc = $desc.", Batterie amovible: Non";
					}
					
					$internalMemory = $cols->getResource()->getAttribute('internal_memory')->setStoreId($store_id)->getFrontend()->getValue($cols);
					
					if($internalMemory !=''){
						$desc = $desc.", Mémoire interne:".$internalMemory;
					}
					$sim_type = $cols->getResource()->getAttribute('sim_type')->setStoreId($store_id)->getFrontend()->getValue($cols);
					
					if($sim_type!=''){
						$desc = $desc. ", Format de carte SIM:". $sim_type;
					}
					$os_detail = $cols->getResource()->getAttribute('os_details')->setStoreId($store_id)->getFrontend()->getValue($cols);
					
					if($os_detail!=''){
						$desc = $desc. ", Système d'exploitation:". $os_detail;
					}
					//$processorType = $cols->getResource()->getAttribute('processor')->setStoreId($store_id)->getFrontend()->getValue($cols);
					$processorType = $cols->getProcessor();
					
					$processorSpeed = $cols->getResource()->getAttribute('processor_speed')->setStoreId($store_id)->getFrontend()->getValue($cols); 
					
					$desc = $desc. ", Processeur:".$processorType." x ".$processorSpeed;
					
					$ram = $cols->getResource()->getAttribute('ram')->setStoreId($store_id)->getFrontend()->getValue($cols);
					
					if($ram!=''){
						$desc = $desc.",  RAM:".$ram;
					}
					
					$mobileNetwork = $cols->getResource()->getAttribute('mobile_network_type')->setStoreId($store_id)->getFrontend()->getValue($cols);
					
					if($mobileNetwork!=''){
					   $desc = $desc.", Accès internet mobile:".$mobileNetwork; 
					}
					$twoGband = $cols->getResource()->getAttribute('bands_2g')->setStoreId($store_id)->getFrontend()->getValue($cols);
					$threeGband = $cols->getResource()->getAttribute('bands_3g')->setStoreId($store_id)->getFrontend()->getValue($cols);
					//$fourGband = $cols->getResource()->getAttribute('bands_4g')->setStoreId($store_id)->getFrontend()->getValue($cols);
					$fourGband = $cols->getBands4g();
					
					if($twoGband!=''){
						$desc = $desc.", Fréquences2G (MHz):". $twoGband;
					}
					if($threeGband!=''){
						$desc = $desc.", Fréquences3G (MHz):". $threeGband;
					}
					if($fourGband!=''){
						$desc = $desc.", Fréquences4G (MHz):". $fourGband;
					}
					$bluetooth = $cols->getBluetooth();
					if($bluetooth==1){
						$desc = $desc.", Bluetooth: Oui";
					}
					$wifi = $cols->getWifi();
					if($wifi==1){
						$desc = $desc.", WIFI: Oui";
					}
					$gps = $cols->getGps();
					if($wifi==1){
						$desc = $desc.", GPS: Oui";
					}
					$usb = $cols->getUsb();
					if($usb==1){
						$desc = $desc.", USB: Oui";
					}
					$wireless = $cols->getWirelessCharging();
					if($wireless==1){
						$desc = $desc.", Recharge sans fil: Oui";
					}
					$nfc = $cols->getNfc();
					if($nfc==1){
						$desc = $desc.", NFC: Oui";
					}
					$hdVoice = $cols->getHdVoice(); 
					if($hdVoice==1){
						$desc = $desc.", HD Voice: Oui";
					}
					
					$primaryCamera = $cols->getResource()->getAttribute('primary_camera_resolution')->setStoreId($store_id)->getFrontend()->getValue($cols);
					$desc = $desc.", Appareil photo:".$primaryCamera;
					$flash = $cols->getFlash();
					if($flash==1){
						$desc = $desc.", Flash: Oui";
					}
					$secondaryCamera = $cols->getResource()->getAttribute('secondary_camera_resolution')->setStoreId($store_id)->getFrontend()->getValue($cols);
					$desc = $desc.", Appareil photo avant:".$secondaryCamera;
					$video = $cols->getVideoCapture();
					if($video == 1){
						$desc = $desc.", VidéoHD:Oui";
					}
					$mplayer = $cols->getData('mp3_player'); 
					if($mplayer == 1){
						$desc = $desc.", MP3:Oui";
					}
					$fmRadio = $cols->getFmRadio();
					if($fmRadio == 1){
						$desc = $desc.", Radio FM:Oui";
					}
					if($cols->getSarDarDetails()!=''){
						$desc = $desc.", Valeur DAS (Débit d'absorption spécifique):".$cols->getSarDarDetails();
					}
				
            }
            $desc = ltrim($desc, ',');
            $desc = substr($desc,0,1000);
            
            //$product = $this->_productRepository->getById($cols->getId());
            //$productUrl = $product->getUrlModel()->getUrl($product);
            //$productUrl = $product->setStoreId($store_id)->getProductUrl();
            $productUrl = $baseurl.$cols->getUrlKey();
            $productName = $cols->getName();

            
            if ($cols->getStatus() == '1') {
                $status = 'in stock';
            } else {
                $status = 'out of stock';
            }
            $productSku = $cols->getSku().''.$stores;
            $productSku = str_replace(' ', '', $productSku);
            if(!empty($cols->getProductIconImage())){
                $productType = $cols->getResource()->getAttribute('product_icon_image')->getFrontend()->getValue($cols);
                if($productType=="smartphone"){
                    $product_type = "Mobile > Hardware > Smartphones and GSM > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Communications > Telephony > Mobile Phones > Smartphones";
                }else if($productType=="shop-accessories"){
                    $product_type = "Mobile > Hardware > Accessories > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Communications > Telephony > Mobile Phone Accessories";
                }else if($productType=="tablet"){
                    $product_type = "Mobile > Hardware > Tablets > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Computers > Tablet Computers";
                }else if($productType=="modem"){
                    $product_type = "Mobile > Hardware > Modems > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Networking > Modems";
                }
                else if($productType=="connection"){
                    $product_type = "Mobile > Hardware > Connected devices > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Electronics Accessories";
                }
            }
            
            $color = $cols->getResource()->getAttribute('color')->setStoreId($store_id)->getFrontend()->getValue($cols);
			$color = htmlspecialchars(strip_tags($color));
            if(empty($color)){
                  $color = ""; 
            }
            
            $ean = $cols->getData('european_article_number_1');
            
            if(!empty($cols->getData('cashback_stand_alone_b2c'))){
                $cashback = 'Yes';
            }else {
                $cashback = 'No';
            }
            
				$nintendo_ids = $cols->getUpSellProductIds();
            
				if (count($nintendo_ids) > 0){
					$nintendo_product = $this->_productRepository->getById($nintendo_ids[0]);
					
					if($productType=="smartphone"){
						$product_type = "Mobile > Subscriptions with Smartphone > ".$brandName;
						$product_type = rtrim($product_type,' > ');
						$google_product_cat = "Electronics > Communications > Telephony > Mobile Phones > Smartphones";
					}
					
					$typeInstance = $nintendo_product->getTypeInstance(true);
					$requiredChildrenIds = $typeInstance->getChildrenIds($nintendo_ids[0], false);
					$price = ROUND($nintendo_product->getPrice(),2);
					foreach($requiredChildrenIds as $bundle_item_ids => $item_ids){ 
						foreach($item_ids as $item_id){
						   $bundle_item = $this->_productRepository->getById($item_id);
						   //$price = ROUND($bundle_item->getPrice(),2);                       
							if($bundle_item->getTypeId()=='virtual'){
								$plan = $bundle_item->getName(); 
								$tarrif_plan = ROUND($bundle_item->getSubscriptionAmount(),2);
								$subs_duration = $bundle_item->getSubsidyDuration();
								if(!empty($subs_duration)){
									$subsidy_duration = $subs_duration.' months';
								} else {
									$subsidy_duration = '24 months';
								}
							}
						}                    
					}
					if(!empty($tarrif_plan)){
						$tarrif_plan = $tarrif_plan.' EUR';
					}
					$productName = $productName.' avec '.$plan.' '.$product_ext;
					$productName = substr($productName,0,150);
					$abonnement = 'abonnement';
				}else {
					$price = ROUND($cols->getPrice(),2);
					$abonnement = 'standalone';
					$plan = '';
					$tarrif_plan = '';
					$subsidy_duration = '';
					$cashback = '';
					$productName = $productName.' '.$product_ext;
					$productName = substr($productName,0,150);
				}
			
            if(!empty($cols->getData('handset_family'))){
                $item_group_id = $cols->getData('handset_family');
            }else if(!empty($cols->getData('accessory_family'))){
                $item_group_id = $cols->getData('accessory_family');
            }
            
            //$productName = $productName.' '.$product_ext;
            
//            foreach ($cols->getCategoryIds() as $key => $val) { //getting category name of product
//                $categoryId = $val;                
//                $category = $this->categoryModel->load($categoryId);
//                //$parentName = $category->getId();
//                $Name = $category->getName().'>'.$Name;
//                
////                $categoryObj = $this->categoryRepository->get($category->getId());
////                $subcategories = $categoryObj->getChildrenCategories();
////                foreach ($subcategories as $subcategorie) {
////                    $raw = $subcategorie->getName();
////                }
//            }
            //rtrim($Name,'>');
            //$Name = 'Electronics > Communications > Telephony > Mobile Phones';
            $item = $xml->channel->addChild('item');            
            $item->addChild('g:ID', $productSku, $ns);
            $item->addChild('title', htmlspecialchars(strip_tags($productName)));
            $item->addChild('description', htmlspecialchars(strip_tags($desc)));
            $item->addChild('g:item_group_id', htmlspecialchars(strip_tags($item_group_id)), $ns);            
            $item->addChild('link', $productUrl);
            $item->addChild('g:image_link', $pics, $ns);
            $item->addChild('g:google_product_category', $google_product_cat, $ns);
            $item->addChild('g:condition', 'new', $ns);
            $item->addChild('g:availability', $status, $ns);
            $item->addChild('g:price', $price.' EUR', $ns);
            $item->addChild('g:brand', $brandName, $ns); 
            $item->addChild('g:product_type',$product_type,$ns);
            $item->addChild('g:gtin', $ean,$ns);
            $item->addChild('g:color', $color,$ns);
            $item->addChild('g:shipping', '0 EUR', $ns);
            $item->addChild('g:abonnement', $abonnement, $ns);
            $item->addChild('g:type_abonnement', $plan, $ns);
            $item->addChild('g:prix_abonnement', $tarrif_plan, $ns);
            $item->addChild('g:durée_abonnement', $subsidy_duration, $ns); 
            $item->addChild('g:cashback', $cashback, $ns);
        }   
        
	    $name = strftime($stores.'_mobistar.xml');
	    $path=self::REPORTPATH;
        $WPAfile = $path . $name;
        $content=$xml->asXML();        	
        header('Content-disposition: attachment; filename="'.$name.'"');
        header('Content-type: "text/xml"; charset="utf8"');
		
		if (is_dir($path) === false) {
			mkdir($path);
			chmod($path,0777);
		}
		
        file_put_contents($WPAfile,$content);
        //file_put_contents('var/google_feed/',$content);
	    echo $content;
        
	    exit;
        }
    
        public function getDownloadableFeedsNL() { 
        $attribute_ids = $this->_scopeConfig->getValue('googlefeed_sec/googlefeed_configuration/attributelist', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $attribute_set_id = explode(',', $attribute_ids);
		
        $stores = 'nl';
        $store_id = 1;
        $product_ext = 'bij Orange';        
        
        $product_type ='';
        $plan = '';
        $tarrif_plan = '';
        $subsidy_duration = '';
        $cashback = '';
        $brandName = '';
        $attribute_name = '';
        $internalMemory = '';
        $sim_type = '';
        $os_detail = '';
        $processorType = '';
        $processorSpeed = '';
        $ram ='';
        $mobileNetwork ='';
        $twoGband ='';
        $threeGband = '';
        $fourGband = '';
        $bluetooth = '';
        $wifi = '';
        $usb = '';
        $wireless = '';
        $nfc = '';
        $hdVoice ='';
        $primaryCamera = '';
        $secondaryCamera = '';
        $mplayer ='';
        $fmRadio = '';
        $desc = '';
        $abonnement = 'standalone';
        $color = '';
        
        $collection = $this->getProductCollection($stores,$attribute_set_id);
        $baseurl = $this->_storeManager->getStore($store_id)->getBaseUrl();
        
        header('Content-Type: text/xml; charset=utf-8', true);
        $xml = new \SimpleXMLElement('<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0"></rss>');
        $xml->addChild('channel');
        $xml->channel->addChild('title', 'nl_orange');
        $xml->channel->addChild('link', 'https://eshop.orange.be/');
        $xml->channel->addChild('description', 'All products for the nl orange feed');
        // add item element for each article                        
        $ns = "http://base.google.com/ns/1";  
        foreach ($collection as $key => $cols) {
            $image = $cols->getImage(); //getting product image
            
            $attributeSetRepository = $this->_attributeSet->get($cols->getAttributeSetId());
            $attribute_name = $attributeSetRepository->getAttributeSetName();
            
            $mediaPath = $this->getStores()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            //$imagePath = $mediaPath . "catalog/product" . $image;
            $pics = $mediaPath . "catalog/product" . $image;
            
            if (!empty($cols->getBrand())) { //converting the brand from option id to option value
                $brandName = $cols->getResource()->getAttribute('brand')->setStoreId($store_id)->getFrontend()->getValue($cols);
				$brandName = htmlspecialchars(strip_tags($brandName));
            } else {
                $brandName = ""; //if no brand is specified then null
            }
            $desc = htmlspecialchars(strip_tags($cols->getMarketingDescription()));
			
            if($attribute_name != "Accessories"){
                
					$desc = $desc.", Schermgrootte:".$cols->getData('screen_size').", Schermresolutie:".$cols->getData('screen_resolution');
					if($cols->getLength() !='' && $cols->getWidth()!='' && $cols->getThicknessHeight()!=''){
						$desc = $desc.", Afmeting:".floatval($cols->getLength())." x ".floatval($cols->getWidth())." x ".floatval($cols->getThicknessHeight())."mm";
					}
					
					$desc = $desc.", Gewicht:".floatval($cols->getWeight())." g".", Capaciteit van de batterij:".$cols->getBatteryCharge();
					if($cols->getRemovableBattery() == 1){
						$desc = $desc.", Verwijderbare batterij: Oui";
					}else{
						$desc = $desc.", Verwijderbare batterij: Non";
					}
					
					$internalMemory = $cols->getResource()->getAttribute('internal_memory')->setStoreId($store_id)->getFrontend()->getValue($cols);
					
					if($internalMemory !=''){
						$desc = $desc.", Intern geheugen:".$internalMemory;
					}
					$sim_type = $cols->getResource()->getAttribute('sim_type')->setStoreId($store_id)->getFrontend()->getValue($cols);
					
					if($sim_type!=''){
						$desc = $desc. ", Simkaartformaat:". $sim_type;
					}
					$os_detail = $cols->getResource()->getAttribute('os_details')->setStoreId($store_id)->getFrontend()->getValue($cols);
					
					if($os_detail!=''){
						$desc = $desc. ", Besturingssysteem:". $os_detail;
					}
					//$processorType = $cols->getResource()->getAttribute('processor')->setStoreId($store_id)->getFrontend()->getValue($cols);
					$processorType = $cols->getProcessor();
					
					$processorSpeed = $cols->getResource()->getAttribute('processor_speed')->setStoreId($store_id)->getFrontend()->getValue($cols); 
					
					$desc = $desc. ", BewerkerQuad :".$processorType." x ".$processorSpeed;
					
					$ram = $cols->getResource()->getAttribute('ram')->setStoreId($store_id)->getFrontend()->getValue($cols);
					
					if($ram!=''){
						$desc = $desc.",  RAM:".$ram;
					}
					
					$mobileNetwork = $cols->getResource()->getAttribute('mobile_network_type')->setStoreId($store_id)->getFrontend()->getValue($cols);
					
					if($mobileNetwork!=''){
					   $desc = $desc.", Mobiele internetverbinding:".$mobileNetwork; 
					}
					$twoGband = $cols->getResource()->getAttribute('bands_2g')->setStoreId($store_id)->getFrontend()->getValue($cols);
					$threeGband = $cols->getResource()->getAttribute('bands_3g')->setStoreId($store_id)->getFrontend()->getValue($cols);
					//$fourGband = $cols->getResource()->getAttribute('bands_4g')->setStoreId($store_id)->getFrontend()->getValue($cols);
					$fourGband = $cols->getBands4g();
					
					if($twoGband!=''){
						$desc = $desc.", Frequenties2G (MHz):". $twoGband;
					}
					if($threeGband!=''){
						$desc = $desc.", Frequenties3G (MHz):". $threeGband;
					}
					if($fourGband!=''){
						$desc = $desc.", Frequenties4G (MHz):". $fourGband;
					}
					$bluetooth = $cols->getBluetooth();
					if($bluetooth==1){
						$desc = $desc.", Bluetooth: Ja";
					}
					$wifi = $cols->getWifi();
					if($wifi==1){
						$desc = $desc.", WIFI: Ja";
					}
					$gps = $cols->getGps();
					if($wifi==1){
						$desc = $desc.", GPS: Ja";
					}
					$usb = $cols->getUsb();
					if($usb==1){
						$desc = $desc.", USB: Ja";
					}
					$wireless = $cols->getWirelessCharging();
					if($wireless==1){
						$desc = $desc.", Draadloos opladen: Ja";
					}
					$nfc = $cols->getNfc();
					if($nfc==1){
						$desc = $desc.", NFC: Ja";
					}
					$hdVoice = $cols->getHdVoice(); 
					if($hdVoice==1){
						$desc = $desc.", HD Voice: Ja";
					}
					
					$primaryCamera = $cols->getResource()->getAttribute('primary_camera_resolution')->setStoreId($store_id)->getFrontend()->getValue($cols);
					$desc = $desc.", Camera:".$primaryCamera;
					$flash = $cols->getFlash();
					if($flash==1){
						$desc = $desc.", Flash: Ja";
					}
					$secondaryCamera = $cols->getResource()->getAttribute('secondary_camera_resolution')->setStoreId($store_id)->getFrontend()->getValue($cols);
					$desc = $desc.", Front camera:".$secondaryCamera;
					$video = $cols->getVideoCapture();
					if($video == 1){
						$desc = $desc.", VideoHD: Ja";
					}
					$mplayer = $cols->getData('mp3_player'); 
					if($mplayer == 1){
						$desc = $desc.", MP3: Ja";
					}
					$fmRadio = $cols->getFmRadio();
					if($fmRadio == 1){
						$desc = $desc.", Radio FM: Ja";
					}
					if($cols->getSarDarDetails()!=''){
						$desc = $desc.", SAT waarde (Specifiek absorptie tempo):".$cols->getSarDarDetails();
					}
				
            }
            $desc = ltrim($desc, ',');
            $desc = substr($desc,0,1000);
            
            //$product = $this->_productRepository->getById($cols->getId());
            //$productUrl = $product->getUrlModel()->getUrl($product);
            //$productUrl = $product->setStoreId($store_id)->getUrlInStore();
            //$productUrl = $product->setStoreId($store_id)->getProductUrl();
            
            $productUrl = $baseurl.$cols->getUrlKey();
            $productName = $cols->getName();
            
            if ($cols->getStatus() == '1') {
                $status = 'in stock';
            } else {
                $status = 'out of stock';
            }
            $productSku = $cols->getSku().''.$stores;
            $productSku = str_replace(' ', '', $productSku);
            if(!empty($cols->getProductIconImage())){
                $productType = $cols->getResource()->getAttribute('product_icon_image')->getFrontend()->getValue($cols);
                if($productType=="smartphone"){
                    $product_type = "Mobile > Hardware > Smartphones and GSM > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Communications > Telephony > Mobile Phones > Smartphones";
                }else if($productType=="shop-accessories"){
                    $product_type = "Mobile > Hardware > Accessories > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Communications > Telephony > Mobile Phone Accessories";
                }else if($productType=="tablet"){
                    $product_type = "Mobile > Hardware > Tablets > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Computers > Tablet Computers";
                }else if($productType=="modem"){
                    $product_type = "Mobile > Hardware > Modems > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Networking > Modems";
                }
                else if($productType=="connection"){
                    $product_type = "Mobile > Hardware > Connected devices > ".$brandName;
                    $product_type = rtrim($product_type,' > ');
                    $google_product_cat = "Electronics > Electronics Accessories";
                }
            }
            $color = $cols->getResource()->getAttribute('color')->setStoreId($store_id)->getFrontend()->getValue($cols);
			$color = htmlspecialchars(strip_tags($color));
            if(empty($color)){
                  $color = ""; 
            }
            
            $ean = $cols->getData('european_article_number_1');
            
            if(!empty($cols->getData('cashback_stand_alone_b2c'))){
                $cashback = 'Yes';
            }else {
                $cashback = 'No';
            }
            
				$nintendo_ids = $cols->getUpSellProductIds();
            
				if (count($nintendo_ids) > 0){
					$nintendo_product = $this->_productRepository->getById($nintendo_ids[0]);
					
					if($productType=="smartphone"){
						$product_type = "Mobile > Subscriptions with Smartphone > ".$brandName;
						$product_type = rtrim($product_type,' > ');
						$google_product_cat = "Electronics > Communications > Telephony > Mobile Phones > Smartphones";
					}
					
					$typeInstance = $nintendo_product->getTypeInstance(true);
					$requiredChildrenIds = $typeInstance->getChildrenIds($nintendo_ids[0], false);
					$price = ROUND($nintendo_product->getPrice(),2);
					
					foreach($requiredChildrenIds as $bundle_item_ids => $item_ids){ 
						foreach($item_ids as $item_id){
						   $bundle_item = $this->_productRepository->getById($item_id);
						   //$price = ROUND($bundle_item->getPrice(),2);                       
							if($bundle_item->getTypeId()=='virtual'){
								$plan = $bundle_item->getName(); 
								$tarrif_plan = ROUND($bundle_item->getSubscriptionAmount(),2);
								$subs_duration = $bundle_item->getSubsidyDuration();
								if(!empty($subs_duration)){
									$subsidy_duration = $subs_duration.' months';
								} else {
									$subsidy_duration = '24 months';
								}
							}
						}                    
					}
					if(!empty($tarrif_plan)){
						$tarrif_plan = $tarrif_plan.' EUR';
					}
					$productName = $productName.' met '.$plan.' '.$product_ext;
					$productName = substr($productName,0,150);
					$abonnement = 'abonnement';
				}else {
					$price = ROUND($cols->getPrice(),2);
					$abonnement = 'standalone';
					$plan = '';
					$tarrif_plan = '';
					$subsidy_duration = '';
					$cashback = '';
					$productName = $productName.' '.$product_ext;
					$productName = substr($productName,0,150);
				}
			
            if(!empty($cols->getData('handset_family'))){
                $item_group_id = $cols->getData('handset_family');
            }else if(!empty($cols->getData('accessory_family'))){
                $item_group_id = $cols->getData('accessory_family');
            }
            
            //$productName = $productName.' '.$product_ext;
            
//            foreach ($cols->getCategoryIds() as $key => $val) { //getting category name of product
//                $categoryId = $val;                
//                $category = $this->categoryModel->load($categoryId);
//                //$parentName = $category->getId();
//                $Name = $category->getName().'>'.$Name;
//                
////                $categoryObj = $this->categoryRepository->get($category->getId());
////                $subcategories = $categoryObj->getChildrenCategories();
////                foreach ($subcategories as $subcategorie) {
////                    $raw = $subcategorie->getName();
////                }
//            }
//            rtrim($Name,'>');
            //$Name = 'Electronics > Communications > Telephony > Mobile Phones';
            $item = $xml->channel->addChild('item');            
            $item->addChild('g:ID', $productSku, $ns);
            $item->addChild('title',  htmlspecialchars(strip_tags($productName)));
            $item->addChild('description', htmlspecialchars(strip_tags($desc)));
            $item->addChild('g:item_group_id', htmlspecialchars(strip_tags($item_group_id)), $ns);
            $item->addChild('link', $productUrl);
            $item->addChild('g:image_link', $pics, $ns);
            $item->addChild('g:google_product_category', $google_product_cat, $ns);
            $item->addChild('g:condition', 'new', $ns);
            $item->addChild('g:availability', $status, $ns);
            $item->addChild('g:price', $price.' EUR', $ns);
            $item->addChild('g:brand', $brandName, $ns); 
            $item->addChild('g:product_type',$product_type,$ns);
            $item->addChild('g:gtin', $ean,$ns);
            $item->addChild('g:color', $color,$ns);
            $item->addChild('g:shipping', '0 EUR', $ns);
            $item->addChild('g:abonnement', $abonnement, $ns);
            $item->addChild('g:type_abonnement', $plan, $ns);
            $item->addChild('g:prix_abonnement', $tarrif_plan, $ns);
            $item->addChild('g:durée_abonnement', $subsidy_duration, $ns); 
            $item->addChild('g:cashback', $cashback, $ns);
			
        }
		
		$name = strftime($stores.'_mobistar.xml');
		$path=self::REPORTPATH;
            $content=$xml->asXML();
        $WPAfile = $path . $name;
        header('Content-disposition: attachment; filename="'.$name.'"');
        header('Content-type: "text/xml"; charset="utf8"');
		
		if (is_dir($path) === false) {
			mkdir($path);
			chmod($path,0777);
		}
		
        file_put_contents($WPAfile,$content);
		
		echo $content;
	    exit;
        }
    
    
        }

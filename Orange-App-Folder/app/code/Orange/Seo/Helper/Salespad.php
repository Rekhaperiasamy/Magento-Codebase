<?php

namespace Orange\Seo\Helper;
use Magento\Customer\Model\Session;
use \Magento\Framework\Archive\Zip;
use \Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Bootstrap;

class Salespad extends \Magento\Framework\View\Element\Template {

protected $_productRepository;

 public function __construct(
 \Magento\Backend\Block\Template\Context $context,
 \Magento\Catalog\Model\ProductRepository $productRepository
  )
    {
		$this->_productRepository = $productRepository;
		parent::__construct($context);
    }
 public function generatesalsepad()
    {
$filecreate = $this->generateallfile();
$dir = 'media/common-header/salespadreport/';
$zip_file = 'media/salespad.zip';

// Get real path for our folder
 $rootPath = realpath($dir);
 $rootPath_zip = realpath($dir);
// Initialize archive object

$zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
     

	 

// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new \RecursiveIteratorIterator(
    new \RecursiveDirectoryIterator($rootPath),
    \RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file)
{
  // Skip directories (they would be added automatically)
    if (!$file->isDir())
    {
  // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

  
        // Add current file to archive
     $zip->addFile($filePath, $relativePath);
		
    }
}

// Zip archive will be created only after closing object
$zip->close();

//header('Content-Description: File Transfer');
//header('Content-Type: application/octet-stream');
//header('Content-Disposition: attachment; filename='.basename($zip_file));
//header('Content-Transfer-Encoding: binary');
//header('Expires: 0');
//header('Cache-Control: must-revalidate');
//header('Pragma: public');
//header('Content-Length: ' . filesize($zip_file));
//readfile($zip_file);
	echo $filecreate;	
	}

public function generateallfile(){
	


	require BP . '/app/bootstrap.php';
	$bootstrap = Bootstrap::create(BP, $_SERVER);
 $dir       = "media/common-header/salespadreport";
 $deletedir = "media/common-header/salespadreport/assets/handsets";


if (is_dir($deletedir)) {
   // $this->delTree($deletedir);
}
 
if (is_dir($dir) === false) {
    mkdir($dir);
	chmod($dir,0777);
}
$mediadir = $dir . '/assets';




if (is_dir($mediadir) === false) {
    mkdir($mediadir);
	chmod($mediadir,0777);
}
$media2dir = $dir . '/assets/handsets';

if (is_dir($media2dir) === false) {
    mkdir($media2dir);
	chmod($media2dir,0777);
}

$obj   = $bootstrap->getObjectManager();
// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

$lng[] = 'fr';
$lng[] = 'nl';
foreach($lng as $lang){
	$storevar          = $lang;
if($storevar == 'nl')
	$storeid = 1;
else
	$storeid = 2;

$store             = $obj->get('Magento\Store\Model\StoreManagerInterface')->getStore($storeid);
$productCollection = $obj->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
$collection        = $productCollection->create()->setStoreId($storeid)->addAttributeToFilter('attribute_set_id','12')->addAttributeToSelect('*')->load();
$directoryList     = $obj->get('Magento\Framework\App\Filesystem\DirectoryList');
$mediaPath         = $directoryList->getPath('media');

$xmlString         = "<?xml version='1.0' encoding='UTF-8'?><productFeed version='1.0'>";
	
foreach ($collection as $product) {

//Getting Product URL based on category	
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
	if (in_array($cid, $category)) {
	   $category_id  = $cid;
	}
	$objectManager 	= \Magento\Framework\App\ObjectManager::getInstance();
	$resource 		= $objectManager->get('Magento\Framework\App\ResourceConnection');
	$connectionDb 	= $resource->getConnection();
	$tableName 		= $resource->getTableName('url_rewrite');
	if($category_id != ''){
		$targetPath = "catalog/product/view/id/".$product->getId()."/category/".$category_id."";
		$sql = "Select request_path FROM " . $tableName . " WHERE (entity_type = 'product' and store_id = $storeid and target_path='$targetPath') ;";
		$result = $connectionDb->fetchAll($sql);
		if (!empty($result)) {
			$path = $result[0];
			$url = $store->getBaseUrl().$path['request_path'];	
		} else {			  
			$url = $store->getBaseUrl().$targetPath;
		}
	}else{			  
		$url = $product->setStoreId($storeid)->getProductUrl();
	  }
    $sku         = $product->getSku();
	ini_set("precision",25);
	$ean_num = $product->getEuropeanArticleNumber1();
	$xmlString .= "<product>
	<name>" . $product->getName() . "</name>
	<description></description>
	<sku>" . $product->getSku() . "</sku>
	<price>" . $product->getPrice() . "</price>
	<imageURL>" . $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA). 'catalog/product' . $product->getImage() ."</imageURL>
	<tac></tac>
	<ean>" . $product->getEuropeanArticleNumber1() . "</ean>
	<brand>" . $product->getAttributeText('brand') . "</brand>
	<model></model>
	<segment></segment>
	<catalogue>GSM</catalogue>
	<details1></details1>
	<details2></details2>
	<type>" . $product->getAttributeText('product_icon_image') . "</type>";

	$xmlString .= "<features>
		<score_total>" . ($product->getAttributeText('score_total') == '' ? '-' : $product->getAttributeText('score_total')) . "</score_total>
		<score_design>" . ($product->getAttributeText('score_design') == '' ? '-' : $product->getAttributeText('score_design')) . "</score_design>
		<score_perform>" . ($product->getAttributeText('score_perform')  == '' ? '-' : $product->getAttributeText('score_perform')). "</score_perform>
		<score_func>" . ($product->getAttributeText('score_func') == '' ? '-' : $product->getAttributeText('score_func')). "</score_func>
		<score_price_qt>" . ($product->getAttributeText('score_price_qt') == '' ? '-' : $product->getAttributeText('score_price_qt')). "</score_price_qt>
		<short_summary></short_summary>
		<photo>" . $product->getAttributeText('primary_camera_resolution') . "</photo>
		<video>" . $product->getResource()->getAttribute('video_capture')->getFrontend()->getValue($product) . "</video>
		<mp3>" . $product->getResource()->getAttribute('mp3_player')->getFrontend()->getValue($product) . "</mp3>
		<radio>" . $product->getResource()->getAttribute('fm_radio')->getFrontend()->getValue($product) . "</radio>
		<gps>" . $product->getResource()->getAttribute('gps')->getFrontend()->getValue($product) . "</gps>
		<multimedia>-</multimedia>
		<mms>-</mms>
		<web>-</web>
		<email>-</email>
		<usb>" . $product->getResource()->getAttribute('usb_port')->getFrontend()->getValue($product) . "</usb>
		<bluebooth>" . $product->getResource()->getAttribute('bluetooth')->getFrontend()->getValue($product) . "</bluebooth>
		<wifi>" . $product->getResource()->getAttribute('wifi')->getFrontend()->getValue($product) . "</wifi>
		<ThreeG>" . $product->getAttributeText('bands_3g') . "</ThreeG>
		<screen>" . $product->getScreenSize() . "</screen>
		<memory>" . $product->getInternalMemory() . "</memory>
		<touchscreen>" . $product->getTouchscreen() . "</touchscreen>
		<bat_standby>" . $product->getBatteryAutonomyStandby() . "</bat_standby>
		<bat_convers>" . $product->getBatteryAutonomyInUse() . "</bat_convers>
		<dimensions>" . $product->getLength() .' * '.$product->getThickness_height().' * '.$product->getWidth() . " mm</dimensions>
		<weight>" . floatVal($product->getWeight()) . " g</weight>
		<system>" . $product->getAttributeText('os_details') . "</system>
		<sms>-</sms>
		<system_version>" . $product->getAttributeText('os_details') . "</system_version>
		
		<score_web_browsing>-</score_web_browsing>
		<score_mail>-</score_mail>
		<score_taking_photos>-</score_taking_photos>
		<score_music_online>-</score_music_online>
		<score_music_offline>-</score_music_offline>
		<score_social_networking>-</score_social_networking>
		<score_application_range>-</score_application_range>
		<score_radio_fm>-</score_radio_fm>
		<score_gps_maps>-</score_gps_maps>
		
		<score_wifi_enabled>-</score_wifi_enabled>
		<score_games>-</score_games>
		<solid>-</solid>
		<sar>" . $product->getSarDarDetails() . "</sar>
		</features>
		<productURL>" . $url . "</productURL>
	</product>";
  
	//For saving images
	$imagemediadir = $media2dir . '/' . $ean_num . '/';
	if (is_dir($imagemediadir) === false && $ean_num != '') {
		mkdir($imagemediadir);
		chmod($imagemediadir,0777);
					
	}
	
 	$_product = $obj->create('\Magento\Catalog\Model\Product')->load($product->getId());

	$galleryData = $_product->getMediaGalleryImages();
	if ($ean_num != '') {
	foreach ($galleryData as $gallery):

		$galleryreplacement = str_replace('/', '/', $gallery['file']);
		$realImagePath      = 'media/catalog/product' . $galleryreplacement; //product image path 
		if (file_exists($realImagePath)) {
			$imagePath = explode('/', $gallery['file']);
			$imageName = end($imagePath);
			$imageDir  = $imagemediadir . $imageName;
			copy($realImagePath, $imageDir);
		}
	endforeach; 
	}

    
}
	
$xmlString .= "</productFeed>";

$dom = new \DomDocument("1.0", "utf-8");

$dom->preserveWhiteSpace = FALSE;
$dom->loadXML($xmlString);
  	
//Save XML as a file
if ($storevar == "nl") {
    $dom->save('media/common-header/salespadreport/salespad_NL.xml');
} else {
    $dom->save('media/common-header/salespadreport/salespad_FR.xml');
}
//View XML document
$dom->formatOutput = TRUE;
$dom->saveXml();
}
return 'Exported successfully';
}
function delTree($dir)
{
    $files = array_diff(scandir($dir), array(
        '.',
        '..',
        '...',
        '....'
    ));
   
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}



  
	}

<?php
namespace Orange\Seooptimization\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;

class CatalogProductSaveBefore implements ObserverInterface 
{
	
	protected $_storeManager;   
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {        
        $this->_storeManager = $context->getStoreManager();	
    }
	
    public function execute(\Magento\Framework\Event\Observer $observer) 
	{	
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$objectManager->get('Psr\Log\LoggerInterface')->addDebug('product save before');
        $product = $observer->getEvent()->getProduct();					
		$store = $this->_storeManager->getStore()->getCode();		
		$productType = $product->getTypeId();
		$productPrice = $product->getPrice();
		$metadesc = $product->getMetaDescription();
		$metatitle = $product->getMetaTitle();
		$productName = $product->getName();
		$productsku = $product->getSku();
		$attributeset = $product->getAttributeSetId();
		$attributesetname = $this->getattributesetName($attributeset);
		$mediaGallery = $product->getData('media_gallery');	//Get Media Gallery images	
		$metaProductTitle = '';		
		if($product->getData('meta_product_title') != '') {
			$metaAttribute = $product->getResource()->getAttribute('meta_product_title');
			$metaProductTitle = $metaAttribute->getSource()->getOptionText($product->getData('meta_product_title')); //Get Meta Product Title
		}
		$colorAttribute = $product->getResource()->getAttribute('color');
		$color = $colorAttribute->getSource()->getOptionText($product->getData('color')); //Get Color
		/*Update Url Key for postpaid */
	
		if($attributesetname == 'Postpaid' && $productType == "virtual" )
			{
			   $urlKey = $product->getUrlKey();
			   if ($productName != ""  && strpos($urlKey, '-orange') == false)
			   {
			    $furlKey = $urlKey."-orange";
				$product->setUrlKey($furlKey);
			   }
			}
		if($store == "admin") {
			/** update meta for nl store and admin store **/
			$productsku = $product->getSku();
	
			if (strpos($productsku, '_') !== false) {
				$afterstr = $this->after('_', $product->getSku());
				if($productType == "bundle"){
					$bundlemetadesc = "Koop je".$productName ." met ". $afterstr ." aan ".$productPrice; //Format Meta description for nintendo
					if($metadesc == '') {
						$product->setMetaDescription($bundlemetadesc); //set meta
					}
				}
			}
			if (strpos($productsku, '+') !== false) {
				$afterstr = $this->after('+', $product->getSku());
				if($productType == "bundle"){
					$bundlemetadesc = "Koop je".$productName ." met ". $afterstr ." aan ".$productPrice; //Format Meta description for nintendo					
					if($metadesc == '') {
						$product->setMetaDescription($bundlemetadesc); //set meta
					}					
				}
			}
			// foreach($mediaGallery['images'] as $prodImgId => $prodImg) {	
				// $labelTitle = str_replace($color,'',$productName);
				// $mediaGalleryData['images'][$prodImgId] = $prodImg;
				// $mediaGalleryData['images'][$prodImgId]['label'] = 'Orange België '.$labelTitle.' - '.$color; //set Image title
			// }
		}
		
		if($store == "fr") {
			/** update meta for fr store **/
			if (strpos($productsku, '+') !== false) {
				$afterstr = $this->after('+', $product->getSku());
				if($productType == "bundle"){
					$bundlemetadesc = "Acheter votre ".$productName ." avec ". $afterstr ." à ".$productPrice; //Format Meta description for nintendo										
					if($metadesc == '') {
						$product->setMetaDescription($bundlemetadesc); //set meta
					}					
				}
			}
			if(strpos($productsku, '_') !== false) {
				$afterstr = $this->after('_', $product->getSku());
				if($productType == "bundle"){
					$bundlemetadesc = "Acheter votre ".$productName ." avec ". $afterstr ." à ".$productPrice;					
					if($metadesc == '') {
						$product->setMetaDescription($bundlemetadesc); //set meta
					}							
				}
			}
			// foreach($mediaGallery['images'] as $prodImgId => $prodImg) {	
				// $labelTitle = str_replace($color,'',$productName);
				// $mediaGalleryData['images'][$prodImgId] = $prodImg; 
				// $mediaGalleryData['images'][$prodImgId]['label'] = 'Orange Belgique '.$labelTitle.' - '.$color; //set Image title
			// }
		}
		if(isset($mediaGalleryData) && is_array($mediaGalleryData)) {
			//$product->setData('media_gallery', $mediaGalleryData); //Update Gallery image title
		}
		if($productName !='') {
			if($metaProductTitle !='') {
				$productMetaTitle = $productName.' - '.$metaProductTitle.' | Orange'; //Format meta title for other products			
				if($metatitle == '') {
					$product->setMetaTitle($productMetaTitle);
				}
			}
			else {
				$productMetaTitle = $productName.' | Orange'; //Format meta title for other products
				if($metatitle == '') {
					$product->setMetaTitle($productMetaTitle);
				}
			}
		}
		if($store != "nl") {
			if($productType != "bundle" && $product->getMarketingDescription()) {
				/** Update meta description only if marketing description is updated **/
				$newdesc = substr(strip_tags($product->getMarketingDescription()), 0, 159);	
				if($metadesc == '') {
					$product->setMetaDescription($newdesc);
				}						
			}
		}
		return $this;
		
	} 
	function after($text, $inthat)
	{
		if (!is_bool(strpos($inthat, $text)))
		return substr($inthat, strpos($inthat,$text)+strlen($text));
	}
	public function getattributesetName($attributeId)
	{
	  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $attributeSet = $objectManager->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
      $attributeSetRepository = $attributeSet->get($attributeId);
      $attribute_set_name = $attributeSetRepository->getAttributeSetName();
      return $attribute_set_name;
	}
       
}

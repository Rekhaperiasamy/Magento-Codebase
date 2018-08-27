<?php
namespace Dilmah\Cartinfo\Observer;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Cartinfo implements ObserverInterface {

    protected $_responseFactory;
	protected $productRepository;
    protected $_url;
	protected $linkManager;
	
	
    public function __construct(
    \Magento\Framework\App\ResponseFactory $responseFactory, ProductRepositoryInterface $productRepository, \Magento\Framework\UrlInterface $url,
	\Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet, \Magento\Bundle\Api\ProductLinkManagementInterface $linkManager
    ) {
	    $this->_responseFactory = $responseFactory;
		$this->productRepository = $productRepository;
        $this->_url = $url;	
		$this->attributeSet = $attributeSet;
		$this->linkManager=$linkManager;
     }

    public function execute(Observer $observer) {
	   
		$cart = $observer->getData('cart');
        $items = $cart->getQuote()->getAllItems();
		
		foreach ($items as $item) {
		try{
		$bundleProduct=$this->productRepository->get($item->getSku());
		}catch(\Exception $e){
		//echo $e->getMessage()."--".$item->getSku(); exit;
			continue;
		}
		$attributeSetRepository = $this->attributeSet->get($bundleProduct->getAttributeSetId());
		$attributeName = $attributeSetRepository->getAttributeSetName();
	    $productType = $bundleProduct->getTypeId();
	    $default_packSize =  $bundleProduct->getPackSize();
			if(($productType == "bundle") && ($attributeName =="Pack product") && ($default_packSize > 0)){
	        $default_packSize =  $bundleProduct->getPackSize();
	        //echo "Pack size is". $default_packSize. "\n";		
			$price =  $item->getProduct()->getFinalPrice();
			//echo "Product Price is " . $price . "\n";
			$qty = $item->getQty();
			//echo "Product Qty". $qty . "\n";
			$customerPrice = $price * $qty ;
			//echo "Customer Price" . $customerPrice . "\n";
			
			$pros=$this->linkManager->getChildren($item->getSku());
			foreach($pros as $pro){
			$sku=$pro->getSku();
			$simpleProduct=$this->productRepository->get($sku);
		    $singlePack_price = $simpleProduct->getPrice();
			//echo "Single Pack Product Price is " . $singlePack_price. "\n";
			$packSize = $qty * $default_packSize. "\n";
			$orginalPrice = $packSize * $singlePack_price; 
			//echo "Original Product Price is" . $orginalPrice;
			
			if ($customerPrice != $orginalPrice){
			throw new \Magento\Framework\Exception\LocalizedException(__('Price is not match with original price.We cant add the product right now.'));
			}
			}
			}
      }
	}

}

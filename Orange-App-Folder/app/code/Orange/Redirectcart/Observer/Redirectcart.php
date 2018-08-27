<?php
namespace Orange\Redirectcart\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Redirectcart implements ObserverInterface {

    protected $_responseFactory;
    protected $_url;
	protected $_catalogHelper;
	
    public function __construct(
    \Magento\Framework\App\ResponseFactory $responseFactory, \Magento\Framework\UrlInterface $url, \Orange\Catalog\Helper\CatalogUrl $catalogHelper
    ) {
	    $this->_responseFactory = $responseFactory;
        $this->_url = $url;
		$this->_catalogHelper = $catalogHelper;
     }

    public function execute(Observer $observer) {
	
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$requestOb = $om->get('Magento\Framework\App\Request\Http'); 
		$frontName = $requestOb->getFrontname(); 
		$controlName = $requestOb->getControllername(); 
		$quote = $om->create('Magento\Checkout\Model\Cart')->getQuote();

		 $customerGroupId = $om->get('Magento\Checkout\Model\Session')->getCustomerSessId();
		$latestcustomerTypeId = '';
		if(isset($_COOKIE['SEGMENT'])) {
			$latestcustomerTypeId = $_COOKIE['SEGMENT'];
			if($customerGroupId != "SOHO" && $customerGroupId !="res") {
				$customerGroupId = $latestcustomerTypeId;
			}
			
			// Remove all items on context switch
			   if($quote && $quote->getId() && $customerGroupId != $latestcustomerTypeId) {
					$allItems = $quote->getAllVisibleItems();//returns all teh items in session
					$itemModel = $om->create('Magento\Quote\Model\Quote\Item');
					$itemId ="";
					$items = array();
					foreach ($allItems as $item) {
						$items[] = $item->getItemId();
						$itemId = $item->getItemId();//item id of particular item
						$quoteItem = $itemModel->load($itemId);//load particular item which you want to delete by his item id
					}
					
					$itemssId = implode(',',$items);
					$om->get('Magento\Checkout\Model\Session')->setDeletedItemId($itemssId);
				}
		}
		$om->get('Magento\Checkout\Model\Session')->setCustomerSessId($latestcustomerTypeId); 
		$params = $om->get('\Magento\Framework\App\Request\Http')->getParams();
		$deleteId = $om->get('Magento\Checkout\Model\Session')->getDeletedItemId();
		if ($frontName == 'checkout' && $controlName == 'cart' && !array_key_exists("sku",$params)) {
			$deleteId = $om->get('Magento\Checkout\Model\Session')->getDeletedItemId();
			$deleteIds = explode(',',$deleteId);
			if($quote && $quote->getId() ) {
				$allItems = $quote->getAllVisibleItems();
				foreach ($allItems as $item) {
					$itemId = $item->getItemId();
					if (in_array($itemId,$deleteIds)) {
						$quote->removeItem($itemId)->save();
					}
				}
			}
		}
        if ($quote && $quote->getItemsCount() && ($frontName == 'checkout' && $controlName == 'cart') || ($frontName == 'checkout' && $controlName == 'index')) {
			$quote->collectTotals()->save();
			$quoteItems = $quote->getAllVisibleItems();
            if (count($quoteItems)) {
				/** total Qty based on Attribute set ID */
				$attr = array();
				foreach($quoteItems as $item){
					$attributeSetid = $item->getProduct()->getAttributeSetId();
					$productType = $item->getProduct()->getTypeId();
					if($productType=="bundle"){
						foreach ($item->getChildren() as $childrenItem){
							$childAttributesetId = $childrenItem->getProduct()->getAttributeSetId();
							if (($childrenItem->getProductType() == 'virtual')&&($childAttributesetId==11)){
								$attributeSetid = 17; 
								if (array_key_exists($attributeSetid, $attr)) {
									$attr[$attributeSetid] = $attr[$attributeSetid] + $item->getQty(); 
								} else {
									$attr[$attributeSetid] = $item->getQty();
								}
							} elseif (($childrenItem->getProductType() == 'virtual')&&($childAttributesetId==15)){
								$attributeSetid = 16;
								if (array_key_exists($attributeSetid, $attr)){
									$attr[$attributeSetid] = $attr[$attributeSetid] + $item->getQty();
								} else {
									$attr[$attributeSetid] = $item->getQty();				  
								}				  
							}
						}
					} else {
						if (array_key_exists($attributeSetid, $attr)) {
							$attr[$attributeSetid] = $attr[$attributeSetid] + $item->getQty();
						} else {
							$attr[$attributeSetid] = $item->getQty();
						}
					}
				}
				
				foreach ($quoteItems as $item) {
					$attributeSetid = $item->getProduct()->getAttributeSetId();
					$product = $item->getProduct();
					$typeId = $product->getTypeId();
					$childrenItems = $item->getChildren();
					if($typeId=="bundle"){
						foreach ($childrenItems as $childrenItem){
						$childAttributesetId = $childrenItem->getProduct()->getAttributeSetId();
							if (($childrenItem->getProductType() == 'virtual')&&($childAttributesetId==11)){
								$attributeSetid = 17;
								$collections = $om->create('Orange\Limitqty\Model\Limitqty')->getCollection()->addFieldToFilter('customer_group',17)->getFirstItem();
								$qty = $collections->getLimitquantity();
								$totalQty = $attr[$attributeSetid];
								if (($qty != "") && ($totalQty > $qty) && ($frontName == "checkout") && ($controlName == "index")) {
									$RedirectUrl = $this->_url->getUrl('checkout/cart/', ['_secure' => true]); //Format Category URL as per SOHO
									$RedirectUrl = $this->_catalogHelper->getFormattedUrl($RedirectUrl);
									$this->_responseFactory->create()->setRedirect($RedirectUrl)->sendResponse();
									die();
								}
							} elseif (($childrenItem->getProductType() == 'virtual')&&($childAttributesetId==15)){
								$attributeSetid = 16;
								$collections = $om->create('Orange\Limitqty\Model\Limitqty')->getCollection()->addFieldToFilter('customer_group',16)->getFirstItem();
								$qty = $collections->getLimitquantity();
								$totalQty = $attr[$attributeSetid];
								if (($qty != "") && ($totalQty > $qty) && ($frontName == "checkout") && ($controlName == "index")) {
									$RedirectUrl = $this->_url->getUrl('checkout/cart/', ['_secure' => true]); //Format Category URL as per SOHO
									$RedirectUrl = $this->_catalogHelper->getFormattedUrl($RedirectUrl);
									$this->_responseFactory->create()->setRedirect($RedirectUrl)->sendResponse();
									die();
								}
						   }
						}
					} else {
						$limitData = $om->create('Orange\Limitqty\Model\Limitqty')->getCollection()->addFieldToFilter('customer_group', $product->getAttributeSetId())->getFirstItem();						
						$totalQty = $attr[$attributeSetid];
						$qty = $limitData->getLimitquantity();
						if (($qty != "") && ($totalQty > $qty) && ($frontName == "checkout") && ($controlName == "index")) {
							$RedirectUrl = $this->_url->getUrl('checkout/cart/', ['_secure' => true]); //Format Category URL as per SOHO
							$RedirectUrl = $this->_catalogHelper->getFormattedUrl($RedirectUrl);
							$this->_responseFactory->create()->setRedirect($RedirectUrl)->sendResponse();
							die();
						}
					}
				}
			}
		}
    }
	private function getCustomerTypeId()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerGroupId = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeId();		
		return $customerGroupId;		
	}
}
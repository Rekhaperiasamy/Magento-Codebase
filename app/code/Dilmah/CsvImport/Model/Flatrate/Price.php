<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dilmah\CsvImport\Model\Flatrate;

class Price extends \Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator{
    public function getShippingPricePerOrder(
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        $basePrice,
        $freeBoxes
    ) { 		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();			
		$storeManager  = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$storeID       = $storeManager->getStore()->getStoreId(); 		
		$cart = $objectManager->get('\Magento\Checkout\Model\Cart');  
		$subTotal = $cart->getQuote()->getSubtotal();
		$grandTotal = $cart->getQuote()->getGrandTotal();
	
		if ($grandTotal >= 40 && $storeID =='9'){
			$shippingCharges = 0;
			return $shippingCharges;
		}else if($grandTotal <= 40 && $storeID == '9'){
			return $basePrice;
		}else{
			return $basePrice;
		} 
				
    }
}

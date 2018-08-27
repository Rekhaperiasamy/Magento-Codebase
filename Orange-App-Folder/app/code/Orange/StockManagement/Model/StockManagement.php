<?php
namespace Orange\StockManagement\Model;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockManagementInterface;
use Magento\CatalogInventory\Model\ResourceModel\QtyCounterInterface;
use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Model\ResourceModel\Stock as ResourceStock;


class StockManagement extends \Magento\CatalogInventory\Model\StockManagement
{
	/**
     * Get back to stock (when order is canceled or whatever else)
     *
     * @param int $productId
     * @param float $qty
     * @param int $scopeId
     * @return bool
     */
    public function backItemQty($productId, $qty, $scopeId = null)
    {
        //if (!$scopeId) {
        $scopeId = $this->stockConfiguration->getDefaultScopeId();
        //}
        $stockItem = $this->stockRegistryProvider->getStockItem($productId, $scopeId);
        if ($stockItem->getItemId() && $this->stockConfiguration->isQty($this->getProductType($productId))) {
            if ($this->canSubtractQty($stockItem)) {
				
				$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$this->_objectManager->get('Psr\Log\LoggerInterface')->addDebug('-----Stock Management Update -----');
				$this->_objectManager->get('Psr\Log\LoggerInterface')->addDebug($productId.'-'.$stockItem->getQty().'-'.$qty);
				$product = $this->productRepository->getById($productId);
				$this->_objectManager->get('Psr\Log\LoggerInterface')->addDebug('orangemangestock:'.$product->getOrangeManageStock());	
				/* Update stock only if orange stock management set to Yes (1) and stock is greater that 0 **/
				if($stockItem->getQty() > 0 || ($stockItem->getQty() == 0 && $product->getOrangeManageStock() == 1)) {
					$stockItem->setQty($stockItem->getQty() + $qty);
					$this->_objectManager->get('Psr\Log\LoggerInterface')->addDebug('Stock Increased from '.$stockItem->getQty().' to '.$qty);
				}
				$this->_objectManager->get('Psr\Log\LoggerInterface')->addDebug('-----Stock Management Update -----');
            }
            if ($this->stockConfiguration->getCanBackInStock($stockItem->getStoreId()) && $stockItem->getQty()
                > $stockItem->getMinQty()
            ) {
                $stockItem->setIsInStock(true);
                $stockItem->setStockStatusChangedAutomaticallyFlag(true);
            }
            $stockItem->save();
        }
        return true;
    }
}
<?php
namespace Orange\StockManagement\Block\Adminhtml;

use Magento\Backend\Block\Template;

class Stockexport extends Template
{
    public function getProductData()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        /** Apply filters here */
        $productCollection->addAttributeToFilter('type_id', array('neq' => 'virtual'));
        $productCollection->addAttributeToFilter('type_id', array('neq' => 'bundle'));
        $productCollection->addAttributeToSelect('price', true); // added true as  2nd parameter
        $productCollection->addAttributeToSelect('status', true); // added true as  2nd parameter
        $productCollection->addAttributeToSort('name', 'ASC');
        $productCollection->getSelect()->join(
            ['cataloginventory_item' => $productCollection->getTable('cataloginventory_stock_item')],
            'e.entity_id = cataloginventory_item.product_id',
            ['qty', 'is_in_stock']
        )->join(
            ['cpe' => $productCollection->getTable('catalog_product_entity')],
            'e.entity_id = cpe.entity_id',
            ['attribute_set_id']
        )->join(
            ['eas' => $productCollection->getTable('eav_attribute_set')],
            'cpe.attribute_set_id = eas.attribute_set_id',
            ['attribute_set_name']
        )->where(
            'cataloginventory_item.manage_stock != ?',
            0
        )->where(
            'eas.attribute_set_name != ?',
            'Simcard'
        )->where(
            'eas.attribute_set_name != ?',
            'Prepaid');

        $productCollection->load();
        return $productCollection;
    }
}
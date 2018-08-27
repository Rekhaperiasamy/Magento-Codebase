<?php

namespace Orange\Bundle\Model;

use Magento\Framework\App\ObjectManager;

class Bundle extends \Magento\Framework\Model\AbstractModel {

    protected function _construct() {
        parent::_construct();
        $this->_init('Orange\Bundle\Model\Resource\Bundle');
    }

    /**
     * Object Manager intialize
     * @return type
     */
    public function objectManager() {
        $objectManager = ObjectManager::getInstance();
        return $objectManager;
    }

    public function getBundleColor($bundleId) {
        $bundleSimpleProductFamily = $this->getBundleSimpleProductDetails($bundleId);
        $bundleVirtualProductId = $this->getVirtualProductDetails($bundleId);
        $virtualSku = $bundleVirtualProductId->getSKU();
        $simpleProductFamily = $bundleSimpleProductFamily->getHandsetFamily();
        $collect = $this->getMappingCollection($virtualSku, $simpleProductFamily);
        $returnValue = $this->collectionFetch($collect);

        return $returnValue;
    }

    public function collectionFetch($collection) {
        $value = array();
        foreach ($collection as $key => $collect) {
            $value[$key]['color'] = $collect->getColor();
            $value[$key]['virtual_product_sku'] = $collect->getVirtualProductSku();
            $value[$key]['handset_family'] = $collect->getHandsetFamily();
            $value[$key]['bundled_product_url'] = $collect->getBundledProductUrl();
        }
        return $value;
    }

    public function getMappingCollection($virtualSku, $simpleProductFamily) {
        $colorCollection = $this->objectManager()->create('Orange\Bundle\Model\Bundle')->getCollection();
        $collections = $colorCollection->addFilter('virtual_product_sku', array('in' => $virtualSku))
                ->addFilter('handset_family', array('in' => $simpleProductFamily))
                ->load();
        return $collections;
    }

    public function getBundleSimpleProductDetails($bundleId) {

        $product = $this->objectManager()->create('Magento\Catalog\Model\Product')->load($bundleId);

        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($bundleId, true);
        $productIds = array();

        foreach ($requiredChildrenIds as $ids) {
            $productIds[] = $ids;
        }
        $productCollection = $this->objectManager()->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $productIds))
                ->addAttributeToFilter('type_id', 'simple')
                ->load();

        return $collection->getFirstItem();
    }

    public function getVirtualProductDetails($bundleId) {

        $product = $this->objectManager()->create('Magento\Catalog\Model\Product')->load($bundleId);

        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($bundleId, true);
        $productIds = array();

        foreach ($requiredChildrenIds as $ids) {
            $productIds[] = $ids;
        }
        $productCollection = $this->objectManager()->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $productIds))
                ->addAttributeToFilter('type_id', 'virtual')
                ->load();

        return $collection->getFirstItem();
    }

}

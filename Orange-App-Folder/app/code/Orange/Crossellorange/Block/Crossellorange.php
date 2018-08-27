<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\Crossellorange\Block;
use  Magento\Catalog\Block\Product\ProductList\Crosssell;
use Magento\Framework\App\ObjectManager;

class Crossellorange extends \Magento\Framework\View\Element\Template
{

     public function crossCollection($sku){

         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
         $_productid = $objectManager->create('Magento\Catalog\Model\Product')->getIdBySku($sku);
         $_productCollect = $objectManager->create('Magento\Catalog\Model\Product')->load($_productid);
         return $_productCollect;

     }

    public function getChildrenProduct(\Magento\Catalog\Model\Product $product)
    {
        $pId = $product->getId();
        $objectManager = ObjectManager::getInstance();
        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($pId, true);
        $productIds = array();
        foreach ($requiredChildrenIds as $ids) {
            $productIds[] = $ids;
        }
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $productIds))
            ->addAttributeToFilter('type_id', 'simple')
            ->load();
        return $collection;
    }
    public function getVirtualChildrenProduct(\Magento\Catalog\Model\Product $product)
    {
        $pId = $product->getId();
        $objectManager = ObjectManager::getInstance();
        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($pId, true);
        $productIds = array();
        foreach ($requiredChildrenIds as $ids) {
            $productIds[] = $ids;
        }
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $productIds))
            ->addAttributeToFilter('type_id', 'virtual')
            ->load();
        return $collection;
    }
    public function getUpSellProducts(\Magento\Catalog\Model\Product $product)
    {
        $pId = $product->getId();
        $objectManager = ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($pId);
        $upSellProductIds = $product->getUpSellProductIds();     
        return $upSellProductIds;             
    }
    public function getBannerSmartphoneDevice(){
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        /** Apply filters here */
        $pCollection=$productCollection
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('is_banner_device', '1')
        ->setPageSize(2)
        ->load();
        
        return $pCollection;

    }
 
}

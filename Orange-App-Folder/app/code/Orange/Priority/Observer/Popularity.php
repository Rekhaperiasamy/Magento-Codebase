<?php

namespace Orange\Priority\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Popularity implements ObserverInterface {

    protected $_storeManager;

    public function __construct(
    \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $product = $observer->getEvent()->getProduct();
        $this->popularityProductSave($product);
    }

    public function popularityProductSave($product) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $id = $product->getId();
        if ($id) {
            $collection = $objectManager->get('Orange\Priority\Model\Popularity')->getCollection();
            $collection->addFieldToFilter('product_id', array('in' => $id))
                    ->addFieldToFilter('store_id', array('in' => $this->_storeManager->getStore()->getId()));
            $viewData = array();
            if ($product->getHandsetFamily() || $product->getAccessoryFamily()) {
                $viewData['product_id'] = $product->getId();
                if ($product->getHandsetFamily()) {
                    $viewData['family'] = $product->getHandsetFamily();
                } if ($product->getAccessoryFamily()) {
                    $viewData['family'] = $product->getAccessoryFamily();
                }
                $family_type = $product->getHandsetFamily() ? 1 : 0;
                $viewData['family_type'] = $family_type;
                $viewData['store_id'] = $this->_storeManager->getStore()->getId();
                $model = $objectManager->create('Orange\Priority\Model\Popularity');
                $model->setData($viewData);
                $model->save();
            }
        }
    }

}

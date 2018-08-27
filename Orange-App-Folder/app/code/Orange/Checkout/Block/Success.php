<?php

namespace Orange\Checkout\Block;

use Magento\Framework\View\Element\Template;

class Success extends Template {

    protected $coreRegistry;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Registry $coreRegistry, array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_isScopePrivate = true;
    }

    public function getOrderId() {
        return $this->_coreRegistry->registry('OrderId');
    }

    public function getOrder() {
        $incrementId = $this->getOrderId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('Magento\Sales\Model\Order')->load($incrementId, 'increment_id');
        return $order;
    }

    public function getCustomerGroup($customerGroupId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerGroup = $objectManager->create('Magento\Customer\Api\GroupRepositoryInterface')->getById($customerGroupId)->getCode();
        return $customerGroup;
    }

    public function getOnlyPrepaidInOrder() {
        $orderData = $this->getOrder()->getAllItems();
        $data = 0;
        $count = count($orderData);
        foreach ($orderData as $item) {
            if ($item->getProductType() == 'virtual' && 
                    $item->getParentItemId() == "" 
                    && $item->getProduct()->getAttributeSetId() == 10 && $count == 1) {
                $data = 1;
                continue;
            }
        }
        return $data;
    }

}

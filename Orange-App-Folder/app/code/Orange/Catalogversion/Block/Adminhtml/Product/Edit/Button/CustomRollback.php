<?php

namespace Orange\Catalogversion\Block\Adminhtml\Product\Edit\Button;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;

class CustomRollback extends Generic {

    protected $urlBuilder;

    public function __construct(
    \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    public function getButtonData() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->get('Magento\Framework\Registry')->registry('current_product'); //get current product
        $duplicateUrl = $this->urlBuilder->getUrl(
                'catalogversion/catalogversionproduct/rollback', [
            'id' => $product->getId(),
        ]);
        
		return [
            'label' => __('Rollback'),
            //'on_click' => "alert('it works')",
			'on_click' => 'setLocation("' . $duplicateUrl . '")',
            'sort_order' => 200,
            'class' => 'primary',
        ];
    }

}

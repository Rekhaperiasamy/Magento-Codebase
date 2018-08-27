<?php

/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dilmah\Extendgrid\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;


/**
 * Class DepartmentActions
 */
class Qtyordered extends Column {

    /**
     * @var UrlInterface
     */
    protected $_orderCollectionFactory;


    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(

    \Magento\Sales\Model\OrderFactory $orderFactory,
    \Magento\Framework\ObjectManagerInterface $objectManager,
	ContextInterface $context, UiComponentFactory $uiComponentFactory,  array $components = [], array $data = []
    ) {
       
        $this->_orderCollectionFactory = $orderFactory;   
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

	
	
	
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) {

         if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
			        $order = $this->_orderCollectionFactory->create()->load($item['entity_id']);
					$products = $order->getAllItems();
					
					$qty = 0;
                    foreach ($products as $product) {
					
					$qty = $qty + $product->getQtyOrdered();
					}
					$item[$this->getData('name')] = $qty;

            }
        }

        return $dataSource;
    }

}
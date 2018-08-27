<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Webform\Model;

use Magento\Framework\Exception\StockException;
//use Psr\Log\LoggerInterface;

/**
 * Stocktab stock model
 */
class Mnpform extends \Magento\Framework\Model\AbstractModel
{
protected $productFactory;
//protected $logger;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
	   // LoggerInterface $logger,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,		
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);		
    }

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('Orange\Webform\Model\ResourceModel\Mnpform');
    }
	//commented for Stock Management Update issue (incident: 39116440)
	/*public function updatestock()
	{
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			
			$WebformModel = $objectManager->create('Orange\Webform\Model\Stock')->getCollection();
		foreach($WebformModel as $stock)
		{
			 $sku=$stock->getData('product_sku');
			 $from=date("d-m-Y", strtotime($stock->getData('valid_from')));
			 //$to=date("d-m-Y", strtotime($stock->getData('valid_to')));
			 //$tomorrow = date("d-m-Y",strtotime($to . "+1 days"));			
			 $product = $objectManager->create('Magento\Catalog\Model\ProductFactory')->create();
			 $product->load($product->getIdBySku($sku));
						
			if($product)
			{
			$date = date("d-m-Y");
			if(strtotime($date) == strtotime($from))
			{				
			$product->setQuantityAndStockStatus(['qty' => $stock->getData('product_qty'), 'is_in_stock' => 1]);
			$product->save(); 
			//echo $stock->getData('Stock_id').':in stock';
			
			}			
			}
		}
	}*/
}
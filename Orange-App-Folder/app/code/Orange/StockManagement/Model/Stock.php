<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\StockManagement\Model;

use Magento\Framework\Exception\StockException;

/**
 * Stocktab stock model
 */
class Stock extends \Magento\Framework\Model\AbstractModel
{
    protected $productFactory;
    protected $_logger;
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(	   
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,		
        array $data = []
    )   
    {			
		parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_scopeConfig = $scopeConfig;				
	}

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('Orange\StockManagement\Model\ResourceModel\Stock');
    }
    public function updatestock()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $stockmanagementModel = $objectManager->create('Orange\StockManagement\Model\Stock')->getCollection();
        $log_mode = $this->_scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        //Added if condition for Stock Management Update issue (incident: 39116440)
		if(count($stockmanagementModel)) {
			foreach($stockmanagementModel as $stock)
			{
				try {
					 $sku=$stock->getData('product_sku');
					 $from=date("d-m-Y", strtotime($stock->getData('valid_from')));
					 //$to=date("d-m-Y", strtotime($stock->getData('valid_to')));
					 //$tomorrow = date("d-m-Y",strtotime($to . "+1 days"));            
					 $product = $objectManager->create('Magento\Catalog\Model\ProductFactory')->create();
					 $product->load($product->getIdBySku($sku));

					if($product && $product->getId())
					{
						$date = date("d-m-Y");
						if(strtotime($date) == strtotime($from)):
							$product->setQuantityAndStockStatus(['qty' => $stock->getData('product_qty'), 'is_in_stock' => 1]);//Product quantity is updated
							if($stock->getData('product_qty') == 0 || $stock->getData('product_qty') == '') {
								$product->setOrangeManageStock('0');
								/** Set OrangeManageStock to No (0) if inventory updated to 0 **/
								if(isset($log_mode) && $log_mode==1){
									$loggerModel = $objectManager->create('Orange\StockManagement\Model\Logger')->stockImport($sku.':Orange Manage stock set to yes');//Log file success sku msg
								}
							}
							$product->save();

							if(isset($log_mode) && $log_mode==1){
								$loggerModel = $objectManager->create('Orange\StockManagement\Model\Logger')->stockImport($sku.'SKU has been updated successfully');//Log file success sku msg
							}
							$productdelete = $objectManager->create('Orange\StockManagement\Model\Stock');
							$productdelete->load($stock->getId());
							$productdelete->delete();
						endif;

					} else {
						if(isset($log_mode) && $log_mode==1){
							$loggerModel = $objectManager->create('Orange\StockManagement\Model\Logger')->stockImport($sku.'SKU invalid,not been updated successfully');//Log file invalid sku msg
						}
					}
				}
				catch(Exception $e) {
					if(isset($log_mode) && $log_mode==1){
						$loggerModel = $objectManager->create('Orange\StockManagement\Model\Logger')->stockImport($sku.'SKU not been updated successfully'); 
					}
				}
			}
		}
    }
}
<?php
namespace Orange\Catalogversion\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\App\RequestInterface ;
class CatalogProductDeleteAfter implements ObserverInterface {
protected $_request;
	public function __construct(
	        \Magento\Framework\App\RequestInterface $request,
			\Magento\Framework\App\ResourceConnection $resourceConnection
	){
	    $this->_request = $request;
		$this->resourceConnection = $resourceConnection;
	}
	
public function execute(\Magento\Framework\Event\Observer $observer) {
    $productId = $observer->getProduct()->getId();
	if(isset($productId) && intval($productId)>0){
	   $this->deleteAllVersions($productId);	 
	}
	
}

function deleteAllVersions($productId){
   $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
   $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
   $connection = $resource->getConnection();
   $tableName=$resource->getTableName('catalogversion_catalogversion');
   $sql="delete from `".$tableName."` where productid='".$productId."'";
   $connection->query($sql);
}	
	
}
?>
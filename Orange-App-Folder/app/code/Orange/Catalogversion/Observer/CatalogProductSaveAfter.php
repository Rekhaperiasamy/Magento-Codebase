<?php
namespace Orange\Catalogversion\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
class CatalogProductSaveAfter  implements ObserverInterface {

protected $_request;
protected  $productRepository;
protected  $_responseFactory;
protected  $_messageManager;
protected  $_url;
	public function __construct(
	         \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
			 \Magento\Framework\App\ResponseFactory $responseFactory,
            \Magento\Framework\Message\ManagerInterface $messageManager,
			 \Magento\Framework\UrlInterface $url
			
	){
	      $this->productRepository = $productRepository;
          $this->_responseFactory  =   $responseFactory;
          $this->_messageManager=	$messageManager ;
		  $this->_url=$url;
	}
	
public function execute(\Magento\Framework\Event\Observer $observer) {
	     $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
         $backendSession = $objectManager->create('\Magento\Backend\Model\Session');
		 $magentoDateObject = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
         $magentoDate = $magentoDateObject->gmtDate();
		 $type= $backendSession->getPdSaveType();
		 if(isset($type) && $type=='draft'){
		    $productId = $observer->getProduct()->getId();
		    $attributeUpdate=$objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Action');
            if($attributeUpdate->updateAttributes([$productId], ['status' => 0],0)){
			  	$name=$observer->getProduct()->getName();
				$catalogdraft = $objectManager->create('Orange\Catalogversion\Model\Catalogdraft');
				$catalogdraft->setProductId($productId);
				$catalogdraft->setName($name);
				$catalogdraft->setStatus(0);
				$catalogdraft->setCreated($magentoDate);
				
				try{
				    $catalogdraft->save();
				    $id=$catalogdraft->getId();
					$RedirectUrl= $this->_url->getUrl('catalogversion/draft/edit/',array('id'=>$id));
					$link="Draft saved succesfully,please update schedule <a href='".$RedirectUrl."'>Here</a>";
					$this->_messageManager->addSuccess($link);
				}
				catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_messageManager->addError($e->getMessage());
                }
				
			    //$RedirectUrl= $this->_url->getUrl('catalogversion/draft/edit/',array('id'=>$id));
				//$this->_redirect->setRedirect($RedirectUrl);
                //$this->_responseFactory->create()->setRedirect($RedirectUrl);	
				//exit(0);
			}
			
		}
		 //die('catalog product after save');
}
	
	/*
	 Retrieve product previous revison number
	 * @param int $productId
	   @return int
   */
    /*function getRevisionNumber($productId){
		 $newrevison=1;
		 $resources = \Magento\Framework\App\ObjectManager::getInstance()
			->get('Magento\Framework\App\ResourceConnection');
		 $tableName=$resources->getTableName('catalogversion_catalogversion');	
		 $connection= $resources->getConnection();
		 $select="SELECT revision_number FROM `".$tableName."` WHERE productid='".$productId."' ORDER BY revision_number DESC LIMIT 1";
		 $result = $connection->fetchAll($select); 
		 if($result){
			$revison=$result[0]['revision_number']+$newrevison;
		 }else{
			$revison=$newrevison;
		 }
		 return $revison; 
	}*/
}

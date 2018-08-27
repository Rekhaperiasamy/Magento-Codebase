<?php
namespace Orange\Catalogversion\Cron;
use \Psr\Log\LoggerInterface;

class Scheduledraft {
    protected $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

/**
   * Write to system.log
   *
   * @return void
   */

    public function execute() {
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$attributeUpdate=$objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Action');
		//get today date
		$todayDate= date('Y-m-d');
		$timezoneInterface = $objectManager->create('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $todayDate = $timezoneInterface->date($todayDate)->format('Y-m-d H:i:s');
		//collection filter
		$model = $objectManager->create('\Orange\Catalogversion\Model\ResourceModel\Catalogdraft\Collection');
		$productRepository=$objectManager->create('Magento\Catalog\Model\Product');
		$model->addFieldToFilter('status',array('eq' => 0));
		$model->addFieldToFilter('start_time',array('eq' => $todayDate));
		$collection=$model->load();
		$count=$collection->getSize();
		if(isset($count) && $count>0){
		   foreach($collection as $c){
		      $id=$c->getId();
			  $prodcutId=$c->getProductId();
			  $product = $productRepository->load($prodcutId);
			  if($product->getId()){
				  if($attributeUpdate->updateAttributes([$prodcutId], ['status' => 1],0)){
					 $catalogDraftModel = $objectManager->create('\Orange\Catalogversion\Model\Catalogdraft');
					 $catalogDraftModel->setStatus(1)->setId($id)->save();	    
				  }
			  }
			  $product->reset();
		   }
		}
		
    }

}
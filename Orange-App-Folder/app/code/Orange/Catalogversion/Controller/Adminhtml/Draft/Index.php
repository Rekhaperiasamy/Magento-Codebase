<?php
namespace Orange\Catalogversion\Controller\Adminhtml\Draft;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $resultPage;
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    public function execute()
    {
	    /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$magentoDateObject = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
        $magentoDate = $magentoDateObject->gmtDate();
		$todayDate= date('Y-m-d');
		$timezoneInterface = $objectManager->create('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $todayDate = $timezoneInterface->date($todayDate)->format('Y-m-d H:i:s');
		$productRepository=$objectManager->create('Magento\Catalog\Model\Product');
		#########################################################
		//$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$model = $objectManager->create('\Orange\Catalogversion\Model\ResourceModel\Catalogdraft\Collection');
		$attributeUpdate=$objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Action');
		$date=$magentoDateObject = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
        $magentoDate = $magentoDateObject->gmtDate();
		$model->addFieldToFilter('status',array('eq' => 0));
		$model->addFieldToFilter('start_time',array('eq' =>$todayDate));
		$collection=$model->load();
		echo $count=$collection->getSize();
		$model = $objectManager->create('\Orange\Catalogversion\Model\ResourceModel\Catalogdraft\Collection');
		$attributeUpdate=$objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Action');
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
		}*/
		###################################################
		/*for($i=4;$i<14;$i++){
			$catalogVersion = $objectManager->create('Orange\Catalogversion\Model\Catalogdraft');
			$catalogVersion->setProductId(894);
			$catalogVersion->setName('this is product'.$i);
			//$catalogVersion->setStartTime($magentoDate);
			$catalogVersion->setCreated($magentoDate);
			$catalogVersion->setStaus(0);
			$catalogVersion->save();
		}*/
		$this->resultPage = $this->resultPageFactory->create();  
		$this->resultPage->setActiveMenu('Orange_Catalogversionproduct::draft');
		$this->resultPage ->getConfig()->getTitle()->set((__('Manage New Products drafts')));
		return $this->resultPage;
    }
}

<?php
namespace Orange\Catalogversion\Controller\Adminhtml\Price;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
	public function execute()
    {	
	    
        $data = $this->getRequest()->getParams();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$model = $objectManager->create('\Orange\Catalogversion\Model\Pricerevision');
		$productRepository=$objectManager->create('Magento\Catalog\Model\Product');
		$priceVersions=$model->load($data['id']);
		//print_r($priceVersions->getData());
		if(isset($priceVersions['product_id']) && intval($priceVersions['product_id'])>0){
		  try{
		     $productRepository->load($priceVersions['product_id']);  
		     $productRepository->setPrice($priceVersions['price'])->save();
			 $this->messageManager->addSuccess(__('Price rollbacked successfully.'));
		   }
		   catch(\Magento\Framework\Exception\LocalizedException $e) {
		     $this->messageManager->addError($e->getMessage());
		   }
		   $this->_redirect('catalog/product/index');
		}else{
		    $this->messageManager->addError(__('Price revison not found.'));
			$this->_redirect('*/*/');
		}
		
		
    }
}

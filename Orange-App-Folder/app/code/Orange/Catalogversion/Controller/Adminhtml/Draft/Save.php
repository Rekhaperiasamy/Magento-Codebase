<?php
namespace Orange\Catalogversion\Controller\Adminhtml\Draft;
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
		if ($data) {
		  try{
		     $model = $objectManager->create('\Orange\Catalogversion\Model\Catalogdraft');
			// echo $data['id'];
			 if(isset($data['start_time'])){
			    $todayDate=strtotime(date('Y-m-d'));
				$data['start_time']=strtotime($data['start_time']);
				if($todayDate>$data['start_time']){
				   $this->messageManager->addError('Please add future date for drafts');
				}else{
				   $timezoneInterface = $objectManager->create('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
                   $dateTimeZone = $timezoneInterface->date($data['start_time'])->format('Y-m-d H:i:s');
			       $model->setData('start_time',$dateTimeZone); 
				}
			    
			    
			 }
			 
			 $model->setData('id', $data['id']);
			 $model->save();
		  }
		  catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                //$this->getDataPersistor()->set('catalog_product', $data);
                //$redirectBack = $productId ? true : 'new';
		 }
        }
		$this->_redirect('*/*/');
    }
}

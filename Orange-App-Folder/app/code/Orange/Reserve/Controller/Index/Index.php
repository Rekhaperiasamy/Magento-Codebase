<?php

namespace Orange\Reserve\Controller\Index;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action {

    public function execute() {

        if ($this->getRequest()->isAjax()) {
            if ($this->getRequest()->getParam('zip_code')) {
                $zipCode = $this->getRequest()->getParam('zip_code');
                $sku = $this->getRequest()->getParam('sku');
                $name = $this->getRequest()->getParam('name');
				$click = $this->getRequest()->getParam('clickndres');
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $model = $objectManager->create('Orange\Reserve\Model\Zipcode')->zipcodeValidation($zipCode, $sku, $name,$click);				
            } else {
                $model = 'Zip Code is Missing';
            }
			$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
			$resultJson->setData($model);
			return $resultJson;
        } else {       
            $model = __('This is Not An Ajax Call');
			$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
			$resultJson->setData($model);
			return $resultJson;
            header("Location: http://".$_SERVER['SERVER_NAME']);
            die();      
        }
    }

}

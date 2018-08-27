<?php

namespace Orange\PromoDescription\Controller\Adminhtml\PromotionDescription;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute() {

        $data = $this->getRequest()->getParams();
        if ($data) {
            $model = $this->_objectManager->create('Orange\PromoDescription\Model\PromotionDescription');

            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);            
            //print_r($data); exit;
            $sku = $data['sku'];
            $family = $data['family'];
            $store_id = $data['store_id'];
            $customer_group = $data['customer_group'];

            $tableCollection = $model->getCollection()
                    ->addFieldToFilter('sku', $sku)
                    ->addFieldToFilter('store_id', $store_id)
                    ->addFieldToFilter('family', $family)
                    ->addFieldToFilter('customer_group', $customer_group);
					foreach($tableCollection as $col){
						$tableSku = $col['sku'];
						$tableFamily = $col['family'];
						$tableStoreId = $col['store_id'];
						$tableCustomerGroup = $col['customer_group'];
					}													
					if(isset($tableSku) && !empty($tableSku) && isset($tableFamily) && !empty($tableFamily) && isset($tableStoreId) && !empty($tableStoreId) && isset($tableCustomerGroup) && !empty($tableCustomerGroup)  ){
						$this->messageManager->addError(__('The Promo Description has already configured with Same Sku, Family, Customer Group and Language '));
						} else {						
						try {
							$model->save();
							$this->messageManager->addSuccess(__('The Promo  Has been Saved.'));
							$this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
							if ($this->getRequest()->getParam('back')) {
								$this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));
								return;
							}
							$this->_redirect('*/*/');
							return;
							} catch (\Magento\Framework\Model\Exception $e) {
								$this->messageManager->addError($e->getMessage());
							} catch (\RuntimeException $e) {
								$this->messageManager->addError($e->getMessage());
							} catch (\Exception $e) {
								$this->messageManager->addException($e, __('Something went wrong while saving the banner.'));
							}
						}
						  					
            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('banner_id' => $this->getRequest()->getParam('banner_id')));
            return;
        }
        $this->_redirect('*/*/');
    }

}

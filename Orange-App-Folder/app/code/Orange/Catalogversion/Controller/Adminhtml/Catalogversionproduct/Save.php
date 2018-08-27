<?php
namespace Orange\Catalogversion\Controller\Adminhtml\Catalogversionproduct;
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
		    $model = $this->_objectManager->create('Orange\Catalogversion\Model\Catalogversion');
			$id = $this->getRequest()->getParam('id');
			if ($id) {
                $model->load($id);
				$productId=$model->getProductid();
				$storeId=$model->getStoreId();
				$name = $model->getName();
				$sku = $model->getSku();
				$price = $model->getPrice();
				$status=$model->getStatus();
				$quanty=$model->getQuantity();
				$stock=$model->getStock();
				$visibility=$model->getVisibility();
				$description=$model->getDescription();
				$shortdescription=$model->getShortdescription();
				$urlkey=$model->getUrlkey();
				$metatitle=$model->getMetatitle();
				$metakeyword=$model->getMetakeyword();
				$metadescription=$model->getMetadescription();
				$categories=$model->getCategories();
 				$rollBackCategoriesNames[]=explode( ',', $categories );				
				$customAttributes = $model->getCustomAttribueInfo();
				$custom_attribues_unserialize=unserialize($customAttributes);				
				$_product = $objectManager->get('Magento\Catalog\Model\ProductFactory')->create()->load($productId);
				
				try {
					$_product->setSku($sku);
					$_product->setName($name);				
					$_product->setPrice($price);
					$_product->setStatus($status);
					$_product->setQty($quanty);
					if($stock=='Out of Stock')
				    {
				      $stock=0;
				    }
				    else{
				      $stock=1;
				    } 
					$_product->isInStock($stock);
					$_product->setVisibility($visibility);
					$_product->setDescription($description);
					$_product->setShortDescription($shortdescription);
					$_product->setUrlkey($urlkey);
					$_product->setMetaTitle($metatitle);
					$_product->setMetaKeyword($metakeyword);
					$_product->setMetaDescription($metadescription);
					$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
					$connection = $resource->getConnection();
					$sql = "Select * FROM eav_attribute where  entity_type_id=4 ";
					$result = $connection->fetchAll($sql);
					foreach ($result as $attribute) {
					if ($attribute['is_user_defined']==1)
						{	
							$attributeCode = $attribute['attribute_code'];
							$attributeLabel = $attribute['frontend_label'];
							if(isset($custom_attribues_unserialize[$attributeLabel]))
						    {
						    if($attribute['frontend_input']=='select')
						    {
								$attr = $_product->getResource()->getAttribute($attributeCode);
								$label =$custom_attribues_unserialize[$attributeLabel];
								$attributeValue = $attr->getSource()->getOptionId($label);
							}
						  elseif($attribute['frontend_input']=='boolean')
						   {
						     $attributeValue = $custom_attribues_unserialize[$attributeLabel];
						        if($custom_attribues_unserialize[$attributeLabel])
							    {
							       $attributeValue = '1';
								}
							    else {
								   $attributeValue = '0';
								}
						    }
						    else
						    {
						        $attributeValue = $custom_attribues_unserialize[$attributeLabel];
						    }
						        $dynamicAttribute[$attributeCode]=$attributeValue;						
						    }
						
						}
			        }
			
			    foreach($dynamicAttribute as  $Key=>$val){
			            $_product->setData($Key,$val);
		        } 
				$_product->setStoreId($storeId)->save();
			    $this->messageManager->addSuccess(__('The Roll Back Has been completed.'));
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
			    $this->messageManager->addException($e, __('Something went wrong while Rollback.'));
            }
            return;
			}
        }
		$this->_redirect('*/*/');
    }
}

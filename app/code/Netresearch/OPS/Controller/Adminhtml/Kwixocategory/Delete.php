<?php

namespace Netresearch\OPS\Controller\Adminhtml\Kwixocategory;

class Delete extends \Netresearch\OPS\Controller\Adminhtml\Kwixocategory
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('id', 0);
        $storeId = $this->getRequest()->getParam('store', 0);
        try {
            $this->oPSKwixoCategoryMappingFactory->create()
                ->loadByCategoryId($id)
                ->delete();
            $this->messageManager->addSuccess(__('Data succesfully deleted.'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $this->_redirect('*/*/index/', ["id" => $id, "store" => $storeId]);
    }
}

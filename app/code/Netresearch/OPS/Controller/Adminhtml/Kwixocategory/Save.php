<?php

namespace Netresearch\OPS\Controller\Adminhtml\Kwixocategory;

class Save extends \Netresearch\OPS\Controller\Adminhtml\Kwixocategory
{
    public function execute()
    {
        $post = $this->getRequest()->getPost();
        try {
            $this->oPSKwixoHelper->saveKwixoconfigurationMapping($post->toArray());
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $this->_redirect(
            '*/*/index/',
            ['_current' => true, "id" => $post['category_id'], "store" => $post['storeId']]
        );
    }
}

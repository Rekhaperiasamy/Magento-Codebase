<?php

namespace Netresearch\OPS\Controller\Adminhtml\Kwixocategory;

class Tree extends \Netresearch\OPS\Controller\Adminhtml\Kwixocategory
{
    public function execute()
    {
        $categoryId = (int)$this->getRequest()->getParam('id');
        $storeId = $this->getRequest()->getParam('store', 0);
        if ($storeId) {
            if (!$categoryId) {
                $store = $this->storeManager->getStore($storeId);
                $rootId = $store->getRootCategoryId();
                $this->getRequest()->setParam('id', $rootId);
            }
        }

        $category = $this->_initCategory(true);

        if (!$category) {
            return $this->_redirect('*/*/', ['_current' => true, 'id' => null]);
        }
        $page = $this->pageFactory->create();
        $block = $page->getLayout()->createBlock('ops/adminhtml_kwixocategory_categoryTree');
        $root = $block->getRoot();
        return $this->getResponse()->setBody(
            $this->jsonEncoder->encode(
                [
                    'data'       => $block->getTree(),
                    'parameters' => [
                        'text'         => $block->buildNodeName($root),
                        'draggable'    => false,
                        'allowDrop'    => false,
                        'id'           => (int)$root->getId(),
                        'expanded'     => (int)$block->getIsWasExpanded(),
                        'store_id'     => (int)$block->getStore()->getId(),
                        'category_id'  => (int)$category->getId(),
                        'root_visible' => (int)$root->getIsVisible()
                    ]]
            )
        );
    }
}

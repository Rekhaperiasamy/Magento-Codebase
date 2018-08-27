<?php

namespace Netresearch\OPS\Controller\Adminhtml\Kwixocategory;

class Edit extends \Netresearch\OPS\Controller\Adminhtml\Kwixocategory
{
    public function execute()
    {
        $params = ['_current' => true];
        $redirect = false;

        $storeId = (int)$this->getRequest()->getParam('store');
        $parentId = (int)$this->getRequest()->getParam('parent');
        $prevStoreId = $this->backendAuthSession->getLastViewedStore(
            true
        );

        if ($prevStoreId != null && !$this->getRequest()->getQuery('isAjax')) {
            $params['store'] = $prevStoreId;
            $redirect = true;
        }

        $prevCategoryId = $this->backendAuthSession
            ->getLastEditedCategory(true);
        if ($prevCategoryId && !$this->getRequest()->getQuery('isAjax')) {
            $this->getRequest()->setParam('id', $prevCategoryId);
        }
        if ($redirect) {
            return $this->_redirect('*/*/edit', $params);
        }

        $categoryId = (int)$this->getRequest()->getParam('id');
        if ($storeId && !$categoryId && !$parentId) {
            $store = $this->storeManager->getStore($storeId);
            $prevCategoryId = (int)$store->getRootCategoryId();
            $this->getRequest()->setParam('id', $prevCategoryId);
        }

        if (!($category = $this->_initCategory(true))) {
            return;
        }

        $data = $this->_session->getCategoryData(true);
        if (isset($data['general'])) {
            $category->addData($data['general']);
        }

        $page = $this->pageFactory->create();

        if ($this->getRequest()->getQuery('isAjax')) {
            $this->backendAuthSession->setLastViewedStore(
                $this->getRequest()->getParam('store')
            );
            $this->backendAuthSession->setLastEditedCategory(
                $category->getId()
            );
            return $this->getResponse()->setBody(
                $page->getLayout()->getMessagesBlock()->getGroupedHtml()
                . $page->getLayout()->createBlock(
                    'Netresearch\OPS\Block\Adminhtml\Kwixocategory\Edit'
                )->setController('kwixocategory')->toHtml()
            );
        }

        return $this->_redirect('*/*/index', $params);
    }
}

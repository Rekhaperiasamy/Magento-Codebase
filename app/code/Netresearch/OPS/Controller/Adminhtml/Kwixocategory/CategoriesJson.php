<?php

namespace Netresearch\OPS\Controller\Adminhtml\Kwixocategory;

class CategoriesJson extends \Netresearch\OPS\Controller\Adminhtml\Kwixocategory
{
    public function execute()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            $this->backendAuthSession->setIsTreeWasExpanded(true);
        } else {
            $this->backendAuthSession->setIsTreeWasExpanded(false);
        }
        if (($categoryId = (int)$this->getRequest()->getPost('id'))) {
            $this->getRequest()->setParam('id', $categoryId);

            if (!$category = $this->_initCategory()) {
                return;
            }
            $page = $this->pageFactory->create();
            $this->getResponse()->setBody(
                $page->getLayout()->createBlock(
                    'Netresearch\OPS\Block\Adminhtml\Kwixocategory\CategoryTree'
                )->getTreeJson($category)
            );
        }
    }
}

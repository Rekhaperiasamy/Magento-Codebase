<?php

namespace Netresearch\OPS\Controller\Adminhtml\Kwixocategory;

class Index extends \Netresearch\OPS\Controller\Adminhtml\Kwixocategory
{
    public function execute()
    {
        $page = $this->pageFactory->create();
        $selectedCategory = $this->backendAuthSession->getLastEditedCategory(true);
        if ($selectedCategory) {
            $this->getRequest()->setParam('id', $selectedCategory);
        }
        $selectedCategory = (int)$this->getRequest()->getParam('id', 0);
        $this->_initCategory(true);

        if ($selectedCategory > 0) {
            $page->getLayout()->getBlock('tree')->setData(
                'selectedCategory',
                $selectedCategory
            );
        }

        return $page;
    }
}

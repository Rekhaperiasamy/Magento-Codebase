<?php

namespace Netresearch\OPS\Controller\Adminhtml\Kwixoshipping;

class Index extends \Netresearch\OPS\Controller\Adminhtml\Kwixoshipping
{
    /**
     * displays the form
     */
    public function execute()
    {
        $page = $this->pageFactory->create();
        $storeId = $this->getRequest()->getParam('store', 0);
        $page->getLayout()->getBLock('kwixoshipping')->setData('store', $storeId);
        $page->getLayout()->getBLock('kwixoshipping')
            ->setData('postData', $this->backendSessionFactory->create()->getData('errorneousData', true));

        $page->getConfig()->getTitle()->set(__('Shipping configuration'));

        return $page;
    }
}

<?php

namespace Netresearch\OPS\Controller\Customer;

class Aliases extends \Netresearch\OPS\Controller\Customer
{
    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $block = $resultPage->getLayout()->getBlock('ops_customer_aliases');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $resultPage->getConfig()->getTitle()->set(__('My payment information'));
        return $resultPage;
    }
}

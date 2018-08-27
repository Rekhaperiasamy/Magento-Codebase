<?php

namespace Netresearch\OPS\Controller\Customer;

class DeleteAlias extends \Netresearch\OPS\Controller\Customer
{
    public function execute()
    {
        $aliasId = $this->_request->getParam('id');
        $alias = $this->oPSAliasFactory->create()->load($aliasId);
        $customerId = $this->customerSession->getCustomer()->getId();
        if ($alias->getId() && $alias->getCustomerId() == $customerId) {
            $alias->delete();
            $this->messageManager->addSuccess(__('Removed payment information %1.', $alias->getPseudoAccountOrCcNo()));
            return $this->getResponse()->setRedirect($this->_redirect->getRefererUrl());
        }
        $this->messageManager->addError(
            __('Could not remove payment information %1.', $alias->getPseudoAccountOrCcNo())
        );
        return $this->getResponse()->setRedirect($this->_redirect->getRefererUrl());
    }
}

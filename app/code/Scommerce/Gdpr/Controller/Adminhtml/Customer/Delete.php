<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Controller\Adminhtml\Customer;

/**
 * Delete/anonymize customer action
 *
 * Class Delete
 * @package Scommerce\Gdpr\Controller\Adminhtml\Customer
 */
class Delete extends AbstractAction
{
    /**
     * @inheritdoc
     */
    protected function run(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $this->account->anonymize($customer);
        $this->messageManager->addSuccessMessage(
            __('The account has been successfully deleted, and all orders have been anonymised.')
        );
        return $this->redirect();
    }
}

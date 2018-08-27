<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Controller\Adminhtml\Customer;

/**
 * Send anonimization link to customer email
 *
 * Class Email
 * @package Scommerce\Gdpr\Controller\Adminhtml\Customer
 */
class Email extends AbstractAction
{
    /**
     * @inheritdoc
     */
    protected function run(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $this->helper->sendConfirmationEmail($customer);
        $this->messageManager->addSuccessMessage(
            __('Deletion and Anonymisation link has been successfully sent to the customer.')
        );
        return $this->redirectToEdit($customer);
    }
}

<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Controller\Adminhtml\Customer;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Export customer action
 *
 * Class Export
 * @package Scommerce\Gdpr\Controller\Adminhtml\Customer
 */
class Export extends AbstractAction
{
    /**
     * @inheritdoc
     */
    protected function run(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $file = $this->getFilename($customer);
        $content = $this->account->export($customer);
        return $this->fileFactory->create($file, $content, DirectoryList::VAR_DIR);
    }

    /**
     * Helper method for build filename of export customer
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return string
     */
    private function getFilename(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        return sprintf('customer_%s_%s.csv', $customer->getId(), date('Y-m-d_H:i:s'));
    }
}

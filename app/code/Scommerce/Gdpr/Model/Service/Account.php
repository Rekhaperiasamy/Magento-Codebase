<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Service;

/**
 * Account service for anonymize/export customer data
 *
 * Class Account
 * @package Scommerce\Gdpr\Model\Service
 */
class Account
{
    /** @var Anonymize */
    private $anonymize;

    /** @var Export */
    private $export;

    /**
     * @param Anonymize $anonymize
     * @param Export $export
     */
    public function __construct(
        Anonymize $anonymize,
        Export $export
    ) {
        $this->anonymize = $anonymize;
        $this->export = $export;
    }

    /**
     * Anonymize customer data
     *
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @return bool
     * @throws \Exception
     */
    public function anonymize(\Magento\Customer\Model\Data\Customer $customer)
    {
        return $this->anonymize->execute($customer);
    }

    /**
     * Export customer details
     *
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @return string
     * @throws \Exception
     */
    public function export(\Magento\Customer\Model\Data\Customer $customer)
    {
        return $this->export->execute($customer);
    }
}

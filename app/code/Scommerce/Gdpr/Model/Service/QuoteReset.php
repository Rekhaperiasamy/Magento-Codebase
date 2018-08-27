<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Service;

/**
 * Set unprocessed quote data to null
 * Quote data means `quote` and `quote_address`
 *
 * Class QuoteReset
 * @package Scommerce\Gdpr\Model\Service
 */
class QuoteReset
{
    /* @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resource	 
     * @param \Scommerce\Gdpr\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Scommerce\Gdpr\Helper\Data $helper
    ) {
        $this->resource = $resource;
        $this->helper = $helper;
    }

    /**
     * Set unprocessed quote data to null
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
		if (! $this->helper->isEnabled()) {
            return;
        }
		
		$quoteExpiryDays = $this->helper->getQuoteExpire();
		
        if (!$quoteExpiryDays) {
			return;
		}

		if ((int)$quoteExpiryDays>0){
			$connection = $this->resource->getConnection();
			$connection->update(
				$this->resource->getTableName('quote'),
				[
					'scommerce_gdpr_processed_value' => 1,
					'customer_email' => null,
					'customer_prefix' => null,
					'customer_firstname' => null,
					'customer_middlename' => null,
					'customer_lastname' => null,
					'customer_suffix' => null,
					'customer_dob' => null,
					'customer_note' => null,
					'remote_ip' => null
				],
				'is_active = 1 AND scommerce_gdpr_processed_value IS NULL AND (TO_DAYS(NOW()) - TO_DAYS(updated_at)) > ' . $quoteExpiryDays
			);
			$connection->update(
				$this->resource->getTableName('quote_address'),
				[
					'scommerce_gdpr_processed_value' => 1,
					'email' => null,
					'prefix' => null,
					'firstname' => null,
					'middlename' => null,
					'lastname' => null,
					'suffix' => null,
					'company' => null,
					'street' => null,
					'city' => null,
					'region' => null,
					'region_id' => null,
					'postcode' => null,
					'telephone' => null,
					'fax' => null,
					'vat_id' => null
				],
				'scommerce_gdpr_processed_value IS NULL and (TO_DAYS(NOW()) - TO_DAYS(updated_at)) > ' . $quoteExpiryDays
			);
		}
    }
}

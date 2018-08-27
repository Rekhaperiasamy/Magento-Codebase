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
class AnonymiseOrders
{
    /* @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;
	
	/** @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory */
	private $order;
	
	/** @var \Scommerce\Gdpr\Model\Service\Anonymize */
	private $saleAnonymise;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
	 * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $order
	 * @param \Scommerce\Gdpr\Model\Service\Anonymize\Sale $saleAnonymise
     * @param \Scommerce\Gdpr\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $order,
		\Scommerce\Gdpr\Model\Service\Anonymize\Sale $saleAnonymise,
        \Scommerce\Gdpr\Helper\Data $helper
    ) {
        $this->resource = $resource;
        $this->helper = $helper;
		$this->order=$order;
		$this->saleAnonymise=$saleAnonymise;
    }

    /**
     * anonymise order data automatically as part of the cron job
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
		if (! $this->helper->isEnabled()) {
            return;
        }
		
		$orderExpiryDays = $this->helper->getOrderExpire();
		$chunkOrders = $this->helper->getOrderChunk();
		
        if (!$orderExpiryDays) {
			return;
		}
	
		if ((int)$orderExpiryDays>0){
			
			$this->helper->logData($orderExpiryDays);
			
			$toDate = date('Y-m-d H:i:s', strtotime('-'.$orderExpiryDays.' days'));
		
			$orders = $this->order->create();
			$orders->addFieldToSelect(array('increment_id', 'scommerce_gdpr_processed_value'))
				->addAttributeToFilter('created_at', array('to'=>$toDate))
				->addAttributeToFilter('scommerce_gdpr_processed_value',array('null'=>true));
										
			if ((int)$chunkOrders>0) {
				$orders->getSelect()->limit($chunkOrders);
			}
						
			$this->helper->logData((string)$orders->getSelect());
			foreach ($orders as $order) {
				if ((int)$order->getData('scommerce_gdpr_processed_value')==0){
					$this->saleAnonymise->order($order);
					
					$this->helper->logData($order->getIncrementId());
					
					$connection = $this->resource->getConnection();
					$connection->update(
						$this->resource->getTableName('sales_order'),
						[
							'scommerce_gdpr_processed_value'=>1
						],
						'scommerce_gdpr_processed_value is NULL and entity_id=' . $order->getEntityId()
					);
				}
			}
		}
    }
}

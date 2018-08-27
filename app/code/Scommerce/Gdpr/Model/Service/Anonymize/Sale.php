<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Service\Anonymize;

/**
 * Anonymize sale data (orders/quotes) service
 *
 * Class Sale
 * @package Scommerce\Gdpr\Model\Service\Anonymize
 */
class Sale
{
    /** @var \Scommerce\Gdpr\Model\Service\Context */
    private $context;

    /**
     * @param \Scommerce\Gdpr\Model\Service\Context $context
     */
    public function __construct(
        \Scommerce\Gdpr\Model\Service\Context $context
    ) {
        $this->context = $context;
    }

    /**
     * Anonymize specified orders
     *
     * @param array $ids Orders identifiers
     * @throws \Exception
     */
    public function orders($ids = [])
    {
        $orders = $this->context->getOrderCollection()
            ->addFieldToFilter('entity_id', ['in' => $ids])
        ;
        foreach ($orders as $order) {
            $this->order($order);
        }
    }

    /**
     * Anonymize specified order
     *
     * @param \Magento\Sales\Model\Order $model
     * @throws \Exception
     */
    public function order(\Magento\Sales\Model\Order $model)
    {
        $this->context->getOrderRepository()->save(
            $this->context->getHelper()->getAnonymize()->sale($model)
        );
    }

    /**
     * Anonymize specified quote
     *
     * @param \Magento\Quote\Model\Quote $model
     * @throws \Exception
     */
    public function quote(\Magento\Quote\Model\Quote $model)
    {
        $this->context->getQuoteRepository()->save(
            $this->context->getHelper()->getAnonymize()->sale($model)
        );
    }
}

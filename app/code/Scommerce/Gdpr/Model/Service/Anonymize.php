<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Service;

/**
 * Anonymize service
 *
 * Class Anonymize
 * @package Scommerce\Gdpr\Model\Service
 */
class Anonymize
{
    /** @var \Magento\Framework\Registry */
    private $registry;

    /** @var \Scommerce\Gdpr\Model\Service\Context */
    private $context;

    /** @var \Scommerce\Gdpr\Model\Service\Anonymize\Sale */
    private $sale;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Scommerce\Gdpr\Model\Service\Context $context
     * @param \Scommerce\Gdpr\Model\Service\Anonymize\Sale $sale
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Scommerce\Gdpr\Model\Service\Context $context,
        \Scommerce\Gdpr\Model\Service\Anonymize\Sale $sale
    ) {
        $this->registry = $registry;
        $this->context = $context;
        $this->sale = $sale;
    }

    /**
     * Anonymize customer data
     *
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @return bool
     * @throws \Exception
     */
    public function execute(\Magento\Customer\Model\Data\Customer $customer)
    {
        $key = 'isSecureArea';
        $isSecure = $this->enable($key);
        try {
            $result = $this->exec($customer);
            $this->restore($key, $isSecure);
        } catch (\Exception $e) {
            $this->restore($key, $isSecure);
            throw new $e;
        }
        return $result;
    }

    /**
     * Anonymize customer data
     *
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @return bool
     * @throws \Exception
     */
    private function exec(\Magento\Customer\Model\Data\Customer $customer)
    {
        $orders = $this->context->getOrderCollection($customer->getId());
        foreach ($orders as $order) {
            $this->sale->order($order);
        }

        $quotes = $this->context->getQuoteCollection($customer->getId());
        foreach ($quotes as $quote) {
            $this->sale->quote($quote);
        }

        $subscriber = $this->context->getSubscriber($customer->getEmail());
        if ($subscriber->getId()) {
            $subscriber->delete();
        }

        $this->context->getHelper()->sendDeletionEmail($customer);
        $this->context->getCustomerRepository()->delete($customer);
        return true;
    }

    /**
     * Enable secure by registry
     *
     * @param string $key
     * @return bool|null
     */
    private function enable($key)
    {
        $value = $this->registry->registry($key);
        $this->registry->unregister($key);
        $this->registry->register($key, true);
        return $value;
    }

    /**
     * Restore registry secure value
     *
     * @param string $key
     * @param bool|null $value
     */
    private function restore($key, $value)
    {
        $this->registry->unregister($key);
        if ($value !== null) {
            $this->registry->register($key, $value);
        }
    }
}

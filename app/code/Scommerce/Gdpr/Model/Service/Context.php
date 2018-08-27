<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Service;

/**
 * Class Context
 * @package Scommerce\Gdpr\Model\Service
 */
class Context
{
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    private $customerRepository;

    /** @var \Magento\Quote\Api\CartRepositoryInterface */
    private $quoteRepository;

    /** @var \Magento\Sales\Api\OrderRepositoryInterface */
    private $orderRepository;

    /** @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory */
    private $quoteCollectionFactory;

    /** @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface */
    private $orderCollectionFactory;

    /** @var \Magento\Newsletter\Model\SubscriberFactory */
    private $subscriberFactory;

    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface $orderCollectionFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Scommerce\Gdpr\Helper\Data $helper
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface $orderCollectionFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Scommerce\Gdpr\Helper\Data $helper
    ) {
        $this->customerRepository = $customerRepository;
        $this->quoteRepository = $quoteRepository;
        $this->orderRepository = $orderRepository;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->helper = $helper;
    }

    /**
     * Helper method for create new quote collection
     *
     * @param int $customerId Customer identifier
     * @return \Magento\Quote\Model\ResourceModel\Quote\Collection
     */
    public function getQuoteCollection($customerId = null)
    {
        /** @var \Magento\Quote\Model\ResourceModel\Quote\Collection $collection */
        $collection = $this->quoteCollectionFactory->create();
        if ($customerId) {
            $collection->addFieldToFilter('customer_id', $customerId);
        }
        return $collection;
    }

    /**
     * Helper method for create new order collection
     *
     * @param int $customerId Customer identifier
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrderCollection($customerId = null)
    {
        return $this->orderCollectionFactory->create($customerId);
    }

    /**
     * Helper method for create new subscriber instance for specified email
     *
     * @param string $email
     * @return \Magento\Newsletter\Model\Subscriber
     */
    public function getSubscriber($email)
    {
        return $this->subscriberFactory->create()->loadByEmail($email);
    }

    /**
     * @return \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public function getCustomerRepository()
    {
        return $this->customerRepository;
    }

    /**
     * @return \Magento\Quote\Api\CartRepositoryInterface
     */
    public function getQuoteRepository()
    {
        return $this->quoteRepository;
    }

    /**
     * @return \Magento\Sales\Api\OrderRepositoryInterface
     */
    public function getOrderRepository()
    {
        return $this->orderRepository;
    }

    /**
     * @return \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    public function getQuoteCollectionFactory()
    {
        return $this->quoteCollectionFactory;
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface
     */
    public function getOrderCollectionFactory()
    {
        return $this->orderCollectionFactory;
    }

    /**
     * @return \Magento\Newsletter\Model\SubscriberFactory
     */
    public function getSubscriberFactory()
    {
        return $this->subscriberFactory;
    }

    /**
     * @return \Scommerce\Gdpr\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }
}

<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_AutomatedCustomerGroup
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\AutomatedCustomerGroup\Model;


class AutomatedCustomerGroup
{
    /**
     * Customer Group ID for Tea Connoissuer
     */
    const TEA_CONNOISSEUR_CUSTOMER_GROUP_ID = 5;

    /**
     * Customer Repository [API]
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Rewards history factory
     * @var \Magento\Reward\Model\ResourceModel\Reward\History\CollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $resourceIterator;

    /**
     * @var array
     */
    protected $customers = [];

    /**
     * AutomatedCustomerGroup constructor.
     * @param \Magento\Reward\Model\ResourceModel\Reward\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator
     */
    public function __construct(
        \Magento\Reward\Model\ResourceModel\Reward\History\CollectionFactory $historyCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->customerRepository = $customerRepository;
        $this->resourceIterator = $resourceIterator;
    }

    /**
     * Get customers whos reward points exceeds given param
     *
     * @param int $points
     * @return array
     */
    public function getCustomersEmailsByRewardPoints($points)
    {
        $select = $this->historyCollectionFactory->create()
            ->addFieldToSelect(['reward_id', 'points_delta'])
            ->getSelect();
        $select->joinInner(
            ['mr' => "magento_reward"],
            'mr.reward_id = main_table.reward_id',
            ["ce.email", "ce.entity_id"]
        )->joinInner(
            ['ce' => "customer_entity"],
            'ce.entity_id = mr.customer_id',
            ['website_id', 'firstname', 'lastname', 'store_id']
        )->where("main_table.points_delta > 0 AND ce.group_id = 1")
            ->group("mr.customer_id")
            ->having("SUM(main_table.points_delta) > " . $points);

        $this->resourceIterator->walk(
            $select,
            [[$this, 'callbackCustomer']]
        );

        return $this->customers;
    }

    /**
     * call back function for the customer collection
     * @param array $args
     * @return void
     */
    public function callbackCustomer($args)
    {
        $row = $args['row'];
        $item = [];

        $item["entity_id"] = $row['entity_id'];
        $item["email"] = $row['email'];
        $item["store_id"] = $row['store_id'];
        $item["website_id"] = $row['website_id'];
        $item["name"] = implode(' ', [$row['firstname'], $row['lastname']]);

        $this->customers[$row['website_id']][] = $item;
    }

    /**
     * Update customers to TEA_CONNOISSEUR_CUSTOMER_GROUP
     *
     * @param string $customerId
     * @param string $customerEmail
     * @return bool|\Magento\Customer\Api\Data\CustomerInterface
     */
    public function updateCustomerGroup($customerId, $customerEmail)
    {
        $customer = $this->customerRepository->getById($customerId);
        if ($customer->getEmail() == $customerEmail) {
            $customer->setGroupId(self::TEA_CONNOISSEUR_CUSTOMER_GROUP_ID);
        }
        try {
            $customer = $this->customerRepository->save($customer);
            return $customer;
        } catch (\Exception $e) {
            return false;
        }
    }
}

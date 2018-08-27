<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Sales
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Sales\Plugin\Model\Order;

use Magento\Framework\Exception\AlreadyExistsException;
//use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\Data\Address as CustomerAddressModel;
use Magento\Framework\Exception\InputException;

/**
 * Class CustomerManagement
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerManagement
{

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Customer\Api\Data\RegionInterfaceFactory
     */
    protected $regionFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Framework\DataObject\Copy
     */
    protected $objectCopyService;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * CustomerManagement constructor.
     *
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory
     * @param \Magento\Customer\Api\Data\RegionInterfaceFactory $regionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory,
        \Magento\Customer\Api\Data\RegionInterfaceFactory $regionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Customer\Model\Session $customerSession,
        SubscriberFactory $subscriberFactory
    ) {
        $this->objectCopyService = $objectCopyService;
        $this->accountManagement = $accountManagement;
        $this->orderRepository = $orderRepository;
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->regionFactory = $regionFactory;
        $this->customerSession = $customerSession;
        $this->subscriberFactory = $subscriberFactory;

    }

    /**
     * @param \Magento\Sales\Model\Order\CustomerManagement $subject
     * @param \Closure $proceed
     * @param int $selection
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws AlreadyExistsException
     * @SuppressWarnings("unused")
     */
    public function aroundCreate(
        \Magento\Sales\Model\Order\CustomerManagement $subject,
        \Closure $proceed,
        $selection
    ) {
        $order = $this->orderRepository->get($selection);
        if ($order->getCustomerId()) {
            throw new AlreadyExistsException(__("This order already has associated customer account"));
        }
        $customerData = $this->objectCopyService->copyFieldsetToTarget(
            'order_address',
            'to_customer',
            $order->getBillingAddress(),
            []
        );
        $addresses = $order->getAddresses();
        foreach ($addresses as $address) {
            $addressData = $this->objectCopyService->copyFieldsetToTarget(
                'order_address',
                'to_customer_address',
                $address,
                []
            );
            /** @var \Magento\Customer\Api\Data\AddressInterface $customerAddress */
            $customerAddress = $this->addressFactory->create(['data' => $addressData]);
            switch ($address->getAddressType()) {
                case QuoteAddress::ADDRESS_TYPE_BILLING:
                    $customerAddress->setIsDefaultBilling(true);
                    break;
                case QuoteAddress::ADDRESS_TYPE_SHIPPING:
                    $customerAddress->setIsDefaultShipping(true);
                    break;
            }

            if (is_string($address->getRegion())) {
                /** @var \Magento\Customer\Api\Data\RegionInterface $region */
                $region = $this->regionFactory->create();
                $region->setRegion($address->getRegion());
                $region->setRegionCode($address->getRegionCode());
                $region->setRegionId($address->getRegionId());
                $customerAddress->setRegion($region);
            }
            $customerData['addresses'][] = $customerAddress;
        }

        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $this->customerFactory->create(['data' => $customerData]);
        $pw = $this->customerSession->getPassword();
        $pw = (empty($pw)) ? null : $pw;
        $account = $this->accountManagement->createAccount($customer, $pw);
        $isSubscribed = $this->customerSession->getIsSubscribed();
        if ($isSubscribed) {
            $this->subscriberFactory->create()->subscribeCustomerById($account->getId());
        }
        $order->setCustomerId($account->getId());
        $order->setCustomerIsGuest(0);
        $this->orderRepository->save($order);
        return $account;
    }
}

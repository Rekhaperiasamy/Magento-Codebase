<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Service;

use Magento\Newsletter\Model\Subscriber;

/**
 * Export service
 *
 * Class Export
 * @package Scommerce\Gdpr\Model\Service
 */
class Export
{
    /** @var \Scommerce\Gdpr\Model\Service\Context */
    private $context;

    /** @var \Scommerce\Gdpr\Model\Service\Export\StorageFactory */
    private $storageFactory;

    /**
     * @param \Scommerce\Gdpr\Model\Service\Context $context
     * @param \Scommerce\Gdpr\Model\Service\Export\StorageFactory $storageFactory
     */
    public function __construct(
        \Scommerce\Gdpr\Model\Service\Context $context,
        \Scommerce\Gdpr\Model\Service\Export\StorageFactory $storageFactory
    ) {
        $this->context = $context;
        $this->storageFactory = $storageFactory;
    }

    /**
     * Export customer details
     *
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @return string
     * @throws \Exception
     */
    public function execute(\Magento\Customer\Model\Data\Customer $customer)
    {
        return $this->storageFactory->create()
            ->addRecord($this->getDetails($customer)) // Customer Details
            ->addRecord($this->getAddresses($customer)) // Customer Addresses
            ->addRecord($this->getOrders($customer)) // Order Details
            ->addRecord($this->getQuotes($customer)) // Quotes Details
            ->addRecord($this->getSubscriptions($customer)) // Newsletter Subscription Details
            ->render()
        ;
    }

    /**
     * Customer Details
     *
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @return array
     * @throws \Exception
     */
    private function getDetails($customer)
    {
        if (! $customer->getEmail()) {
            return [];
        }
        return [
            'Customer Details',
            [
                'Prefix', 'First Name', 'Middle Name', 'Last Name', 'Email Address', 'DOB', 'Gender'
            ],
            [[
                $customer->getPrefix(), $customer->getFirstname(), $customer->getMiddlename(),
                $customer->getLastname(), $customer->getEmail(), $customer->getDob(), $this->getGenderLabel($customer->getGender()),
            ]]
        ];
    }

    /**
     * Customer Addresses
     *
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @return array
     * @throws \Exception
     */
    private function getAddresses($customer)
    {
        $addresses = $customer->getAddresses();
        if (empty($addresses)) {
            return [];
        }
        $details = [];
        foreach ($addresses as $a) {
            $region = $a->getRegion();
            $region = $region instanceOf \Magento\Customer\Api\Data\RegionInterface ? $region->getRegion() : '';
            $details[] = [
                $a->getPrefix(), $a->getFirstname(), $a->getMiddlename(),
                $a->getLastname(), $a->getCompany(), $region,
                $this->getStreet($a->getStreet()), $a->getCity(), $a->getPostcode(),
                $a->getTelephone(), $a->getFax(), $a->getVatId()
            ];
        }
        return [
            'Customer Address Details',
            [
                'Prefix', 'First Name', 'Middle Name', 'Last Name', 'Company', 'Region',
                'Street', 'City', 'Post Code', 'Telephone', 'Fax', 'VAT'
            ],
            $details
        ];
    }

    /**
     * Customer Orders Details
     *
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @return array
     * @throws \Exception
     */
    private function getOrders($customer)
    {
        $orders = $this->context->getOrderCollection($customer->getId());
        if ($orders->count() == 0) {
            return [];
        }
        $details = [];
        foreach ($orders as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            $details[] = array_merge([$order->getIncrementId()], $this->getSaleData($order));
        }
        return [
            'Order Details',
            array_merge(['Order number'], $this->getSaleHeader()),
            $details
        ];
    }

    /**
     * Customer Quotes Details
     *
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @return array
     * @throws \Exception
     */
    private function getQuotes($customer)
    {
        $quotes = $this->context->getQuoteCollection($customer->getId());
        if ($quotes->count() == 0) {
            return [];
        }
        $details = [];
        foreach($quotes as $quote) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $details[] = $this->getSaleData($quote);
        }
        return [
            'Quote Details',
            $this->getSaleHeader(),
            $details
        ];
    }

    /**
     * Customer Newsletter Subscription Details
     *
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @return array
     * @throws \Exception
     */
    private function getSubscriptions($customer)
    {
        $subscriber = $this->context->getSubscriber($customer->getEmail());
        if (! $subscriber->getId()) {
            return [];
        }
        return [
            'Newsletter Subscription Details',
            ['Subscriber Email', 'Status', 'Change Status At'],
            [[
                $subscriber->getSubscriberEmail(),
                $this->getSubscriberStatus($subscriber->getSubscriberStatus()),
                $subscriber->getChangeStatusAt()
            ]]
        ];
    }

    /**
     * Helper method for get sales data header
     *
     * @return array
     */
    private function getSaleHeader()
    {
        return [
            'First Name', 'Middle Name', 'Last Name', 'Email Address', 'Remote IP',

            'Billing First Name', 'Billing Middle Name',  'Billing Last Name',
            'Billing Company', 'Billing Email Address', 'Billing Region', 'Billing Street',
            'Billing City', 'Billing Post Code', 'Billing Telephone', 'Billing Fax',
            'Billing VAT',

            'Shipping First Name', 'Shipping Middle Name', 'Shipping Last Name',
            'Shipping Company', 'Shipping Email Address', 'Shipping Region', 'Shipping Street',
            'Shipping City', 'Shipping Post Code', 'Shipping Telephone', 'Shipping Fax',
            'Shipping VAT',
        ];
    }

    /**
     * Helper method for build street as string
     *
     * @param string|array $street
     * @return string
     */
    private function getStreet($street)
    {
        return is_array($street) ? implode(' ', $street) : (string) $street;
    }

    /**
     * Helper method for get order/quote data
     *
     * @param \Magento\Sales\Model\Order|\Magento\Quote\Model\Quote $o
     * @return array
     */
    private function getSaleData($o)
    {
        $data = [
            $o->getCustomerFirstname(), $o->getCustomerMiddlename(), $o->getCustomerLastname(),
            $o->getCustomerEmail(), $o->getRemoteIp()
        ];
        $data = $this->addAddress($data, $o->getBillingAddress());
        $data = $this->addAddress($data, $o->getShippingAddress());
        return $data;
    }

    /**
     * Helper method for add address data
     *
     * @param array $data
     * @param \Magento\Sales\Model\Order\Address|\Magento\Quote\Model\Quote\Address $address
     * @return array
     */
    private function addAddress($data, $address)
    {
        return $address ? array_merge($data, $this->getAddress($address)) : $data;
    }

    /**
     * Helper method for get address data
     *
     * @param \Magento\Sales\Model\Order\Address|\Magento\Quote\Model\Quote\Address $a
     * @return array
     */
    private function getAddress($a)
    {
        if (! ($a instanceof \Magento\Sales\Model\Order\Address || $a instanceof \Magento\Quote\Model\Quote\Address)) {
            return [];
        }
        $streetAddress = $this->getStreet($a->getStreet());
        return [
            $a->getFirstname(), $a->getMiddlename(), $a->getLastname(), $a->getCompany(), $a->getEmail(),
            $a->getRegion(), $streetAddress, $a->getCity(), $a->getPostcode(), $a->getTelephone(),
            $a->getFax(), $a->getVatId()
        ];
    }

    /**
     * Helper method for get subscriber status via status identifier
     *
     * @param int $status
     * @return string
     */
    private function getSubscriberStatus($status)
    {
        switch ((int) $status) {
            case Subscriber::STATUS_SUBSCRIBED: $title = 'Subscribed'; break;
            case Subscriber::STATUS_NOT_ACTIVE: $title = 'Not Active'; break;
            case Subscriber::STATUS_UNSUBSCRIBED: $title = 'Unsubscribed'; break;
            case Subscriber::STATUS_UNCONFIRMED: $title = 'Unconfirmed'; break;
            default: $title = 'Uncknown';
        }
        return __($title);
    }

    /**
     * Helper method for get gender label customer
     *
     * @param int|null $gender
     * @return string
     */
    private function getGenderLabel($gender)
    {
        switch ((int) $gender) {
            case 1: $gender = __('Male'); break;
            case 2: $gender = __('Female'); break;
            default: $gender = '';
        }
        return $gender;
    }
}

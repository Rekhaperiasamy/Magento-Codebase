<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Service;

use Magento\Framework\Exception\LocalizedException;
use Scommerce\Gdpr\Api\Data\ConsentInterface;

/**
 * Class ConsentService
 * @package Scommerce\Gdpr\Model\Service
 */
class ConsentService
{
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    /** @var \Magento\Framework\Api\SearchCriteriaBuilder */
    private $criteriaBuilder;

    /** @var \Magento\Framework\Api\SortOrderBuilder */
    private $orderBuilder;

    /** @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress */
    private $remoteAddress;

    /** @var \Scommerce\Gdpr\Api\ConsentRepositoryInterface */
    private $repository;

    /** @var \Scommerce\Gdpr\Model\ConsentFactory */
    private $consentFactory;

    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
     * @param \Magento\Framework\Api\SortOrderBuilder $orderBuilder
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Scommerce\Gdpr\Api\ConsentRepositoryInterface $repository
     * @param \Scommerce\Gdpr\Model\ConsentFactory $consentFactory
     * @param \Scommerce\Gdpr\Helper\Data $helper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder $orderBuilder,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Scommerce\Gdpr\Api\ConsentRepositoryInterface $repository,
        \Scommerce\Gdpr\Model\ConsentFactory $consentFactory,
        \Scommerce\Gdpr\Helper\Data $helper
    ) {
        $this->storeManager = $storeManager;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->orderBuilder = $orderBuilder;
        $this->remoteAddress = $remoteAddress;
        $this->repository = $repository;
        $this->consentFactory = $consentFactory;
        $this->helper = $helper;
    }

    /**
     * Create new record with Newsletter source for current customer or for anonymous user
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface|string $value
     * @return \Magento\Framework\Model\AbstractModel|ConsentInterface
     * @throws LocalizedException
     */
    public function createNewsletter($value)
    {
        return $this->create($this->getNewsletterKey(), $value);
    }

    /**
     * Create new record with Contact Us source for current customer or for anonymous user
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface|string $value
     * @return \Magento\Framework\Model\AbstractModel|ConsentInterface
     * @throws LocalizedException
     */
    public function createContactUs($value)
    {
        return $this->create($this->getContactUsKey(), $value);
    }

    /**
     * Create new record with Checkout source for current customer or for anonymous user
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface|string $value
     * @return \Magento\Framework\Model\AbstractModel|ConsentInterface
     * @throws LocalizedException
     */
    public function createCheckout($value)
    {
        return $this->create($this->getCheckoutKey(), $value);
    }

    /**
     * Create new record with Registration source for customer
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface|string $value
     * @return \Magento\Framework\Model\AbstractModel|ConsentInterface
     * @throws LocalizedException
     */
    public function createRegistration($value)
    {
        return $this->create($this->getRegistrationKey(), $value);
    }

    /**
     * Check if record with Newsletter source is exists for current customer or for anonymous user by email
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface|string $value
     * @return bool
     * @throws LocalizedException
     */
    public function isExistsNewsletter($value)
    {
        return $this->isExists($this->getNewsletterKey(), $value);
    }

    /**
     * Check if record with Conatact Us source is exists for current customer or for anonymous user by email
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface|string $value
     * @return bool
     * @throws LocalizedException
     */
    public function isExistsContactUs($value)
    {
        return $this->isExists($this->getContactUsKey(), $value);
    }

    /**
     * Check if record with Checkout source is exists for current customer or for anonymous user by email
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface|string $value
     * @return bool
     * @throws LocalizedException
     */
    public function isExistsCheckout($value)
    {
        return $this->isExists($this->getCheckoutKey(), $value);
    }

    /**
     * Create new record with specified source for current customer or for anonymous user
     *
     * @param string $source
     * @param \Magento\Customer\Api\Data\CustomerInterface|string $value
     * @return \Magento\Framework\Model\AbstractModel|ConsentInterface
     * @throws LocalizedException
     */
    private function create($source, $value)
    {
        if ($this->isExists($source, $value)) {
            throw new LocalizedException(__('Consent already exists'));
        }

        $consent = $this->consentFactory->create();
        $consent->setSource($source);
        $consent->setWebsiteId($this->storeManager->getWebsite()->getId());
        if ($this->isCustomer($value)) {
            $consent->setCustomerId($value->getId());
            $consent->setGuestEmail($value->getEmail());
        } else {
            $consent->setGuestEmail((string) $value);
        }
        $consent->setRemoteIp($this->remoteAddress->getRemoteAddress());

        return $this->repository->save($consent);
    }

    /**
     * Check if record with specified source is exists for current customer or for anonymous user by email
     *
     * @param string $source
     * @param \Magento\Customer\Api\Data\CustomerInterface|string $value
     * @return bool
     * @throws LocalizedException
     */
    private function isExists($source, $value)
    {
        $builder = $this->criteriaBuilder
            ->addFilter(ConsentInterface::WEBSITE_ID, $this->storeManager->getWebsite()->getId())
            ->addFilter(ConsentInterface::SOURCE, $source);
        if ($this->isCustomer($value)) {
            $builder->addFilter(ConsentInterface::CUSTOMER_ID, $value->getId());
        } else {
            $builder->addFilter(ConsentInterface::GUEST_EMAIL, (string) $value);
        }
        $collection = $this->repository->getList($builder->create());
        return $collection->getTotalCount() > 0;
    }

    /**
     * Check if specified value instance of Customer
     *
     * @param mixed $value
     * @return bool
     */
    private function isCustomer($value)
    {
        return $value instanceof \Magento\Customer\Api\Data\CustomerInterface;
    }

    /**
     * @return string
     */
    private function getNewsletterKey()
    {
        return $this->helper->getPrivacySourceHelper()->getNewsletterKey();
    }

    /**
     * @return string
     */
    private function getContactUsKey()
    {
        return $this->helper->getPrivacySourceHelper()->getContactUsKey();
    }

    /**
     * @return string
     */
    private function getCheckoutKey()
    {
        return $this->helper->getPrivacySourceHelper()->getCheckoutKey();
    }

    /**
     * @return string
     */
    private function getRegistrationKey()
    {
        return $this->helper->getPrivacySourceHelper()->getRegistrationKey();
    }
}

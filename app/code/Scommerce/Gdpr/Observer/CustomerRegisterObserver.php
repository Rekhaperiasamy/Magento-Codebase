<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Observer;

/**
 * Class CustomerRegisterObserver
 * @package Scommerce\Gdpr\Observer
 */
class CustomerRegisterObserver implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \Scommerce\Gdpr\Model\Service\ConsentService */
    private $service;

    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Scommerce\Gdpr\Model\Service\ConsentService $service
     * @param \Scommerce\Gdpr\Helper\Data $helper
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Scommerce\Gdpr\Model\Service\ConsentService $service,
        \Scommerce\Gdpr\Helper\Data $helper
    ) {
        $this->logger = $logger;
        $this->service = $service;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (! $this->helper->isEnabled()) {
            return;
        }

        /** @var \Magento\Customer\Controller\Account\CreatePost $controller */
        $controller = $observer->getAccountController();

        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $observer->getCustomer();

        if ($this->helper->isPrivacyRegistrationEnabled()) {
            try {
                $this->service->createRegistration($customer);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        if ($controller->getRequest()->getParam('is_subscribed', false) && $this->helper->isPrivacyNewsletterEnabled()) {
            try {
                $this->service->createNewsletter($customer);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}

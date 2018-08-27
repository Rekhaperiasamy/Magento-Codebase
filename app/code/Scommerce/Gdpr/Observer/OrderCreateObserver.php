<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Observer;

/**
 * Class OrderCreateObserver
 * @package Scommerce\Gdpr\Observer
 */
class OrderCreateObserver implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Magento\Customer\Model\Session */
    private $session;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \Scommerce\Gdpr\Model\Service\ConsentService */
    private $service;

    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Customer\Model\Session $session
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Scommerce\Gdpr\Model\Service\ConsentService $service
     * @param \Scommerce\Gdpr\Helper\Data $helper
     */
    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Psr\Log\LoggerInterface $logger,
        \Scommerce\Gdpr\Model\Service\ConsentService $service,
        \Scommerce\Gdpr\Helper\Data $helper
    ) {
        $this->session = $session;
        $this->logger = $logger;
        $this->service = $service;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (! $this->helper->isEnabled()) {
            return $this;
        }
        if (! $this->helper->isPrivacyCheckoutEnabled()) {
            return $this;
        }

        /** @var \Magento\Framework\Model\AbstractExtensibleModel|\Magento\Sales\Model\Order|\Magento\Sales\Api\Data\OrderInterface $order */
        $order = $observer->getEvent()->getOrder();
        try {
			$value = $this->session->getCustomerData();
			if (! $value) {
				$value = $order->getCustomerEmail();
			}
            $this->service->createCheckout($value);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }
}

<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Plugin;

/**
 * Class SubscribePlugin
 * @package Scommerce\Gdpr\Model\Plugin
 */
class SubscribePlugin
{
    /** @var \Magento\Customer\Model\Session */
    private $session;

    /** @var \Scommerce\Gdpr\Model\Service\ConsentService */
    private $service;

    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Customer\Model\Session $session
     * @param \Scommerce\Gdpr\Model\Service\ConsentService $service
     * @param \Scommerce\Gdpr\Helper\Data $helper
     */
    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Scommerce\Gdpr\Model\Service\ConsentService $service,
        \Scommerce\Gdpr\Helper\Data $helper
    ) {
        $this->session = $session;
        $this->service = $service;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Newsletter\Controller\Subscriber\NewAction $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterExecute(\Magento\Newsletter\Controller\Subscriber\NewAction $subject, $result)
    {
        if (! $this->helper->isEnabled()) {
            return $result;
        }
        if (! $this->helper->isPrivacyNewsletterEnabled()) {
            return $result;
        }
        $email = (string)$subject->getRequest()->getPost('email');
        if (empty($email)) {
            return $result;
        }
        $customer = $this->session->getCustomerData();
        $value = $customer ? $customer->setEmail($email) : $email;
        try {
            $this->service->createNewsletter($value);
        } catch (\Exception $e) {
            // Do nothing
        }
        return $result;
    }
}

<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Plugin;

/**
 * Class ContactPostPlugin
 * @package Scommerce\Gdpr\Model\Plugin
 */
class ContactPostPlugin
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
     * @param \Magento\Contact\Controller\Index\Post $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterExecute(\Magento\Contact\Controller\Index\Post $subject, $result)
    {
        if (! $this->helper->isEnabled()) {
            return $result;
        }
        if (! $this->helper->isPrivacyContactUsEnabled()) {
            return $result;
        }
        $value = $this->session->getCustomerData();
        if (! $value) {
            $post = $subject->getRequest()->getPostValue();
            $value = trim($post['email']);
        }
        $this->create($value);

        return $result;
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface|string $value
     */
    private function create($value)
    {
        try {
            $this->service->createContactUs($value);
        } catch (\Exception $e) {
            // Do nothing
        }
    }
}

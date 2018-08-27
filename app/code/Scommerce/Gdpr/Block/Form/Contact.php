<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Block\Form;

/**
 * Class Contact
 * @package Scommerce\Gdpr\Block\Form
 */
class Contact extends \Magento\Framework\View\Element\Template
{
    /** @var \Magento\Customer\Model\Session */
    private $session;

    /** @var \Scommerce\Gdpr\Model\Service\ConsentService */
    private $service;

    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Scommerce\Gdpr\Model\Service\ConsentService $service
     * @param \Scommerce\Gdpr\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Scommerce\Gdpr\Model\Service\ConsentService $service,
        \Scommerce\Gdpr\Helper\Data $helper,
        array $data = []
    ) {
        $this->session = $session;
        $this->service = $service;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Show block only if module enabled and Registration source enabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->canShow() ? parent::_toHtml() : '';
    }

    /**
     * @return string
     */
    public function getPrivacySettingText()
    {
        return $this->helper->getPrivacySettingText();
    }

    /**
     * Helper method for check if block can be shown
     *
     * @return bool
     */
    private function canShow()
    {
        $can = $this->helper->isEnabled() && $this->helper->isPrivacyContactUsEnabled();
        if (! $can) {
            return false;
        }
        $customer = $this->session->getCustomerData();
        if (! $customer) {
            return true;
        }
        try {
            $can = ! $this->service->isExistsContactUs($customer);
        } catch (\Exception $e) {
            $can = false;
        }
        return $can;
    }
}

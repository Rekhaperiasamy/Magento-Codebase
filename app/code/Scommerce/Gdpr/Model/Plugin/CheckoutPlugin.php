<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Plugin;

/**
 * Class CheckoutPlugin
 * @package Scommerce\Gdpr\Model\Plugin
 */
class CheckoutPlugin
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
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $subject, $jsLayout)
    {
        if (! $this->helper->isEnabled()) {
            return $jsLayout;
        }
        if (! $this->helper->isPrivacyCheckoutEnabled()) {
            return $jsLayout;
        }
        if ($this->isExists()) {
            return $jsLayout;
        }

        $customAttributeCode = 'scommerce_gdpr_privacy_consent';
        $field = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'Scommerce_Gdpr/form/element/checkbox',
                'id' => $customAttributeCode,
                'description' => $this->helper->getPrivacySettingText(),
            ],
            'provider' => 'checkoutProvider',
            'dataScope' => 'shippingAddress.' . $customAttributeCode,
            'label' => __('Privacy Policy'),
            'sortOrder' => 999,
            'validation' => [
                'required-entry' => true,
            ],
            'id' => $customAttributeCode,
        ];
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'][$customAttributeCode] = $field;

        return $jsLayout;
    }

    /**
     * @return bool
     */
    private function isExists()
    {
        if (! $this->session->getCustomerId()) {
            return false;
        }
        try {
            $exists = $this->service->isExistsCheckout($this->session->getCustomer());
        } catch (\Exception $e) {
            $exists = false;
        }
        return $exists;
    }
}

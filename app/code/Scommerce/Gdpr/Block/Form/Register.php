<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Block\Form;

/**
 * Class Register
 * @package Scommerce\Gdpr\Block\Form
 */
class Register extends \Magento\Framework\View\Element\Template
{
    /** @var \Magento\Customer\Block\Form\Register */
    private $register;

    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Block\Form\Register $register
     * @param \Scommerce\Gdpr\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Block\Form\Register $register,
        \Scommerce\Gdpr\Helper\Data $helper,
        array $data = []
    ) {
        $this->register = $register;
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
        return $this->helper->isEnabled() && $this->helper->isPrivacyRegistrationEnabled() ? parent::_toHtml() : '';
    }

    /**
     * @return mixed
     */
    public function getFormData()
    {
        return $this->register->getFormData();
    }

    /**
     * @return string
     */
    public function getPrivacySettingText()
    {
        return $this->helper->getPrivacySettingText();
    }
}

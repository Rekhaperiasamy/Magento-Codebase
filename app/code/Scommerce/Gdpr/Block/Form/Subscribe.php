<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Block\Form;

/**
 * Class Subscribe
 * @package Scommerce\Gdpr\Block\Form
 */
class Subscribe extends \Magento\Framework\View\Element\Template
{
    /** @var \Scommerce\Gdpr\Model\Service\ConsentService */
    private $service;

    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Scommerce\Gdpr\Model\Service\ConsentService $service
     * @param \Scommerce\Gdpr\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Scommerce\Gdpr\Model\Service\ConsentService $service,
        \Scommerce\Gdpr\Helper\Data $helper,
        array $data = []
    ) {
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
        try {
            $can = $this->helper->isEnabled()
                && $this->helper->isPrivacyNewsletterEnabled();
        } catch (\Exception $e) {
            $can = false;
        }
        return $can;
    }
}

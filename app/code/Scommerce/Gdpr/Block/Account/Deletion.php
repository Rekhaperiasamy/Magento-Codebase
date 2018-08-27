<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Block\Account;

/**
 * Block for rendering message and link in front customer account area
 *
 * Class Deletion
 * @package Scommerce\Gdpr\Block\Account
 */
class Deletion extends \Magento\Framework\View\Element\Template
{
    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Scommerce\Gdpr\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Scommerce\Gdpr\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get attention message
     *
     * @return string
     */
    public function getAttentionMessage()
    {
        return $this->helper->getAttentionMessage();
    }

    /**
     * Get front delete url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->helper->getDeleteUrl();
    }

    /**
     * Render block only if module enabled and delete on front setting is enabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->helper->isEnabled() && $this->helper->isDeletionEnabledOnFrontend() ? parent::_toHtml() : '';
    }
}

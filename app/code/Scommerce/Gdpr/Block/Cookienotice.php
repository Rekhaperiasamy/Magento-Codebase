<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Block;

/**
 * Class Cookienotice
 * @package Scommerce\Gdpr\Block
 */
class Cookienotice extends \Magento\Framework\View\Element\Template
{
    /** @var \Magento\Framework\Stdlib\CookieManagerInterface */
    private $cookieManager;

    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Scommerce\Gdpr\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Scommerce\Gdpr\Helper\Data $helper,
        array $data = []
    ) {
        $this->cookieManager = $cookieManager;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Is blocked enable
     *
     * @return bool
     */
    public function isBlocked()
    {
        return $this->helper->isBlocked();
    }

    /**
     * Is message position set to bottom
     *
     * @return bool
     */
    public function isBottom()
    {
        return $this->helper->isCookieMessagePositionBottom();
    }

    /**
     * @return string
     */
    public function getCssPageWrapperClass()
    {
        return $this->helper->getCssPageWrapperClass();
    }

    /**
     * @return string
     */
    public function getCmsPageUrl()
    {
        return $this->helper->getCmsPageUrl();
    }

    /**
     * @return string
     */
    public function getCookieTextMessage()
    {
        return $this->helper->getCookieTextMessage();
    }

    /**
     * @return string
     */
    public function getCookieLinkText()
    {
        return $this->helper->getCookieLinkText();
    }

    /**
     * @return string
     */
    public function getCookieTextColor()
    {
        return $this->helper->getCookieTextColor();
    }

    /**
     * @return string
     */
    public function getCookieBackgroundColor()
    {
        return $this->helper->getCookieBackgroundColor();
    }

    /**
     * @return string
     */
    public function getCookieLinkColor()
    {
        return $this->helper->getCookieLinkColor();
    }

    /**
     * Get cookie key to check accepted cookie policy
     *
     * @return string
     */
    public function getCookieKey()
    {
        return 'cookie_accepted';
    }

    /**
     * Get cookie key to check if cookie message was closed
     *
     * @return string
     */
    public function getCookieClosedKey()
    {
        return $this->helper->getCookieClosedKey();
    }

    /**
     * Check if has cookie with accepted cookie policy
     *
     * @return bool
     */
    public function hasCookie()
    {
        return $this->cookieManager->getCookie($this->getCookieClosedKey()) !== null;
    }

    /**
     * Show block only if:
     *  a. Module enabled
     *  b. Cookie enabled
     *  c. Cookie doesn't set yet
     *
     * @return string
     */
    protected function _toHtml()
    {
        return ($this->helper->isEnabled() && $this->helper->isCookieEnabled() && ! $this->hasCookie()) ?
            parent::_toHtml() : '';
    }
}

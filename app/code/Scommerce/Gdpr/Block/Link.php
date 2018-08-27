<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Block;

/**
 * Class Link
 * @package Scommerce\Gdpr\Block
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * Link constructor.
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
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->helper->isEnabled() && $this->helper->isCookieEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }
}
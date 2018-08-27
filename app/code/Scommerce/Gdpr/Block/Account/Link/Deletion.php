<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Block\Account\Link;

/**
 * Current link in customer account navigation rendering only if module enabled and delete on front setting is enabled
 *
 * Class Deletion
 * @package Scommerce\Gdpr\Block\Account\Link
 */
class Deletion extends \Magento\Framework\View\Element\Html\Link\Current
{
    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Scommerce\Gdpr\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Scommerce\Gdpr\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->helper = $helper;
    }

    /**
     * Render link only if module enabled and delete on front setting is enabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->helper->isEnabled() && $this->helper->isDeletionEnabledOnFrontend() ? parent::_toHtml() : '';
    }
}

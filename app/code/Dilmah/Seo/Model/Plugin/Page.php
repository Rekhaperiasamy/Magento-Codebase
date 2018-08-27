<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Seo\Model\Plugin;

class Page
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Page constructor.
     *
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Plugin for after getting the page.
     *
     * @param \Magento\Cms\Block\Page $subject
     * @param string                  $result
     *
     * @return string mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetPage(
        \Magento\Cms\Block\Page $subject,
        $result
    ) {
        if (!$this->_coreRegistry->registry('current_cms_page')) {
            $this->_coreRegistry->register('current_cms_page', $result);
        }

        return $result;
    }
}

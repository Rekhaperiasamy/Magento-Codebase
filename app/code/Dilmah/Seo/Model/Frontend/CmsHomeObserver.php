<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Seo\Model\Frontend;

use Magento\Framework\Event\Observer as EventObserver;

/**
 * Versions cms page observer for backend area.
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CmsHomeObserver extends Observer implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * CMS page observer for home.
     *
     * @param EventObserver $observer 
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(EventObserver $observer)
    {
        $cmsPage = $this->_coreRegistry->registry('current_cms_page');
        if ($cmsPage) {
            $this->_coreRegistry->register('page_type', \Dilmah\Seo\Block\Code::PAGE_TYPE_HOME);
        }
    }
}

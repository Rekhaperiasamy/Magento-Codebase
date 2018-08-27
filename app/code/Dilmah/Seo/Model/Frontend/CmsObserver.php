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
 * Versions cms page observer for page typing
 * Class CmsObserver.
 */
class CmsObserver extends Observer implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Observer for CMS page types.
     *
     * @param EventObserver $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(EventObserver $observer)
    {
        $cmsPage = $this->_coreRegistry->registry('current_cms_page');
        if ($cmsPage) {
            $canonicalUrl = $cmsPage->getCanonicalUrl();
            $robotText = $cmsPage->getRobotTags();

            if (!empty($canonicalUrl)) {
                parent::addRel('canonical', $canonicalUrl);
            }
            if (!empty($robotText)) {
                $robotText = $this->_robottags->getOptionText($robotText)->getText();
                $this->_layout->setRobots($robotText);
            }
            $this->_coreRegistry->register('page_type', \Dilmah\Seo\Block\Code::PAGE_TYPE_CMS);
        }
    }
}

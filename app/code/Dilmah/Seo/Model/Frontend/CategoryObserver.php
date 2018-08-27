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
class CategoryObserver extends Observer implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Catalog helper holder.
     *
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_categoryHelper;

    /**
     * Construct.
     *
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Dilmah\Seo\Model\Source\Catalog\Robottags $robottags
     * @param \Magento\Framework\View\Page\Config        $layout
     * @param \Magento\Framework\View\Asset\Repository   $assetRepo
     * @param \Magento\Catalog\Helper\Category           $categoryHelper
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Dilmah\Seo\Model\Source\Catalog\Robottags $robottags,
        \Magento\Framework\View\Page\Config $layout,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Catalog\Helper\Category $categoryHelper
    ) {
        parent::__construct($coreRegistry, $robottags, $layout, $assetRepo);
        $this->_categoryHelper = $categoryHelper;
    }

    /**
     * Observer for Category page type.
     *
     * @param EventObserver $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(EventObserver $observer)
    {
        $category = $this->_coreRegistry->registry('current_category');
        if ($category) {
            $canonicalUrl = $category->getCanonicalUrl();
            $robotText = $category->getRobotTags();

            if (!empty($canonicalUrl) && $this->_categoryHelper->canUseCanonicalTag()) {
                parent::removeRel($category->getUrl());
                parent::addRel('canonical', $canonicalUrl);
            }

            if (!empty($robotText)) {
                $robotText = $this->_robottags->getOptionText($robotText)->getText();
                $this->_layout->setRobots($robotText);
            }

            $this->_coreRegistry->register('page_type', \Dilmah\Seo\Block\Code::PAGE_TYPE_CATEGORY);
        }
    }
}

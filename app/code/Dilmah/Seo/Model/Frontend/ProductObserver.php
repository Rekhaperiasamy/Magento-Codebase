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
class ProductObserver extends Observer implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Catalog Helper.
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_productHelper;

    /**
     * Construct.
     *
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Dilmah\Seo\Model\Source\Catalog\Robottags $robottags
     * @param \Magento\Framework\View\Page\Config        $layout
     * @param \Magento\Framework\View\Asset\Repository   $assetRepo
     * @param \Magento\Catalog\Helper\Product            $productHelper
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Dilmah\Seo\Model\Source\Catalog\Robottags $robottags,
        \Magento\Framework\View\Page\Config $layout,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Catalog\Helper\Product $productHelper
    ) {
        parent::__construct($coreRegistry, $robottags, $layout, $assetRepo);
        $this->_productHelper = $productHelper;
    }

    /**
     * Observer execution.
     *
     * @param EventObserver $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(EventObserver $observer)
    {
        $product = $this->_coreRegistry->registry('product');

        if ($product) {
            $canonicalUrl = $product->getCanonicalUrl();
            $robotText = $product->getRobotTags();

            if (!empty($canonicalUrl) && $this->_productHelper->canUseCanonicalTag()) {
                parent::removeRel($product->getUrlModel()->getUrl($product, ['_ignore_category' => true]));
                parent::addRel('canonical', $canonicalUrl);
            }

            if (!empty($robotText)) {
                $robotText = $this->_robottags->getOptionText($robotText)->getText();
                $this->_layout->setRobots($robotText);
            }

            $this->_coreRegistry->register('page_type', \Dilmah\Seo\Block\Code::PAGE_TYPE_PRODUCT);
            $this->_coreRegistry->register('product_id', $product->getID());
        }
    }
}

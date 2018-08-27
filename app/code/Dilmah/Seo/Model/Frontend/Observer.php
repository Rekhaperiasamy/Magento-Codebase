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

/**
 * Versions cms page observer for backend area.
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Observer
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Robotlegs holder.
     *
     * @var \Dilmah\Seo\Model\Source\Catalog\Robottags
     */
    protected $_robottags;

    /**
     * Config holder.
     *
     * @var \Magento\Framework\View\Page\Config
     */
    protected $_layout;

    /**
     * Asset service.
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * Construct.
     *
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Dilmah\Seo\Model\Source\Catalog\Robottags $robottags
     * @param \Magento\Framework\View\Page\Config        $layout
     * @param \Magento\Framework\View\Asset\Repository   $assetRepo
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Dilmah\Seo\Model\Source\Catalog\Robottags $robottags,
        \Magento\Framework\View\Page\Config $layout,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_robottags = $robottags;
        $this->_layout = $layout;
        $this->assetRepo = $assetRepo;
    }

    /**
     * Add link relations.
     *
     * @param string $title
     * @param string $href
     * @return void
     */
    protected function addRel($title, $href)
    {
        $remoteAsset = $this->assetRepo->createRemoteAsset((string) $href, 'unknown');
        $this->_layout->getAssetCollection()->add(
            "link/{$href}",
            $remoteAsset,
            ['attributes' => 'rel="'.$title.'"']
        );
    }

    /**
     * Remove Reference.
     *
     * @param string $identifier
     * @return void
     */
    protected function removeRel($identifier)
    {
        $this->_layout->getAssetCollection()->remove($identifier);
    }
}

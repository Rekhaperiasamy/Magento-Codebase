<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Afeature
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Afeature\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;

/**
 * Class Banner.
 */
class Banner extends Template implements BlockInterface
{
    /**
     * @var \Dilmah\Afeature\Model\ResourceModel\Item\CollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var base media URL
     */
    protected $mediaUrl;

    /**
     * Banner constructor.
     *
     * @param Context                                                     $context
     * @param \Dilmah\Afeature\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory
     */
    public function __construct(
        Context $context,
        \Dilmah\Afeature\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory
    ) {
        $this->storeManager = $context->getStoreManager();
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->mediaUrl = $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        parent::__construct($context);
    }

    /**
     * return active afeature banners assigned to current store.
     *
     * @return \Dilmah\Afeature\Model\ResourceModel\Item\Collection
     */
    public function getItems()
    {
        /** @var \Dilmah\Afeature\Model\ResourceModel\Item\Collection $items */
        $items = $this->itemCollectionFactory->create();
        $items->getSelect()->joinLeft(
            ['as' => 'dilmah_afeature_store'],
            'as.item_id = main_table.item_id',
            []
        );

        $items->addFieldToFilter('is_active', 1);
        $items->addFieldToFilter(['as.store_id', 'as.store_id'], ['0', $this->storeManager->getStore()->getStoreId()]);
        $items->addOrder('sort_order', 'asc');

        return $items;
    }

    /**
     * get desktop banner url (Retina).
     *
     * @param \Dilmah\Afeature\Model\ResourceModel\Item  $banner
     *
     * @return string
     */
    public function getRetinaImageUrl($banner)
    {
        $image = $banner->getDesktopImageUrl();
        $url = '';
        if ($image) {
            $url = $this->mediaUrl.$image;
        }

        return $url;
    }

    /**
     * get desktop banner url (NON-Retina).
     *
     * @param \Dilmah\Afeature\Model\ResourceModel\Item  $banner
     *
     * @return string
     */
    public function getDesktopImageUrl($banner)
    {
        $url = $this->getRetinaImageUrl($banner);

        return str_replace(
            \Dilmah\Afeature\Model\Item::DESKTOP_BANNER_DIRECTORY,
            \Dilmah\Afeature\Model\Item::DESKTOP_SMALL_BANNER_DIRECTORY,
            $url
        );
    }

    /**
     * get tablet banner url.
     *
     * @param \Dilmah\Afeature\Model\ResourceModel\Item $banner
     *
     * @return string
     */
    public function getTabletImageUrl($banner)
    {
        $image = $banner->getTabletImageUrl();
        $url = '';
        if ($image) {
            $url = $this->mediaUrl.$image;
        }

        return $url;
    }

    /**
     * get mobile banner url.
     *
     * @param \Dilmah\Afeature\Model\ResourceModel\Item $banner
     *
     * @return string
     */
    public function getMobileImageUrl($banner)
    {
        $image = $banner->getMobileImageUrl();
        $url = '';
        if ($image) {
            $url = $this->mediaUrl.$image;
        }

        return $url;
    }
}

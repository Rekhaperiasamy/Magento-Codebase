<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Catalog
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Catalog\Helper;

use Magento\Catalog\Model\Category as ModelCategory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\Store;

/**
 * Class Category.
 */
class Category extends AbstractHelper
{
    /**
     * Mobile banner path.
     */
    const MOBILE_BANNER_PATH = 'mobile';

    /**
     * category root id.
     */
    const XML_PATH_CATEGORY_ROOT_ID = 'catalog/category/root_id';

    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Category constructor.
     *
     * @param \Magento\Framework\App\Helper\Context      $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Retrieve mobile image URL.
     *
     * @param ModelCategory $category
     *
     * @return bool|string
     */
    public function getMobileImageUrl($category)
    {
        $url = false;
        if ($category instanceof ModelCategory) {
            $image = $category->getCategoryMobileBanner();
            if ($image) {
                $url = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ).'catalog/category/'.$image;
            }
        }

        return $url;
    }
}

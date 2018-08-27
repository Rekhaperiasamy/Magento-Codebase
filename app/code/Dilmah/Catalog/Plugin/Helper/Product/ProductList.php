<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Catalog
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Catalog\Plugin\Helper\Product;

use Dilmah\Catalog\Plugin\Block\Product\ProductList\Toolbar;

/**
 * Class ProductList.
 */
class ProductList
{
    /**
     * @param \Magento\Catalog\Helper\Product\ProductList $subject
     *
     * @return string
     * @SuppressWarnings("unused")
     */
    public function aroundGetDefaultSortField(\Magento\Catalog\Helper\Product\ProductList $subject)
    {
        return Toolbar::TOP_RATED_LISTPAGE_SORTER;
    }
}

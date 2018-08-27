<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Catalog
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Catalog\Plugin\Model;

use Dilmah\Catalog\Plugin\Block\Product\ProductList\Toolbar;

/**
 * Class Config.
 */
class Config
{
    /**
     * Retrieve Attributes Used for Sort by as array
     * key = code, value = name.
     *
     * @param \Magento\Catalog\Model\Config $subject
     * @param array                         $result
     *
     * @return mixed
     * @SuppressWarnings("unused")
     */
    public function afterGetAttributeUsedForSortByArray(
        \Magento\Catalog\Model\Config $subject,
        $result
    ) {
        if (!empty($result['position'])) {
            unset($result['position']);
        }

        $result[Toolbar::TOP_RATED_LISTPAGE_SORTER] = __('Top Rated');

        return $result;
    }
}

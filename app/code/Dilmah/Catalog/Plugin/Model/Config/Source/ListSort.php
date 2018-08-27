<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Catalog
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Catalog\Plugin\Model\Config\Source;

use Dilmah\Catalog\Plugin\Block\Product\ProductList\Toolbar;

/**
 * Class ListSort.
 */
class ListSort
{
    /**
     * @param \Magento\Catalog\Model\Config\Source\ListSort $subject
     * @param array                                         $result
     *
     * @return mixed
     * @SuppressWarnings("unused")
     */
    public function afterToOptionArray(
        \Magento\Catalog\Model\Config\Source\ListSort $subject,
        $result
    ) {
        if (!isset($result[Toolbar::TOP_RATED_LISTPAGE_SORTER])) {
            $result[Toolbar::TOP_RATED_LISTPAGE_SORTER] = __('Top Rated');
        }

        return $result;
    }
}

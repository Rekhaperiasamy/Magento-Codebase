<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Orange\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons\Rewrite;

/**
 * Coupon codes grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Grid extends \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons\Grid
{
    /**
     * Define grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->addColumn(
            'order_ids',
            ['header' => __('Order Ids'), 'index' => 'order_ids', 'width' => '50', 'type' => 'text']
        );
    }
}

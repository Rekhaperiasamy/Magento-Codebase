<?php


namespace Orange\Coupon\Model\ResourceModel\QuoteCoupon;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Orange\Coupon\Model\QuoteCoupon',
            'Orange\Coupon\Model\ResourceModel\QuoteCoupon'
        );
    }
}

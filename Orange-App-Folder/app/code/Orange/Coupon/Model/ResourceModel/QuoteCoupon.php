<?php


namespace Orange\Coupon\Model\ResourceModel;

class QuoteCoupon extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('orange_quote_coupon', 'quote_coupon_id');
    }
}

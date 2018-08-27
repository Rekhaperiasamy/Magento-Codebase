<?php
/**
 *
 */

namespace Orange\CouponReport\Model\Resource\Coupons;

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
            'Orange\CouponReport\Model\Coupons',
            'Orange\CouponReport\Model\Resource\Coupons' 
        );

    }
}

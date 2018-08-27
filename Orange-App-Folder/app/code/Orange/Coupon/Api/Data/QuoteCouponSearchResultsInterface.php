<?php


namespace Orange\Coupon\Api\Data;

interface QuoteCouponSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get quote_coupon list.
     * @return \Orange\Coupon\Api\Data\QuoteCouponInterface[]
     */
    
    public function getItems();

    /**
     * Set quote_coupon_id list.
     * @param \Orange\Coupon\Api\Data\QuoteCouponInterface[] $items
     * @return $this
     */
    
    public function setItems(array $items);
}

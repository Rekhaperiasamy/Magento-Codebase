<?php


namespace Orange\Coupon\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface QuoteCouponRepositoryInterface
{


    /**
     * Save quote_coupon
     * @param \Orange\Coupon\Api\Data\QuoteCouponInterface $quoteCoupon
     * @return \Orange\Coupon\Api\Data\QuoteCouponInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function save(
        \Orange\Coupon\Api\Data\QuoteCouponInterface $quoteCoupon
    );

    /**
     * Retrieve quote_coupon
     * @param string $quoteCouponId
     * @return \Orange\Coupon\Api\Data\QuoteCouponInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getById($quoteCouponId);

    /**
     * Retrieve quote_coupon matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Orange\Coupon\Api\Data\QuoteCouponSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete quote_coupon
     * @param \Orange\Coupon\Api\Data\QuoteCouponInterface $quoteCoupon
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function delete(
        \Orange\Coupon\Api\Data\QuoteCouponInterface $quoteCoupon
    );

    /**
     * Delete quote_coupon by ID
     * @param string $quoteCouponId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function deleteById($quoteCouponId);
}

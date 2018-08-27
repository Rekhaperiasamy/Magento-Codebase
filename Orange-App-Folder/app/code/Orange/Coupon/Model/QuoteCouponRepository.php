<?php


namespace Orange\Coupon\Model;

use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\DataObjectHelper;
use Orange\Coupon\Model\ResourceModel\QuoteCoupon\CollectionFactory as QuoteCouponCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SortOrder;
use Orange\Coupon\Api\QuoteCouponRepositoryInterface;
use Orange\Coupon\Model\ResourceModel\QuoteCoupon as ResourceQuoteCoupon;
use Magento\Framework\Exception\CouldNotSaveException;
use Orange\Coupon\Api\Data\QuoteCouponSearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Orange\Coupon\Api\Data\QuoteCouponInterfaceFactory;

class QuoteCouponRepository 
{

    private $storeManager;

    protected $dataQuoteCouponFactory;

    protected $quote_couponCollectionFactory;

    protected $dataObjectProcessor;

    protected $resource;

    protected $dataObjectHelper;

    protected $quote_couponFactory;

    protected $searchResultsFactory;


    /**
     * @param ResourceQuoteCoupon $resource
     * @param QuoteCouponFactory $quoteCouponFactory
     * @param QuoteCouponInterfaceFactory $dataQuoteCouponFactory
     * @param QuoteCouponCollectionFactory $quoteCouponCollectionFactory
     * @param QuoteCouponSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceQuoteCoupon $resource,
        QuoteCouponFactory $quoteCouponFactory,
        QuoteCouponInterfaceFactory $dataQuoteCouponFactory,
        QuoteCouponCollectionFactory $quoteCouponCollectionFactory,
        QuoteCouponSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->quoteCouponFactory = $quoteCouponFactory;
        $this->quoteCouponCollectionFactory = $quoteCouponCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataQuoteCouponFactory = $dataQuoteCouponFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Orange\Coupon\Api\Data\QuoteCouponInterface $quoteCoupon
    ) {
        /* if (empty($quoteCoupon->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $quoteCoupon->setStoreId($storeId);
        } */
        try {
            $this->resource->save($quoteCoupon);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the quoteCoupon: %1',
                $exception->getMessage()
            ));
        }
        return $quoteCoupon;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($quoteCouponId)
    {
        $quoteCoupon = $this->quoteCouponFactory->create();
        $quoteCoupon->load($quoteCouponId);
        if (!$quoteCoupon->getId()) {
            throw new NoSuchEntityException(__('quote_coupon with id "%1" does not exist.', $quoteCouponId));
        }
        return $quoteCoupon;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $collection = $this->quoteCouponCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $items = [];
        
        foreach ($collection as $quoteCouponModel) {
            $quoteCouponData = $this->dataQuoteCouponFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $quoteCouponData,
                $quoteCouponModel->getData(),
                'Orange\Coupon\Api\Data\QuoteCouponInterface'
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $quoteCouponData,
                'Orange\Coupon\Api\Data\QuoteCouponInterface'
            );
        }
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Orange\Coupon\Api\Data\QuoteCouponInterface $quoteCoupon
    ) {
        try {
            $this->resource->delete($quoteCoupon);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the quote_coupon: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($quoteCouponId)
    {
        return $this->delete($this->getById($quoteCouponId));
    }
}

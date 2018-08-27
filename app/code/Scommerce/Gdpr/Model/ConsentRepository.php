<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;
use Scommerce\Gdpr\Api\Data\ConsentInterface;
use Scommerce\Gdpr\Model\ResourceModel\Consent\Collection;

/**
 * Class ConsentRepository
 * @package Scommerce\Gdpr\Model
 */
class ConsentRepository implements \Scommerce\Gdpr\Api\ConsentRepositoryInterface
{
    /** @var \Magento\Framework\Api\DataObjectHelper */
    private $dataObjectHelper;

    /** @var \Scommerce\Gdpr\Model\ResourceModel\Consent */
    private $resource;

    /** @var \Scommerce\Gdpr\Model\ConsentFactory */
    private $consentFactory;

    /** @var \Scommerce\Gdpr\Api\Data\ConsentSearchResultInterfaceFactory */
    private $searchResultsFactory;

    /** @var \Scommerce\Gdpr\Model\ResourceModel\Consent\CollectionFactory */
    private $consentCollectionFactory;

    /**
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Scommerce\Gdpr\Model\ResourceModel\Consent $resource
     * @param \Scommerce\Gdpr\Model\ConsentFactory $consentFactory
     * @param \Scommerce\Gdpr\Api\Data\ConsentSearchResultInterfaceFactory $searchResultsFactory
     * @param \Scommerce\Gdpr\Model\ResourceModel\Consent\CollectionFactory $consentCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Scommerce\Gdpr\Model\ResourceModel\Consent $resource,
        \Scommerce\Gdpr\Model\ConsentFactory $consentFactory,
        \Scommerce\Gdpr\Api\Data\ConsentSearchResultInterfaceFactory $searchResultsFactory,
        \Scommerce\Gdpr\Model\ResourceModel\Consent\CollectionFactory $consentCollectionFactory
    ) {
        $this->dataObjectHelper         = $dataObjectHelper;
        $this->resource                 = $resource;
        $this->consentFactory           = $consentFactory;
        $this->searchResultsFactory     = $searchResultsFactory;
        $this->consentCollectionFactory = $consentCollectionFactory;
    }

    /**
     * @inheritdoc
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save(ConsentInterface $model)
    {
        try {
            /** @var ConsentInterface|\Magento\Framework\Model\AbstractModel $model */
            $this->resource->save($model);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save the consent: %1', $e->getMessage()));
        }
        return $this->get($model->getId());
    }

    /**
     * @inheritdoc
     * @throws NoSuchEntityException
     */
    public function get($id)
    {
        return $this->_load($id);
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->consentCollectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $group) {
            foreach ($group->getFilters() as $filter) {
                $collection->addFieldToFilter(
                    'main_table.' . $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
            }
        }
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            /** @var SortOrder $sortOrder */
            $field = $sortOrder->getField();
            $order = $sortOrder->getDirection() == SortOrder::SORT_ASC ? SortOrder::SORT_ASC : SortOrder::SORT_DESC;
            $collection->addOrder($field, $order);
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();

        /** @var \Scommerce\Gdpr\Api\Data\ConsentSearchResultInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * @inheritdoc
     * @throws StateException
     */
    public function delete(ConsentInterface $model)
    {
        /** @var ConsentInterface|\Magento\Framework\Model\AbstractModel $model */
        try {
            $this->resource->delete($model);
        } catch (\Exception $e) {
            throw new StateException(__('Unable to remove choice: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * @inheritdoc
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function deleteById($id)
    {
        return $this->delete($this->get($id));
    }

    /**
     * Helper method for loading model via one of the field
     *
     * @param mixed $value
     * @param string|null $field
     * @return ConsentInterface|\Magento\Framework\Model\AbstractModel
     * @throws NoSuchEntityException
     */
    private function _load($value, $field = null)
    {
        $model = $this->consentFactory->create();
        $this->resource->load($model, $value, $field);
        if (! $model->getId()) {
            throw new NoSuchEntityException(__('Requested consent doesn\'t exist'));
        }
        return $model;
    }
}

<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model;

use Colissimo\Rule\Api\Data;
use Colissimo\Rule\Api\Data\RuleInterfaceFactory;
use Colissimo\Rule\Api\RuleRepositoryInterface;
use Colissimo\Rule\Model\ResourceModel\Rule as RuleResource;
use Colissimo\Rule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class RuleRepository
 */
class RuleRepository implements RuleRepositoryInterface
{

    /**
     * @var RuleResource $ruleResource
     */
    protected $ruleResource;

    /**
     * @var RuleFactory $ruleFactory
     */
    protected $ruleFactory;

    /**
     * @var RuleInterfaceFactory $dataRuleFactory
     */
    protected $dataRuleFactory;

    /**
     * @var RuleCollectionFactory $ruleCollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var Data\RuleSearchResultsInterfaceFactory $searchResultsFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper $dataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor $dataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * RuleRepository constructor.
     *
     * @param RuleResource $ruleResource
     * @param RuleFactory $ruleFactory
     * @param RuleInterfaceFactory $dataRuleFactory
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param Data\RuleSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        RuleResource $ruleResource,
        RuleFactory $ruleFactory,
        RuleInterfaceFactory $dataRuleFactory,
        RuleCollectionFactory $ruleCollectionFactory,
        Data\RuleSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->ruleResource          = $ruleResource;
        $this->ruleFactory           = $ruleFactory;
        $this->dataRuleFactory       = $dataRuleFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->searchResultsFactory  = $searchResultsFactory;
        $this->dataObjectHelper      = $dataObjectHelper;
        $this->dataObjectProcessor   = $dataObjectProcessor;
        $this->storeManager          = $storeManager;
    }

    /**
     * Save rule
     *
     * @param \Colissimo\Rule\Api\Data\RuleInterface $entity
     *
     * @return \Colissimo\Rule\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\RuleInterface $entity)
    {
        try {
            $this->ruleResource->save($entity);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $entity;
    }

    /**
     * Retrieve by id
     *
     * @param int $entityId
     *
     * @return \Colissimo\Rule\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($entityId)
    {
        $rule = $this->ruleFactory->create();
        $this->ruleResource->load($rule, $entityId);
        if (!$rule->getId()) {
            throw new NoSuchEntityException(__('Rule with id "%1" does not exist.', $entityId));
        }

        return $rule;
    }

    /**
     * Retrieve list
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return \Colissimo\Rule\Api\Data\RuleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->ruleCollectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue());
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $rule = [];
        /** @var Rule $ruleModel */
        foreach ($collection as $ruleModel) {
            $rule[] = $this->ruleFactory->create(['data' => $ruleModel->getData()]);
        }
        $searchResults->setItems($rule);

        return $searchResults;
    }

    /**
     * Delete rule
     *
     * @param \Colissimo\Rule\Api\Data\RuleInterface $rule
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\RuleInterface $rule)
    {
        try {
            $this->ruleResource->delete($rule);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * Delete rule by ID.
     *
     * @param int $ruleId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($ruleId)
    {
        /** @var \Colissimo\Rule\Api\Data\ruleInterface $rule */
        $rule = $this->getById($ruleId);

        return $this->delete($rule);
    }
}
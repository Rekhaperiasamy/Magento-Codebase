<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model\ResourceModel\Rule;

use Colissimo\Rule\Api\Data\RuleInterface;
use Colissimo\Rule\Model\Rule\AssociatedEntityMap;
use Magento\Quote\Model\Quote\Address;
use Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Select;
use Magento\Store\Model\Website;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{

    /**
     * @var TimezoneInterface $_date
     */
    protected $_date;

    /**
     * @param EntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param TimezoneInterface $date
     * @param AssociatedEntityMap $associatedEntitiesMap
     * @param AdapterInterface $connection
     * @param AbstractDb $resource
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        TimezoneInterface $date,
        AssociatedEntityMap $associatedEntitiesMap,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_date = $date;
        $this->_associatedEntitiesMap = $associatedEntitiesMap->getData();
    }

    /**
     * Set resource model and determine field mapping
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Colissimo\Rule\Model\Rule', 'Colissimo\Rule\Model\ResourceModel\Rule');
        $this->_map['fields']['rule_id'] = 'main_table.rule_id';
    }

    /**
     * Add websites for load
     *
     * @return $this
     */
    public function _initSelect()
    {
        parent::_initSelect();

        return $this;
    }

    /**
     * @param string $entityType
     * @param string $objectField
     * @throws LocalizedException
     * @return void
     */
    protected function mapAssociatedEntities($entityType, $objectField)
    {
        if (!$this->_items) {
            return;
        }

        $entityInfo = $this->_getAssociatedEntityInfo($entityType);
        $ruleIdField = $entityInfo['rule_id_field'];
        $entityIds = $this->getColumnValues($ruleIdField);

        $select = $this->getConnection()->select()->from(
            $this->getTable($entityInfo['associations_table'])
        )->where(
            $ruleIdField . ' IN (?)',
            $entityIds
        );

        $associatedEntities = $this->getConnection()->fetchAll($select);

        array_map(function ($associatedEntity) use ($entityInfo, $ruleIdField, $objectField) {
            $item = $this->getItemByColumnValue($ruleIdField, $associatedEntity[$ruleIdField]);
            $itemAssociatedValue = $item->getData($objectField) === null ? [] : $item->getData($objectField);
            $itemAssociatedValue[] = $associatedEntity[$entityInfo['entity_id_field']];
            $item->setData($objectField, $itemAssociatedValue);
        }, $associatedEntities);
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        $this->mapAssociatedEntities('website', 'website_ids');
        $this->mapAssociatedEntities('customer_group', 'customer_group_ids');

        $this->setFlag('add_websites_to_result', false);
        return parent::_afterLoad();
    }

    /**
     * Filter collection by specified website, customer group, coupon code, date.
     * Filter collection to use only active rules.
     * Involved sorting by sort_order column.
     *
     * @param int $websiteId
     * @param int $customerGroupId
     * @param string $shippingMethod
     * @param string|null $now
     * @use $this->addWebsiteGroupMethodDateFilter()
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return $this
     */
    public function setValidationFilter(
        $websiteId,
        $customerGroupId,
        $shippingMethod,
        $now = null
    ) {
        if (!$this->getFlag('validation_filter')) {
            /* We need to overwrite joinLeft if coupon is applied */
            $this->getSelect()->reset();
            parent::_initSelect();

            $this->addWebsiteGroupMethodDateFilter($websiteId, $customerGroupId, $shippingMethod, $now);

            $this->setOrder('sort_order', self::SORT_ORDER_ASC);
            $this->setFlag('validation_filter', true);
        }

        return $this;
    }

    /**
     * Filter collection by website(s), customer group(s) and date.
     * Filter collection to only active rules.
     * Sorting is not involved
     *
     * @param int $websiteId
     * @param int $customerGroupId
     * @param string $shippingMethod
     * @param string|null $now
     * @use $this->addWebsiteFilter()
     * @return $this
     */
    public function addWebsiteGroupMethodDateFilter($websiteId, $customerGroupId, $shippingMethod, $now = null)
    {
        if (!$this->getFlag('website_group_method_date_filter')) {
            if ($now === null) {
                $now = $this->_date->date()->format('Y-m-d');
            }

            $this->addWebsiteFilter($websiteId);
            $this->addCustomerGroupFilter($customerGroupId);
            $this->addDateFilter($now);
            $this->addShippingMethodFilter($shippingMethod);
            $this->addIsActiveFilter();

            $this->setFlag('website_group_method_date_filter', true);
        }

        return $this;
    }

    /**
     * Find product attribute in conditions or actions
     *
     * @param string $attributeCode
     * @return $this
     */
    public function addAttributeInConditionFilter($attributeCode)
    {
        $match = sprintf('%%%s%%', substr(serialize(['attribute' => $attributeCode]), 5, -1));
        $field = $this->_getMappedField('conditions_serialized');
        $cCond = $this->_getConditionSql($field, ['like' => $match]);

        $this->getSelect()->where(
            sprintf('(%s)', $cCond),
            null,
            Select::TYPE_CONDITION
        );

        return $this;
    }

    /**
     * Limit rules collection by specific websites
     *
     * @param int|int[]|Website $websiteId
     * @return $this
     */
    public function addWebsiteFilter($websiteId)
    {
        $websiteIds = is_array($websiteId) ? $websiteId : [$websiteId];
        $entityInfo = $this->_getAssociatedEntityInfo('website');

        foreach ($websiteIds as $index => $website) {
            if ($website instanceof Website) {
                $websiteIds[$index] = $website->getId();
            }
        }
        $this->getSelect()->join(
            ['website' => $this->getTable($entityInfo['associations_table'])],
            $this->getConnection()->quoteInto('website.' . $entityInfo['entity_id_field'] . ' IN (?)', $websiteIds)
            . ' AND main_table.' . $entityInfo['rule_id_field'] . ' = website.' . $entityInfo['rule_id_field'],
            []
        );

        return $this;
    }

    /**
     * Limit rules collection by specific customer group
     *
     * @param int $customerGroupId
     * @return $this
     */
    public function addCustomerGroupFilter($customerGroupId)
    {
        $entityInfo = $this->_getAssociatedEntityInfo('customer_group');
        $this->getSelect()->join(
            ['customer_group' => $this->getTable($entityInfo['associations_table'])],
            $this->getConnection()
                ->quoteInto('customer_group.' . $entityInfo['entity_id_field'] . ' = ?', $customerGroupId)
            . ' AND main_table.' . $entityInfo['rule_id_field'] . ' = customer_group.'
            . $entityInfo['rule_id_field'],
            []
        );

        return $this;
    }

    /**
     * Limit rule to date filter
     *
     * @param string $date
     * @return $this
     */
    public function addDateFilter($date)
    {
        $this->getSelect()
            ->where(RuleInterface::KEY_FROM_DATE . ' IS NULL OR ' . RuleInterface::KEY_FROM_DATE . ' <= ?', $date)
            ->where(RuleInterface::KEY_TO_DATE . ' IS NULL OR ' . RuleInterface::KEY_TO_DATE . ' >= ?', $date);

        return $this;
    }

    /**
     * Filter collection to only active or inactive rules
     *
     * @param int $isActive
     * @return $this
     */
    public function addIsActiveFilter($isActive = 1)
    {
        $this->getSelect()->where(RuleInterface::KEY_IS_ACTIVE, (int)$isActive ? 1 : 0);

        return $this;
    }

    /**
     * Limit rule to shipping method
     *
     * @param string $shippingMethod
     * @return $this
     */
    public function addShippingMethodFilter($shippingMethod)
    {
        $this->getSelect()->where(RuleInterface::KEY_SHIPPING_METHOD . ' = ?', $shippingMethod);

        return $this;
    }
}

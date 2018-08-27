<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model\ResourceModel;

use Colissimo\Rule\Model\Rule as ColissimoRule;
use Colissimo\Rule\Api\Data\RuleInterface;
use Colissimo\Rule\Model\Rule\AssociatedEntityMap;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DB\Select;
use Magento\Rule\Model\ResourceModel\AbstractResource;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\App\ObjectManager;

/**
 * Class Rule
 */
class Rule extends AbstractResource
{

    /**
     * @var array $customerGroupIds
     */
    protected $customerGroupIds = [];

    /**
     * @var array $websiteIds
     */
    protected $websiteIds = [];

    /**
     * @var EntityManager $entityManager
     */
    protected $entityManager;

    /**
     * @param Context $context
     * @param AssociatedEntityMap $associatedEntitiesMap
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        AssociatedEntityMap $associatedEntitiesMap,
        $connectionName = null
    ) {
        $this->_associatedEntitiesMap = $associatedEntitiesMap->getData();
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('colissimo_rule', 'rule_id');
    }

    /**
     * @param AbstractModel $object
     * @return void
     */
    public function loadCustomerGroupIds(AbstractModel $object)
    {
        if (!$this->customerGroupIds) {
            $this->customerGroupIds = (array)$this->getCustomerGroupIds($object->getId());
        }
        $object->setData(RuleInterface::KEY_CUSTOMER_GROUPS, $this->customerGroupIds);
    }

    /**
     * @param AbstractModel $object
     * @return void
     */
    public function loadWebsiteIds(AbstractModel $object)
    {
        if (!$this->websiteIds) {
            $this->websiteIds = (array)$this->getWebsiteIds($object->getId());
        }

        $object->setData(RuleInterface::KEY_WEBSITES, $this->websiteIds);
    }

    /**
     * Save customer groups ids
     *
     * @param ColissimoRule|AbstractModel $object
     * @return $this
     */
    public function saveCustomerGroupIds(AbstractModel $object)
    {
        $this->unbindRuleFromEntity(
            $object->getId(),
            $this->getCustomerGroupIds($object->getId()),
            'customer_group'
        );
        $this->_multiplyBunchInsert(
            $object->getId(),
            $object->getCustomerGroupIds(),
            'customer_group'
        );

        return $this;
    }

    /**
     * Save website ids
     *
     * @param ColissimoRule|AbstractModel $object
     * @return $this
     */
    public function saveWebsiteIds(AbstractModel $object)
    {
        $this->unbindRuleFromEntity(
            $object->getId(),
            $this->getWebsiteIds($object->getId()),
            'website'
        );
        $this->_multiplyBunchInsert(
            $object->getId(),
            $object->getWebsiteIds(),
            'website'
        );

        return $this;
    }

    /**
     * Load an object
     *
     * @param ColissimoRule|AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        parent::load($object, $value, $field);

        $this->loadCustomerGroupIds($object);
        $this->loadWebsiteIds($object);

        return $this;
    }

    /**
     * Save object object data
     *
     * @param ColissimoRule|AbstractModel $object
     * @return $this
     */
    public function save(AbstractModel $object)
    {
        parent::save($object);

        $this->saveCustomerGroupIds($object);
        $this->saveWebsiteIds($object);

        return $this;
    }
}

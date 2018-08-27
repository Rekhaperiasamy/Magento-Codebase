<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model;

use Colissimo\Rule\Api\Data\RuleInterface;
use Colissimo\Rule\Api\Data\ConditionInterface;
use Colissimo\Rule\Model\Rule\Condition\CombineFactory;
use Colissimo\Rule\Model\Rule\Condition\Product\CombineFactory as ProductCombineFactory;
use Magento\Rule\Model\AbstractModel;
use Magento\Quote\Model\Quote\Address;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Rule
 */
class Rule extends AbstractModel implements RuleInterface
{

    const RULE_SHIPPING_CODE = 'colissimo';

    /**
     * @var string $_eventPrefix
     */
    protected $_eventPrefix = 'colissimo_rule';

    /**
     * @var string $_eventObject
     */
    protected $_eventObject = 'rule';

    /**
     * @var array $_validatedAddresses
     */
    protected $_validatedAddresses = [];

    /**
     * @var CombineFactory $_condCombineFactory
     */
    protected $_condCombineFactory;

    /**
     * @var ProductCombineFactory $_condProdCombineF
     */
    protected $_condProdCombineF;

    /**
     * @var StoreManagerInterface $_storeManager
     */
    protected $_storeManager;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param CombineFactory $condCombineFactory
     * @param ProductCombineFactory $condProdCombineF
     * @param StoreManagerInterface $storeManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CombineFactory $condCombineFactory,
        ProductCombineFactory $condProdCombineF,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_condCombineFactory = $condCombineFactory;
        $this->_condProdCombineF = $condProdCombineF;
        $this->_storeManager = $storeManager;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Colissimo\Rule\Model\ResourceModel\Rule');
        $this->setIdFieldName('rule_id');
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \Colissimo\Rule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_condCombineFactory->create();
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return \Colissimo\Rule\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance()
    {
        return $this->_condProdCombineF->create();
    }

    /**
     * Check cached validation result for specific address
     *
     * @param Address $address
     * @return bool
     */
    public function hasIsValidForAddress($address)
    {
        $addressId = $this->getDataAddressId($address);
        return isset($this->_validatedAddresses[$addressId]) ? true : false;
    }

    /**
     * Set validation result for specific address to results cache
     *
     * @param Address $address
     * @param bool $validationResult
     * @return $this
     */
    public function setIsValidForAddress($address, $validationResult)
    {
        $addressId = $this->getDataAddressId($address);
        $this->_validatedAddresses[$addressId] = $validationResult;
        return $this;
    }

    /**
     * Get cached validation result for specific address
     *
     * @param Address $address
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsValidForAddress($address)
    {
        $addressId = $this->getDataAddressId($address);
        return isset($this->_validatedAddresses[$addressId]) ? $this->_validatedAddresses[$addressId] : false;
    }

    /**
     * Return id for address
     *
     * @param Address $address
     * @return string
     */
    private function getDataAddressId($address)
    {
        if ($address instanceof Address) {
            return $address->getId();
        }
        return $address;
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getActionsFieldSetId($formName = '')
    {
        return $formName . 'rule_actions_fieldset_' . $this->getId();
    }

    /**
     * Return rule id
     *
     * @return int|null
     */
    public function getRuleId()
    {
        return $this->getData(self::KEY_RULE_ID);
    }

    /**
     * Set rule id
     *
     * @param int $ruleId
     * @return $this
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::KEY_RULE_ID, $ruleId);
    }

    /**
     * Get rule name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::KEY_NAME);
    }

    /**
     * Set rule name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(self::KEY_NAME, $name);
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(self::KEY_DESCRIPTION);
    }

    /**
     * Set Shipping Method
     *
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethod($shippingMethod)
    {
        return $this->setData(self::KEY_SHIPPING_METHOD, $shippingMethod);
    }

    /**
     * Get Shipping Method
     *
     * @return string|null
     */
    public function getShippingMethod()
    {
        return $this->getData(self::KEY_SHIPPING_METHOD);
    }

    /**
     * Set Shipping Action
     *
     * @param string $shippingAction
     * @return $this
     */
    public function setShippingAction($shippingAction)
    {
        return $this->setData(self::KEY_SHIPPING_ACTION, $shippingAction);
    }

    /**
     * Get Shipping Action
     *
     * @return string|null
     */
    public function getShippingAction()
    {
        return $this->getData(self::KEY_SHIPPING_ACTION);
    }

    /**
     * Set Shipping Amount
     *
     * @param float $shippingAmount
     * @return $this
     */
    public function setShippingAmount($shippingAmount)
    {
        return $this->setData(self::KEY_SHIPPING_AMOUNT, $shippingAmount);
    }

    /**
     * Get Shipping Amount
     *
     * @return float|null
     */
    public function getShippingAmount()
    {
        return $this->getData(self::KEY_SHIPPING_AMOUNT);
    }

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->setData(self::KEY_DESCRIPTION, $description);
    }

    /**
     * Get the start date when the coupon is active
     *
     * @return string|null
     */
    public function getFromDate()
    {
        return $this->getData(self::KEY_FROM_DATE);
    }

    /**
     * Set the star date when the coupon is active
     *
     * @param string $fromDate
     * @return $this
     */
    public function setFromDate($fromDate)
    {
        return $this->setData(self::KEY_FROM_DATE, $fromDate);
    }

    /**
     * Get the end date when the coupon is active
     *
     * @return string|null
     */
    public function getToDate()
    {
        return $this->getData(self::KEY_TO_DATE);
    }

    /**
     * Set the end date when the coupon is active
     *
     * @param string $toDate
     * @return $this
     */
    public function setToDate($toDate)
    {
        return $this->setData(self::KEY_TO_DATE, $toDate);
    }

    /**
     * Whether the rule is active
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->getData(self::KEY_IS_ACTIVE);
    }

    /**
     * Set whether the coupon is active
     *
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::KEY_IS_ACTIVE, $isActive);
    }

    /**
     * Get a list of websites the rule applies to
     *
     * @return int[]
     */
    public function getWebsiteIds()
    {
        return $this->getData(self::KEY_WEBSITES);
    }

    /**
     * Set the websites the rule applies to
     *
     * @param int[] $websites
     * @return $this
     */
    public function setWebsiteIds(array $websites)
    {
        return $this->setData(self::KEY_WEBSITES, $websites);
    }

    /**
     * Get ids of customer groups that the rule applies to
     *
     * @return int[]
     */
    public function getCustomerGroupIds()
    {
        return $this->getData(self::KEY_CUSTOMER_GROUPS);
    }

    /**
     * Set the customer groups that the rule applies to
     *
     * @param int[] $customerGroups
     * @return $this
     */
    public function setCustomerGroupIds(array $customerGroups)
    {
        return $this->setData(self::KEY_CUSTOMER_GROUPS, $customerGroups);
    }

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->getData(self::KEY_SORT_ORDER);
    }

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::KEY_SORT_ORDER, $sortOrder);
    }
}

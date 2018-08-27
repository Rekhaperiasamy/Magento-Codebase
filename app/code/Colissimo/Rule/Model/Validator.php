<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model;

use Colissimo\Rule\Model\ResourceModel\Rule\CollectionFactory;
use Colissimo\Rule\Model\Utility;
use Colissimo\Rule\Model\Validator\Pool;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Catalog\Helper\Data as CatalogHelperData;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Validator
 */
class Validator extends AbstractModel
{
    /**
     * @var array $_rules
     */
    protected $_rules;

    /**
     * @var bool $_isFirstTimeResetRun
     */
    protected $_isFirstTimeResetRun = true;

    /**
     * @var array $_rulesItemTotals
     */
    protected $_rulesItemTotals = [];

    /**
     * @var bool $_skipActionsValidation
     */
    protected $_skipActionsValidation = false;

    /**
     * @var CatalogHelperData|null $_catalogData
     */
    protected $_catalogData = null;

    /**
     * @var CollectionFactory $_collectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Utility $validatorUtility
     */
    protected $validatorUtility;

    /**
     * @var PriceCurrencyInterface $priceCurrency
     */
    protected $priceCurrency;

    /**
     * @var Pool $validators
     */
    protected $validators;

    /**
     * @var ManagerInterface $messageManager
     */
    protected $messageManager;

    /**
     * @var int $counter
     */
    protected $counter = 0;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param CollectionFactory $collectionFactory
     * @param CatalogHelperData $catalogData
     * @param Utility $utility
     * @param PriceCurrencyInterface $priceCurrency
     * @param Pool $validators
     * @param ManagerInterface $messageManager
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $collectionFactory,
        CatalogHelperData $catalogData,
        Utility $utility,
        PriceCurrencyInterface $priceCurrency,
        Pool $validators,
        ManagerInterface $messageManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_catalogData = $catalogData;
        $this->validatorUtility = $utility;
        $this->priceCurrency = $priceCurrency;
        $this->validators = $validators;
        $this->messageManager = $messageManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init validator
     * Init process load collection of rules for specific website,
     * customer group and coupon code
     *
     * @param int $websiteId
     * @param int $customerGroupId
     * @return $this
     */
    public function init($websiteId, $customerGroupId)
    {
        $this->setWebsiteId($websiteId)->setCustomerGroupId($customerGroupId);

        return $this;
    }

    /**
     * Get rules collection for current object state
     *
     * @param Address|null $address
     * @return \Colissimo\Rule\Model\ResourceModel\Rule\Collection
     */
    protected function _getRules(Address $address = null)
    {
        $addressId = $this->getAddressId($address);
        $key = $this->getWebsiteId() . '_'
            . $this->getCustomerGroupId() . '_'
            . $this->getShippingMethod() . '_'
            . $addressId;
        if (!isset($this->_rules[$key])) {
            $this->_rules[$key] = $this->_collectionFactory->create()
                ->setValidationFilter(
                    $this->getWebsiteId(),
                    $this->getCustomerGroupId(),
                    $this->getShippingMethod(),
                    null
                )
                ->addFieldToFilter('is_active', 1)
                ->load();
        }
        return $this->_rules[$key];
    }

    /**
     * @param Address $address
     * @return string
     */
    protected function getAddressId(Address $address)
    {
        if ($address == null) {
            return '';
        }
        if (!$address->hasData('address_colissimo_rule_id')) {
            if ($address->hasData('address_id')) {
                $address->setData('address_colissimo_rule_id', $address->getData('address_id'));
            } else {
                $type = $address->getAddressType();
                $tempId = $type . $this->counter++;
                $address->setData('address_colissimo_rule_id', $tempId);
            }
        }
        return $address->getData('address_colissimo_rule_id');
    }

    /**
     * Set skip actions validation flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setSkipActionsValidation($flag)
    {
        $this->_skipActionsValidation = $flag;
        return $this;
    }

    /**
     * Can apply rules check
     *
     * @param AbstractItem $item
     * @return bool
     */
    public function canApplyRules(AbstractItem $item)
    {
        $address = $item->getAddress();
        foreach ($this->_getRules($address) as $rule) {
            if (!$this->validatorUtility->canProcessRule($rule, $address) || !$rule->getActions()->validate($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Reset quote and address applied rules
     *
     * @param Address $address
     * @return $this
     */
    public function reset(Address $address)
    {
        $this->validatorUtility->resetRoundingDeltas();
        if ($this->_isFirstTimeResetRun) {
            $address->setAppliedRuleIds('');
            $address->getQuote()->setAppliedRuleIds('');
            $this->_isFirstTimeResetRun = false;
        }

        return $this;
    }

    /**
     * Return items list sorted by possibility to apply prioritized rules
     *
     * @param array $items
     * @param Address $address
     * @return array $items
     */
    public function sortItemsByPriority($items, Address $address = null)
    {
        $itemsSorted = [];
        /** @var $rule \Colissimo\Rule\Model\Rule */
        foreach ($this->_getRules($address) as $rule) {
            foreach ($items as $itemKey => $itemValue) {
                if ($rule->getActions()->validate($itemValue)) {
                    unset($items[$itemKey]);
                    array_push($itemsSorted, $itemValue);
                }
            }
        }

        if (!empty($itemsSorted)) {
            $items = array_merge($itemsSorted, $items);
        }

        return $items;
    }

    /**
     * Check if we can apply discount to current QuoteItem
     *
     * @param AbstractItem $item
     * @return bool
     */
    public function canApplyDiscount(AbstractItem $item)
    {
        $result = true;
        /** @var \Zend_Validate_Interface $validator */
        foreach ($this->validators->getValidators('discount') as $validator) {
            $result = $validator->isValid($item);
            if (!$result) {
                break;
            }
        }
        return $result;
    }
}

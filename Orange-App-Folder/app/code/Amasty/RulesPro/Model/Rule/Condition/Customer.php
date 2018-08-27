<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */

namespace Amasty\RulesPro\Model\Rule\Condition;

use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Rule\Model\Condition as Condition;
use Magento\Customer\Model\Address;
/**
 * Product rule condition data model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Customer extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerHelper;

    public function __construct(
        Condition\Context $context,
        AppResource $resource,
        \Magento\Customer\Helper\View $customerHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->resource = $resource;
        $this->_customerHelper = $customerHelper;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    public function loadAttributeOptions()
    {
        $customerAttributes = $this->_objectManager->get('Magento\Customer\Model\ResourceModel\Customer')
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = array();

        foreach ($customerAttributes as $attribute) {
            if (!($attribute->getFrontendLabel()) || !($attribute->getAttributeCode())) {
                continue;
            }

            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }
        $this->_addSpecialAttributes($attributes);
        asort($attributes);
        $this->setAttributeOption($attributes);
        return $this;
    }

    protected function _addSpecialAttributes(array &$attributes)
    {
        //$attributes['id'] = __('ID');
        $attributes['membership_days'] = __('Membership Days');
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function getInputType()
    {
        $customerAttribute = $this->_objectManager->get('Magento\Customer\Model\ResourceModel\Customer')->getAttribute($this->getAttribute());

        if ($this->getAttribute() ==  'membership_days') {
            return 'string';
        }
        if ($this->getAttribute() ==  'entity_id') {
            return 'string';
        }
        if (!$customerAttribute)
            return parent::getInputType();

        switch ($customerAttribute->getFrontendInput()) {

            case 'boolean':
                return 'select';
            case 'text':
                return 'string';
            default :
                return $customerAttribute->getFrontendInput();
        }

    }

    public function getValueElement()
    {
        $element = parent::getValueElement();
        switch ($this->getInputType()) {
            case 'date':
                $element->setClass('hasDatepicker');
                break;
        }
        return $element;
    }

    public function getExplicitApply()
    {
        return ($this->getInputType() == 'date');
    }

    public function getValueElementType()
    {
        $customerAttribute = $this->_objectManager->get('Magento\Customer\Model\ResourceModel\Customer')->getAttribute($this->getAttribute());

        if ($this->getAttribute() ==  'membership_days') {
            return 'text';
        }
        if ($this->getAttribute() ==  'entity_id') {
            return 'text';
        }
        if (!$customerAttribute) {
            return parent::getValueElementType();
        }
        switch ($customerAttribute->getFrontendInput()) {
            case 'boolean':
                return 'select';
            default :
                return $customerAttribute->getFrontendInput();
        }
    }

    public function getValueSelectOptions()
    {
        $selectOptions = array();
        $attributeObject = $this->_objectManager->get('Magento\Customer\Model\ResourceModel\Customer')->getAttribute($this->getAttribute());

        if (is_object($attributeObject) && $attributeObject->usesSource() ) {
            if ($attributeObject->getFrontendInput() == 'multiselect') {
                $addEmptyOption = false;
            } else {
                $addEmptyOption = true;
            }
            $selectOptions = $attributeObject->getSource()->getAllOptions($addEmptyOption);
        }

        $key = 'value_select_options';

        if (!$this->hasData($key)) {
            $this->setData($key, $selectOptions);
        }

        return $this->getData($key);
    }

    /**
     * Validate Address Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $customer = $model;
        if (!$customer instanceof \Magento\Customer\Model\Customer) {
            $customer = $model->getQuote()->getCustomer();
            $attr = $this->getAttribute();

            $allAttr = $customer->__toArray();

            if ($attr == 'membership_days') {
                //$customer->setData($attr, $this->getMembership($customer->getCreatedAt()));
                $allAttr[$attr] = $this->getMembership($customer->getCreatedAt());
            }
            if ($attr != 'entity_id' && !array_key_exists($attr, $allAttr)){
                $address = $model->getQuote()->getBillingAddress();
                $allAttr[$attr] = $address->getData($attr);
            }
            $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->setData($allAttr);
        }
        return parent::validate($customer);
    }

    public function getMembership($created)
    {
        return round((time() - strtotime($created))  /60 / 60 /24);
    }
}

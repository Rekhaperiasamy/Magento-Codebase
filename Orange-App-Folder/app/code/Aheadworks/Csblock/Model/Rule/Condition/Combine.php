<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Model\Rule\Condition;

/**
 * Class Combine
 * @package Aheadworks\Csblock\Model\Rule\Condition
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{

    /**
     * @var \Aheadworks\Csblock\Model\Rule\Condition\Product\AttributesFactory
     */
    protected $_attributeFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Aheadworks\Csblock\Model\Rule\Condition\Product\AttributesFactory $attributesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Aheadworks\Csblock\Model\Rule\Condition\Product\AttributesFactory $attributesFactory,
        array $data = []
    ){
        parent::__construct($context, $data);
        $this->_attributeFactory = $attributesFactory;
        $this->setType('Aheadworks\Csblock\Model\Rule\Condition\Combine');
    }

    /**
     * Prepare child rules option list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = [
            ['value' => $this->getType(), 'label' => __('Conditions Combination')],
            $this->_attributeFactory->create()->getNewChildSelectOptions(),
        ];

        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }

    /**
     * @return array|mixed
     */
    public function getConditions()
    {
        if ($this->getData($this->getPrefix()) === null) {
            $this->setData($this->getPrefix(), []);
        }
        return $this->getData($this->getPrefix());
    }

    /**
     * @param array $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /** @var Product|Combine $condition */
            $condition->setAggregator($this->getAggregator());
            $condition->setTrue((bool)$this->getValue());
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
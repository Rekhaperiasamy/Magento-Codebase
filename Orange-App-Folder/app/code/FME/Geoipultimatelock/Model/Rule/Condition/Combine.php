<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Catalog Rule Combine Condition data model
 */

namespace FME\Geoipultimatelock\Model\Rule\Condition;

class Combine extends \Magento\Rule\Model\Condition\Combine
{

    /**
     * @var \FME\Geoipultimatelock\Model\Rule\Condition\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \FME\Geoipultimatelock\Model\Rule\Condition\ProductFactory $conditionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \FME\Geoipultimatelock\Model\Rule\Condition\ProductFactory $conditionFactory,
        array $data = array()
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        $this->_productFactory = $conditionFactory;
        parent::__construct($context, $data);
        $this->setType('FME\Geoipultimatelock\Model\Rule\Condition\Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->_productFactory->create()->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($productAttributes as $code => $label) {
            $attributes[] = array(
                'value' => 'FME\Geoipultimatelock\Model\Rule\Condition\Product|' . $code,
                'label' => $label,
            );
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            array(
            array(
                'value' => 'FME\Geoipultimatelock\Model\Rule\Condition\Combine',
                'label' => __('Conditions Combination'),
            ),
            array('label' => __('Product Attribute'), 'value' => $attributes)
                )
        );
        return $conditions;
    }

    /**
     * @param array $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /** @var Product|Combine $condition */
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }
}

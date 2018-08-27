<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model\Rule\Condition\Product;

use Colissimo\Rule\Model\Rule\Condition\Product as RuleConditionProduct;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Rule\Model\Condition\Combine as ModelConditionCombine;
use Magento\Rule\Model\Condition\Context;

/**
 * Class Combine
 */
class Combine extends ModelConditionCombine
{
    /**
     * @var RuleConditionProduct
     */
    protected $_ruleConditionProd;

    /**
     * @param Context $context
     * @param RuleConditionProduct $ruleConditionProduct
     * @param array $data
     */
    public function __construct(
        Context $context,
        RuleConditionProduct $ruleConditionProduct,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_ruleConditionProd = $ruleConditionProduct;
        $this->setType('Colissimo\Rule\Model\Rule\Condition\Product\Combine');
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->_ruleConditionProd->loadAttributeOptions()->getAttributeOption();
        $pAttributes = [];
        $iAttributes = [];
        foreach ($productAttributes as $code => $label) {
            if (strpos($code, 'quote_item_') === 0) {
                $iAttributes[] = [
                    'value' => 'Colissimo\Rule\Model\Rule\Condition\Product|' . $code,
                    'label' => $label,
                ];
            } else {
                $pAttributes[] = [
                    'value' => 'Colissimo\Rule\Model\Rule\Condition\Product|' . $code,
                    'label' => $label,
                ];
            }
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => 'Colissimo\Rule\Model\Rule\Condition\Product\Combine',
                    'label' => __('Conditions Combination'),
                ],
                ['label' => __('Cart Item Attribute'), 'value' => $iAttributes],
                ['label' => __('Product Attribute'), 'value' => $pAttributes]
            ]
        );
        return $conditions;
    }

    /**
     * Collect validated attributes
     *
     * @param Collection $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Amasty\RulesPro\Model\Rule\Condition;

class Total extends \Magento\SalesRule\Model\Rule\Condition\Product\Combine
{

    private $_passedRules = array();

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\SalesRule\Model\Rule\Condition\Product $ruleConditionProduct
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,

        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\SalesRule\Model\Rule\Condition\Product $ruleConditionProduct,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $ruleConditionProduct, $data);
        $this->setType('Amasty\RulesPro\Model\Rule\Condition\Total')->setValue(null);
    }

    /**
     * Load array
     *
     * @param array $arr
     * @param string $key
     * @return $this
     */
    public function loadArray($arr, $key = 'conditions')
    {
        $this->setAttribute($arr['attribute']);
        $this->setOperator($arr['operator']);
        parent::loadArray($arr, $key);
        return $this;
    }

    /**
     * Return as xml
     *
     * @param string $containerKey
     * @param string $itemKey
     * @return string
     */
    public function asXml($containerKey = 'conditions', $itemKey = 'condition')
    {
        $xml = '<attribute>' .
            $this->getAttribute() .
            '</attribute>' .
            '<operator>' .
            $this->getOperator() .
            '</operator>' .
            parent::asXml(
                $containerKey,
                $itemKey
            );
        return $xml;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'average_order_value' => __('Average Order Value'),
            'total_orders_amount' => __('Total Sales Amount'),
            'of_placed_orders'    => __('Number of Placed Orders'),
        ]);
        return $this;
    }

    /**
     * Load value options
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        return $this;
    }

    /**
     * Load operator options
     *
     * @return $this
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(
            [
                '==' => __('is'),
                '!=' => __('is not'),
                '>=' => __('equals or greater than'),
                '<=' => __('equals or less than'),
                '>' => __('greater than'),
                '<' => __('less than'),
                '()' => __('is one of'),
                '!()' => __('is not one of'),
            ]
        );
        return $this;
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        /*
        $productAttributes = $this->_ruleConditionProd->loadAttributeOptions()->getAttributeOption();
        $pAttributes = [];
        $iAttributes = [];
        foreach ($productAttributes as $code => $label) {
            if (strpos($code, 'quote_item_') === 0) {
                $iAttributes[] = [
                    'value' => 'Magento\SalesRule\Model\Rule\Condition\Product|' . $code,
                    'label' => $label,
                ];
            } else {
                $pAttributes[] = [
                    'value' => 'Magento\SalesRule\Model\Rule\Condition\Product|' . $code,
                    'label' => $label,
                ];
            }
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => 'Magento\SalesRule\Model\Rule\Condition\Product\Combine',
                    'label' => __('Conditions Combination'),
                ],
                ['label' => __('Cart Item Attribute'), 'value' => $iAttributes],
                ['label' => __('Product Attribute'), 'value' => $pAttributes]
            ]
        );
*/

        $conditions = [
            array('label' => __('Please choose condition'), 'value' => ''),
            array('label' => __('Order Status'), 'value' => 'Amasty\RulesPro\Model\Rule\Condition\Total\Status'),
            array('label' => __('Period after order was placed'), 'value' => 'Amasty\RulesPro\Model\Rule\Condition\Total\Period'),
        ];
        return $conditions;


    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Return as html
     *
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . __(
                "If %1 %2 %3 for a subselection of items in cart matching %4 of these conditions:",
                $this->getAttributeElement()->getHtml(),
                $this->getOperatorElement()->getHtml(),
                $this->getValueElement()->getHtml(),
                $this->getAggregatorElement()->getHtml()
            );
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * Validate
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $quote = $model;
        if (!$quote instanceof \Magento\Quote\Model\Quote) {
            $quote = $model->getQuote();
        }

        // order history conditions are valid for customers only, not for visitors.
        $id = $quote->getCustomerId();
        if (!$id) {
            return false;
        }

        $condArray = array();

        foreach ($this->getConditions() as $condObj) {
            if (!in_array( $condObj->getId(),$this->_passedRules  )){
                $this->_passedRules[] = $condObj->getId();
                $condArray[] = $condObj->validate($model);
            }
        }

        if ( empty($condArray) ){
            return $this->validateAttribute($model->getData($this->getAttribute()));
        }

        $fieldName = $this->getAttributeElement()->getValue();

        $v = $this->_objectManager->get('Amasty\RulesPro\Helper\Calculator')
            ->getSingleTotalField($id, $fieldName, $condArray, $this->getAggregator());

        return $this->validateAttribute($v);
    }
}




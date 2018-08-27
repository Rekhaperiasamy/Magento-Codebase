<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model\Rule\Condition;

use Colissimo\Rule\Model\Rule\Condition\Address;
use Magento\Rule\Model\Condition\Combine as ModelConditionCombine;
use Magento\Framework\Event\ManagerInterface;
use Magento\Rule\Model\Condition\Context;
use Magento\Framework\DataObject;

/**
 * Class Combine
 */
class Combine extends ModelConditionCombine
{
    /**
     * Core event manager proxy
     *
     * @var ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var Address
     */
    protected $_conditionAddress;

    /**
     * @param Context $context
     * @param ManagerInterface $eventManager
     * @param Address $conditionAddress
     * @param array $data
     */
    public function __construct(
        Context $context,
        ManagerInterface $eventManager,
        Address $conditionAddress,
        array $data = []
    ) {
        $this->_eventManager = $eventManager;
        $this->_conditionAddress = $conditionAddress;
        parent::__construct($context, $data);
        $this->setType('Colissimo\Rule\Model\Rule\Condition\Combine');
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $addressAttributes = $this->_conditionAddress->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Colissimo\Rule\Model\Rule\Condition\Address|' . $code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => 'Colissimo\Rule\Model\Rule\Condition\Product\Found',
                    'label' => __('Product attribute combination'),
                ],
                [
                    'value' => 'Colissimo\Rule\Model\Rule\Condition\Product\Subselect',
                    'label' => __('Products subselection')
                ],
                [
                    'value' => 'Colissimo\Rule\Model\Rule\Condition\Combine',
                    'label' => __('Conditions combination')
                ],
                ['label' => __('Cart Attribute'), 'value' => $attributes]
            ]
        );

        $additional = new DataObject();
        $this->_eventManager->dispatch('colissimo_rule_rule_condition_combine', ['additional' => $additional]);
        $additionalConditions = $additional->getConditions();
        if ($additionalConditions) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}

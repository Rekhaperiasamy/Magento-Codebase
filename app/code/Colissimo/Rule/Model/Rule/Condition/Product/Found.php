<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model\Rule\Condition\Product;

use Colissimo\Rule\Model\Rule\Condition\Product\Combine;
use Colissimo\Rule\Model\Rule\Condition\Product as RuleConditionProduct;
use Magento\Rule\Model\Condition\Context;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Found
 */
class Found extends Combine
{
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
        parent::__construct($context, $ruleConditionProduct, $data);
        $this->setType('Colissimo\Rule\Model\Rule\Condition\Product\Found');
    }

    /**
     * Load value options
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption([1 => __('FOUND'), 0 => __('NOT FOUND')]);
        return $this;
    }

    /**
     * Return as html
     *
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . __(
            "If an item is %1 in the cart with %2 of these conditions true:",
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
     * @param AbstractModel $model
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function validate(AbstractModel $model)
    {
        $all = $this->getAggregator() === 'all';
        $true = (bool)$this->getValue();
        $found = false;
        foreach ($model->getAllItems() as $item) {
            $found = $all;
            foreach ($this->getConditions() as $cond) {
                $validated = $cond->validate($item);
                if ($all && !$validated || !$all && $validated) {
                    $found = $validated;
                    break;
                }
            }
            if ($found && $true || !$true && $found) {
                break;
            }
        }
        // found an item and we're looking for existing one
        if ($found && $true) {
            return true;
        } elseif (!$found && !$true) {
            // not found and we're making sure it doesn't exist
            return true;
        }
        return false;
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */

namespace Amasty\Rules\Model;


class Rule extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Rules\Model\ResourceModel\Rule');
        $this->setIdFieldName('entity_id');
    }

    public function loadBySalesrule(\Magento\Rule\Model\AbstractModel $rule)
    {
        if ($amrulesRule = $rule->getData('amrules_rule'))
            return $amrulesRule;

        $amrulesRule = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Amasty\Rules\Model\Rule');

        $amrulesRule->load($rule->getId(), 'salesrule_id');

        $rule->setData('amrules_rule', $amrulesRule);

        return $amrulesRule;
    }
}

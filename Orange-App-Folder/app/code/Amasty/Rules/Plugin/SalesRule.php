<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


namespace Amasty\Rules\Plugin;

class SalesRule
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Rule
     *
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $_rule;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\SalesRule\Model\Rule $rule,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_coreRegistry = $registry;
        $this->_rule = $rule;
        $this->_objectManager = $objectManager;
    }

    public function afterSave(\Magento\SalesRule\Model\Rule $subject, $result)
    {
        $this->_coreRegistry->register('amrules_current_salesrule', $subject, true);
        return $result;
    }

    public function afterLoad($subject, $result)
    {
        $amrulesRule = $this->_objectManager->get('Amasty\Rules\Model\Rule');
        $amrulesRule->loadBySalesrule($subject);
        return $result;
    }
}

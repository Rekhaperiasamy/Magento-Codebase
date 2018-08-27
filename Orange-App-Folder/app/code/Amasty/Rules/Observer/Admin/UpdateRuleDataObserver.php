<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


namespace Amasty\Rules\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;

class UpdateRuleDataObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $salesrule = $this->_coreRegistry->registry('amrules_current_salesrule');

        if (!$salesrule)
            return;

        $amrulesData = $observer->getRequest()->getParam('amrulesrule');

        if ($salesrule->getId() && $amrulesData) {
            $amrulesRule = $this->_objectManager->create('Amasty\Rules\Model\Rule');

            $amrulesRule
                ->load($salesrule->getId(), 'salesrule_id')
                ->addData($amrulesData)
                ->setData('salesrule_id', $salesrule->getId())
                ->save()
            ;
        }
    }
}

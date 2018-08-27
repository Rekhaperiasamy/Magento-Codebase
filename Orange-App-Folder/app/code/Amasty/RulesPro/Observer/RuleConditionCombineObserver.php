<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */


namespace Amasty\RulesPro\Observer;

use Magento\Framework\Event\ObserverInterface;

class RuleConditionCombineObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getAdditional();
        $cond = $transport->getConditions();
        if (!is_array($cond)) {
            $cond = array();
        }

        $types = array(
            'Customer' => 'Customer attributes',
            'Orders'   => 'Purchases history',
        );
        foreach ($types as $typeCode => $typeLabel) {
            $condition = $this->_objectManager->get('Amasty\RulesPro\Model\Rule\Condition\\'.$typeCode);
            $conditionAttributes = $condition->loadAttributeOptions()->getAttributeOption();

            $attributes = array();
            foreach ($conditionAttributes as $code => $label) {
                $attributes[] = array(
                    'value' => 'Amasty\RulesPro\Model\Rule\Condition\\' . $typeCode . '|' . $code,
                    'label' => $label,
                );
            }
            $cond[] = array(
                'value' => $attributes,
                'label' => __($typeLabel),
            );
        }

        $cond[] = array(
            'value' => 'Amasty\RulesPro\Model\Rule\Condition\Total',
            'label' => __('Orders Subselection')
        );

        $transport->setConditions($cond);

        return $this;
    }
}

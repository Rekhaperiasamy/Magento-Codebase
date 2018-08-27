<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Model\SalesRule\Condition;

class Combine extends \Magento\SalesRule\Model\Rule\Condition\Combine
{
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\SalesRule\Model\Rule\Condition\Address $conditionAddress,
        array $data = []
    ) {

        parent::__construct($context, $eventManager, $conditionAddress, $data);

        $this->setType('Amasty\Acart\Model\SalesRule\Condition\Combine');
    }

    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();

        $conditions = array_merge_recursive($conditions, array(
            array(
                'value' => 'Amasty\Acart\Model\SalesRule\Condition\Carts',
                'label'=> __('Number of recovered cards this month')
            )
        ));

        return $conditions;
    }
}
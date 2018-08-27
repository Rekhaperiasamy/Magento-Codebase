<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */

namespace Amasty\Rules\Plugin;

use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Metadata\ValueProvider as SalesRuleValueProvider;

class ValueProvider
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Amasty\Rules\Helper\Data
     */
    private $rulesDataHelper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\Rules\Helper\Data $rulesDataHelper
    )
    {
        $this->_objectManager = $objectManager;
        $this->rulesDataHelper = $rulesDataHelper;
    }

    public function aroundGetMetadataValues(
        SalesRuleValueProvider $subject,
        \Closure $proceed,
        Rule $rule
    ) {
        $result = $proceed($rule);

        $actions = &$result['actions']['children']['simple_action']['arguments']['data']['config']['options'];
        
        //$ruleActions = $this->rulesDataHelper->getDiscountTypes();

        $actions = array_merge($actions, $this->rulesDataHelper->getDiscountTypes());

        $ampromoRule = $this->_objectManager->create('Amasty\Rules\Model\Rule');
        $ampromoRule->load($rule->getId(), 'salesrule_id');

        $result['actions']['children']['amrulesrule[eachm]']['arguments']['data']['config']['value']
            = $ampromoRule->getData('eachm');

        $result['actions']['children']['amrulesrule[priceselector]']['arguments']['data']['config']['value']
            = $ampromoRule->getData('priceselector');

        $result['actions']['children']['amrulesrule[promo_skus]']['arguments']['data']['config']['value']
            = $ampromoRule->getData('promo_skus');

        $result['actions']['children']['amrulesrule[promo_cats]']['arguments']['data']['config']['value']
            = $ampromoRule->getData('promo_cats');

        $result['actions']['children']['amrulesrule[nqty]']['arguments']['data']['config']['value']
            = $ampromoRule->getData('nqty');

        $result['actions']['children']['amrulesrule[skip_rule]']['arguments']['data']['config']['value']
            = $ampromoRule->getData('skip_rule');

        $result['actions']['children']['amrulesrule[max_discount]']['arguments']['data']['config']['value']
            = $ampromoRule->getData('max_discount');

        return $result;
    }
}

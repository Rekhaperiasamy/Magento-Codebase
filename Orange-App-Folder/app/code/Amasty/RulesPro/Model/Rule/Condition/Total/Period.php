<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */

namespace Amasty\RulesPro\Model\Rule\Condition\Total;

use Magento\Rule\Model\Condition as Condition;
/**
 * Product rule condition data model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Period extends \Magento\Rule\Model\Condition\AbstractCondition
{

    public function __construct(
        Condition\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function loadAttributeOptions()
    {
        $attributes = array(
            'period'    => __('Period after order was placed'),
        );

        $this->setAttributeOption($attributes);
        return $this;
    }

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '>=' => __('equals or less than'),
            '<=' => __('equals or greater than'),
            '>'  => __('less than'),
            '<'  => __('greater than'),
            '='  => __('is'),
        ));

        return $this;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function getInputType()
    {
        return 'numeric';
    }

    public function getValueElementType()
    {
        return 'text';
    }

    public function getValueSelectOptions()
    {
        $options = array();

        $key = 'value_select_options';
        if (!$this->hasData($key)) {
            $this->setData($key, $options);
        }
        return $this->getData($key);
    }

    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $v = min(16000, $this->getValue()); // on windows can work incorrect for very big values.

        $date = date("Y-m-d H:i:s", time() - $v * 24 * 3600);
        $result = array('date' => $this->getOperatorForValidate() . "'" . $date . "'");

        return $result;
    }
}

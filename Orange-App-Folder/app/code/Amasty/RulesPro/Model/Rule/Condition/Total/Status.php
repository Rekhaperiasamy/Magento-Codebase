<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */


namespace Amasty\RulesPro\Model\Rule\Condition\Total;
use Magento\Rule\Model\Condition\Context;

class Status extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data=[]
    ) {

        $this->setType('Amasty\RulesPro\Model\Condition\Total\Status')
            ->setValue(null);
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    public function loadAttributeOptions()
    {
        $statuses = $this->_objectManager->get('Magento\Sales\Model\Order\Status')->getResourceCollection()->getData();
        $options = $this->getAttributeOptions();
        foreach ($statuses as $status) {
            $options[$status['status']] = $status['label'];
        }

        $this->setAttributeOption($options);
        return $this;
    }

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '='  => __('is'),
            '<>' => __('is not'),
        ));

        return $this;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() .
            __( sprintf("Order Status %s %s", $this->getOperatorElement()->getHtml(), $this->getAttributeElement()->getHtml()
            ));
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $result = array('status' => $this->getOperatorForValidate() . "'" . $this->getAttributeElement()->getValue() . "'");
        return $result;
    }

}


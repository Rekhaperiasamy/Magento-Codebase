<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 18/11/2015
 * Time: 09:54
 */

namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Rule\Add\Tab;

class Coupon extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_template = 'rule/add/tab/coupon.phtml';

    protected $_saleRuleFactory;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        array $data = []
    ) {
        $this->_coreRegistry    = $registry;
        $this->_formFactory     = $formFactory;
        $this->_saleRuleFactory = $ruleFactory;
        parent::__construct($context, $registry, $formFactory);

    }//end __construct()


    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __('Coupon');

    }//end getTabLabel()


    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle()
    {
        return __('Coupon');

    }//end getTabTitle()


    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        return true;

    }//end canShowTab()


    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        return false;

    }//end isHidden()


    public function getEnableCoupon()
    {
        $data = $this->_coreRegistry->registry('rule_data');
        if (isset($data['enable_coupon'])) {
            return $data['enable_coupon'];
        }

        return 0;

    }//end getEnableCoupon()


    public function getIsSelect($value)
    {
        $selected = '';
        $data     = $this->_coreRegistry->registry('rule_data');
        if (isset($data['enable_coupon'])) {
            if ($value == $data['enable_coupon']) {
                return $selected = 'selected="selected"';
            }
        }

        return $selected;

    }//end getIsSelect()


    public function getPromotionId()
    {
        $data = $this->_coreRegistry->registry('rule_data');
        if (isset($data['promotion_rule_id'])) {
            return $data['promotion_rule_id'];
        }

        return '';

    }//end getPromotionId()


    public function getPromotionName()
    {
        $data = $this->_coreRegistry->registry('rule_data');
        if (isset($data['promotion_rule_id'])) {
            $rule = $this->_saleRuleFactory->create()->load($data['promotion_rule_id']);
            return $rule->getName();
        }

        return '';

    }//end getPromotionName()
}//end class

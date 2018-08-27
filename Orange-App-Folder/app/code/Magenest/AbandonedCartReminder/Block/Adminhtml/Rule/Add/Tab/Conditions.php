<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/09/2015
 * Time: 23:46
 */
namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Rule\Add\Tab;

class Conditions extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * Core registry
     *
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $_conditions;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        array $data = []
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_conditions       = $conditions;
        parent::__construct($context, $registry, $formFactory, $data);

    }//end __construct()


    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Conditions');

    }//end getTabLabel()


    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Conditions');

    }//end getTabTitle()


    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;

    }//end canShowTab()


    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;

    }//end isHidden()


    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_promo_catalog_rule');
        // $model= $this->_objectManager->create('Magento\SalesRule\Model\Rule');
        /*
            * @var \Magento\Framework\Data\Form $form
        */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $renderer = $this->_rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('sales_rule/promo_quote/newConditionHtml/form/rule_conditions_fieldset')
        );

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            [
             'legend' => __(
                 'Apply the rule only if the following conditions are met (leave blank for all products).'
             ),
            ]
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'conditions',
            'text',
            [
             'name'  => 'conditions',
             'label' => __('Conditions'),
             'title' => __('Conditions'),
            ]
        )->setRule(
            $model
        )->setRenderer(
            $this->_conditions
        );

        $followupEmailRule = $this->_coreRegistry->registry('current_fue_rule');

        if ($this->getRequest()->getParam('id')) {
            $editData = $followupEmailRule->getData();

            if ($editData['website_id']) {
                $editData['website_id'] = unserialize($editData['website_id']);
            }

            if ($editData['customer_group_id']) {
                $editData['customer_group_id'] = unserialize($editData['customer_group_id']);
            }

            $editData['id'] = $this->getRequest()->getParam('id');
            $form->setValues($editData);
        }

        $this->setForm($form);

        return parent::_prepareForm();

    }//end _prepareForm()
}//end class

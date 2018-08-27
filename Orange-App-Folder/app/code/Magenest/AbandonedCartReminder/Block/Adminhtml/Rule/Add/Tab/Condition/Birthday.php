<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 03/12/2015
 * Time: 13:44
 */
namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Rule\Add\Tab\Condition;

class Birthday extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{


    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __('Condition');

    }//end getTabLabel()


    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle()
    {
        return __('Condition');

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


    protected function _prepareForm()
    {

        /*
            * @var \Magento\Framework\Data\Form $form
        */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $followupEmailRule = $this->_coreRegistry->registry('current_fue_rule');

        if ($this->getRequest()->getParam('id')) {
            $editData = $followupEmailRule->getData();

            if ($editData['website_id']) {
                $editData['website_id'] = unserialize($editData['website_id']);
            }

            if ($editData['customer_group_id']) {
                $editData['customer_group_id'] = unserialize($editData['customer_group_id']);
            }

            $gender = '';
            if ($editData['conditions_serialized']) {
                $editData['condition'] = unserialize($editData['conditions_serialized']);
            }

            if (isset($editData['condition']['gender'])) {
                $gender = $editData['condition']['gender'];
            }

            $editData['id'] = $this->getRequest()->getParam('id');
        } else {
            $gender = '';
        }//end if
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Condition')]);

        $fieldset->addField(
            'condition_gender',
            'select',
            [
             'label'       => __('Gender'),
             'required'    => false,
             'name'        => 'condition[gender]',
             'data-role'   => 'attach-value',
             'data-action' => $gender,

             'values'      => array(
                               array(
                                'label' => __('Any'),
                                'value' => '',

                               ),                               array(
                                                                 'label' => __('Male'),
                                                                 'value' => '1',

                                                                ),                               array(
                                                                                                  'label' => __('Female'),
                                                                                                  'value' => '2',
                                                                                                 ),
                              ),
            ]
        );

        // $form->setUseContainer(true);
        $this->setForm($form);

        if ($this->getRequest()->getParam('id')) {
            $form->setValues($editData);
        }

        return parent::_prepareForm();

    }//end _prepareForm()
}//end class

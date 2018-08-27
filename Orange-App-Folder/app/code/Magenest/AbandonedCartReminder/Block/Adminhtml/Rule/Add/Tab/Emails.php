<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 30/09/2015
 * Time: 10:17
 */

namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Rule\Add\Tab;

use Magento\Config\Model\Config\Source\Email\Identity as EmailIdentity;

// use Magento\Config\Model\Config\Source\Email\Template as EmailTemplate;
use Magenest\AbandonedCartReminder\Model\System\Email\Template as EmailTemplate;
use Symfony\Component\Config\Definition\Exception\Exception;

class Emails extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * Core registry
     *
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $_rendererFieldset;

    protected $type;

    protected $_email;

    protected $_emailIdentity;

    protected $_emailTemplate;

    protected $__nameInLayout = "abandonedcartreminder_rule_new_tab_emails";


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magenest\AbandonedCartReminder\Block\Adminhtml\Rule\Add\Email $email,
        EmailIdentity $identity,
        EmailTemplate $emailTemplate,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        array $data = []
    ) {
        $this->_rendererFieldset = $rendererFieldset;

        $this->_rendererFieldset->setData('papaClass' , $this);

        $this->_email = $email;

        $this->_emailIdentity = $identity;

        $this->_emailTemplate = $emailTemplate;

        parent::__construct($context, $registry, $formFactory, $data);

    }//end __construct()


    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Email Chain');

    }//end getTabLabel()


    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Emails');

    }//end getTabTitle()


    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;

    }//end canShowTab()


    /**
     * @return array
     */
    public function getEmailTemplates()
    {
        $options = $this->_emailTemplate->toOptionArray();

        return $options;

    }//end getEmailTemplates()


    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;

    }//end isHidden()


    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model      = $this->_coreRegistry->registry('current_fue_rule');
        $this->type = $this->_coreRegistry->registry('type');

        $attachFiles = unserialize($model->getData('attached_files'));

        $followupEmailRule = $this->_coreRegistry->registry('current_fue_rule');

        /*
            * @var \Magento\Framework\Data\Form $form
        */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $uploadAttachUrl = $this->getUrl('abandonedcartreminder/rule/upload');

        $renderer = $this->_rendererFieldset->setTemplate(
            'Magenest_AbandonedCartReminder::rule/emailChain.phtml'
        )->setNewChildUrl(
            $this->getUrl('sales_rule/promo_quote/newConditionHtml/form/rule_conditions_fieldset')
        );
        $renderer->setData('uploadUrl', $uploadAttachUrl);

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

        $renderer->setData('type', $this->type);

        $renderer->setData('attached_files', $attachFiles);

        $renderer->setData('options', $this->getEmailTemplates());
        $fieldset->addField(
            'conditions',
            'text',
            [
             'name'  => 'conditions',
             'label' => __('Conditions'),
             'title' => __('Conditions'),
            ]
        )->setRenderer(
            $this->_email
        );

        $this->_email->type = $this->type;

        if ($this->getRequest()->getParam('id')) {
            $editData = $followupEmailRule->getData();
            $renderer->setData('emails', unserialize($followupEmailRule->getData('email_chain')));

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

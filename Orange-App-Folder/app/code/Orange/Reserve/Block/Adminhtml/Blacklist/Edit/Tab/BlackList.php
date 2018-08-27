<?php

namespace Orange\Reserve\Block\Adminhtml\Blacklist\Edit\Tab;

class BlackList extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Store\Model\System\Store $systemStore, array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm() {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('reserve_blacklist');
        $isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Black List')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }

        $fieldset->addField(
                'name', 'text', array(
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('name'),
                /* 'required' => true, */
                )
        );
        $fieldset->addField(
                'firstname', 'text', array(
            'name' => 'firstname',
            'label' => __('First Name'),
            'title' => __('first name'),
                /* 'required' => true, */
                )
        );
        $fieldset->addField(
                'ip_address', 'text', array(
            'name' => 'ip_address',
            'label' => __('IP Address'),
            'title' => __('ip address'),
                /* 'required' => true, */
                )
        );
        $fieldset->addField(
                'email_address', 'text', array(
            'name' => 'email_address',
            'label' => __('Email Id'),
            'title' => __('email id'),
                /* 'required' => true, */
                )
        );
        /* {{CedAddFormField}} */

        if (!$model->getId()) {
            $model->setData('status', $isElementDisabled ? '2' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel() {
        return __('Black List');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return __('Black List');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden() {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId) {
        return $this->_authorization->isAllowed($resourceId);
    }

}

<?php

namespace Orange\Priority\Block\Adminhtml\Priority\Edit\Tab;

class Orange extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {

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
        $model = $this->_coreRegistry->registry('family_priority');
        $isElementDisabled = false;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $groupOptions = $objectManager->get('\Magento\Customer\Model\ResourceModel\Group\Collection')->toOptionArray();
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Product Priority')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }


        $fieldset->addField(
                'family_name', 'text', array(
            'name' => 'family name',
            'label' => __('Family Name'),
            'title' => __('family name'),
            'required' => true
                )
        );
        $fieldset->addField(
                'priority', 'text', array(
            'name' => 'priority',
            'label' => __('Priority'),
            'title' => __('Priority'),
            'required' => true
                )
        );

         $fieldset->addField(
                'store_id', 'select', array(
            'name' => 'store_id',
            'label' => __('Store Id'),
            'title' => __('Store_id'),
            'values' => $this->_systemStore->getStoreValuesForForm(false, true),
            'required' => true,
                )
        );
        $fieldset->addField(
                'customer_group', 'select', array(
            'name' => 'customer_group',
            'label' => __('Customer Group'),
            'title' => __('Customer Group'),
            'values' => $groupOptions,
            'required' => true,
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
        return __('Orange');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return __('Orange');
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

<?php

namespace Orange\Reserve\Block\Adminhtml\Reserve\Edit\Tab;

class ReservedDetails extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {

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
        $model = $this->_coreRegistry->registry('reserve_reserve');
        $isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Reserved Details')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }

        $fieldset->addField(
                'customer_name', 'text', array(
            'name' => 'customer_name',
            'label' => __('customer name'),
            'title' => __('customer name'),
            'required' => true
                )
        );
        $fieldset->addField(
                'sku', 'text', array(
            'name' => 'sku',
            'label' => __('sku'),
            'title' => __('sku'),
            'required' => true
                )
        );
        $fieldset->addField(
                'shop_id', 'text', array(
            'name' => 'shop_id',
            'label' => __('shop id'),
            'title' => __('shop id'),
            'required' => true
                )
        );
        $fieldset->addField(
                'other_details', 'textarea', array(
            'name' => 'other_details',
            'label' => __('other_details'),
            'title' => __('other_details')
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
        return __('Reserved Details');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return __('Reserved Details');
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

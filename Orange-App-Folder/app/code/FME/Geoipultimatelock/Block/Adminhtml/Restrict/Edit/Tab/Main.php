<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Block\Adminhtml\Restrict\Edit\Tab;

/**
 * FME Geoipultimatelock index edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     *
     * @var \FME\Geoipultimatelock\Helper\Data $_geoipultimatelockHelper
     */
    protected $_geoipultimatelockHelper;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \FME\Geoipultimatelock\Helper\Data $_geoipultimatelockHelper,
        array $data = array()
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        $this->_geoipultimatelockHelper = $_geoipultimatelockHelper;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \FME\Geoipultimatelock\Model\Inex */
        $model = $this->_coreRegistry->registry('geoipultimatelock_restrict_data');
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('FME_Geoipultimatelock::restrict_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('restrict_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Main')));

        if ($model->getId()) {
            $fieldset->addField('blocked_id', 'hidden', array('name' => 'blocked_id'));
        }

       
        $fieldset->addField(
            'is_active', 'select', array(
            'label' => __('Status'),
            'title' => __('Status'),
            'name' => 'is_active',
            'required' => false,
            'options' => $model->getAvailableStatuses(),
            'disabled' => $isElementDisabled
            )
        );

        $fieldset->addField(
            'blocked_ip', 'textarea', array(
            'name' => 'blocked_ip',
            'label' => __('Block IP(s)'),
            'disabled' => $isElementDisabled,
            'note' => __('State IPs (comma separated only) here for restriction')
            )
        );
        
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Main');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Main');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}

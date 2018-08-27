<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Block\Adminhtml\Rule\Edit\Tab;

/**
 * FME Geoipultimatelock index edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     *
     * @var \FME\Geoipultimatelock\Helper\Data $_geoipultimatelockHelper
     */
    protected $_geoipultimatelockHelper;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \FME\Geoipultimatelock\Helper\Data $_geoipultimatelockHelper,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = array()
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        $this->_geoipultimatelockHelper = $_geoipultimatelockHelper;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
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
        $model = $this->_coreRegistry->registry('geoipultimatelock_data');
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('FME_Geoipultimatelock::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Main')));

        if ($model->getId()) {
            $fieldset->addField('geoipultimatelock_id', 'hidden', array('name' => 'geoipultimatelock_id'));
        }

        $fieldset->addField(
            'title', 'text', array(
            'name' => 'title',
            'label' => __('Title'),
            'title' => __('Title'),
            'required' => true,
            'disabled' => $isElementDisabled
            )
        );

        $fieldset->addField(
            'priority', 'text', array(
            'name' => 'priority',
            'label' => __('Priority'),
            'title' => __('Priority'),
            'required' => true,
            'class' => 'validate-number',
            'disabled' => $isElementDisabled,
            'style' => 'width:50px;'
            )
        );

//        $fieldset->addField(
//            'test',
//            'radios',
//            [
//                'label' => __('test'),
//                'title' => __('test'),
//                'name' => 'test',
//                'required' => true,
//                'values' => array(
//                            array('value'=>'1','label'=>'Radio1'),
//                            array('value'=>'2','label'=>'Radio2'),
//                            array('value'=>'3','label'=>'Radio3'),
//                       ),
//            ]
//        );
        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                array(
                'name' => 'stores[]',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                'disabled' => $isElementDisabled
                    )
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                array('name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId())
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $pageValues = $this->_geoipultimatelockHelper->getCmsPageModel()
                ->getCollection()
                ->addFieldToFilter('is_active', 1)
                ->toOptionIdArray();
        
        $fieldset->addField(
            'cms_page_ids', 'multiselect', array(
            'label' => __('CMS Page'),
            'title' => __('CMS Page'),
            'name' => 'cms_page_ids',
            'required' => false,
            'values' => $pageValues,
            'disabled' => $isElementDisabled
            )
        );
        
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
            'exception_ips', 'textarea', array(
            'name' => 'exception_ips',
            'label' => __('Exceptions'),
            'disabled' => $isElementDisabled,
            'note' => __('State IPs (comma separated only) here for exception')
            )
        );
        
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(array('tab_id' => $this->getTabId()));

        $fieldset->addField(
            'error_url_redirect', 'text', array(
            'name' => 'error_url_redirect',
            'label' => __('Error URL Redirect'),
            'title' => __('Error URL Redirect'),
            'required' => false,
            'class' => 'validate-url',
            'disabled' => $isElementDisabled,
            'note' => _('You can also redirect visitor to a URL. This will have priority over Error Message')
            )
        );
        
        $fieldset->addField(
            'error_msg', 'editor', array(
            'name' => 'error_msg',
            'label' => __('Error Messgae'),
            'style' => 'width:100%;resize:vertical;',
            'required' => true,
            'disabled' => $isElementDisabled,
            'config' => $wysiwygConfig
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

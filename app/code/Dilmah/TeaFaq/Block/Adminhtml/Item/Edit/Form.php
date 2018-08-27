<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\TeaFaq\Block\Adminhtml\Item\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Dilmah\TeaFaq\Model\ResourceModel\Category\Collection;

/**
 * Class Form
 *
 * @package Dilmah\TeaFaq\Block\Adminhtml\Category\Edit
 */
class Form extends Generic
{
    /**
     * System Store
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * Wysiwyg Config
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * Category Collection
     * @var \Dilmah\TeaFaq\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollection;


    /**
     * Constructor
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param Config $wysiwygConfig
     * @param Collection $categoryCollection
     * @param []           $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Config $wysiwygConfig,
        Collection $categoryCollection,
        $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->categoryCollection = $categoryCollection;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init class
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('faqItemForm');
        $this->setTitle(__('FAQ Item Information'));
        $this->setUseContainer(true);
    }

    /**
     * PrepareForm
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_item');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'method' => 'post']]
        );

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('FAQ Information')]);

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'class' => 'required-entry',
                'required' => true
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, true),
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $options = $this->categoryCollection->getLabels();
        array_unshift($options, ['value' => '', 'label' => '-- Please Select --']);


        $fieldset->addField(
            'category_id',
            'select',
            [
                'name' => 'category_id',
                'label' => __('Category'),
                'required' => true,
                'values' => $options,
            ]
        );

        $fieldset->addField(
            'content',
            'editor',
            [
                'name' => 'content',
                'label' => __('Content'),
                'style' => 'height:36em;',
                'required' => true,
                'config' => $this->wysiwygConfig->getConfig()
            ]
        );


        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => ['1' => __('Active'), '0' => __('Inactive')]
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'class' => 'validate-not-negative-number',
                'required' => false
            ]
        );

        if ($model && $model->getId()) {
            $form->setValues($model);
            $fieldset->addField('item_id', 'hidden', ['name' => 'item_id', 'value' => $model->getItemId()]);
        }
        $form->setAction($this->getUrl('dtfaq/item/save'));
        $form->setUseContainer($this->getUseContainer());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

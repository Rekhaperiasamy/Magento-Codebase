<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Afeature
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Afeature\Block\Adminhtml\Item\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

/**
 * Class Form.
 */
class Form extends Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * Form constructor.
     *
     * @param Context     $context
     * @param Registry    $registry
     * @param FormFactory $formFactory
     * @param Store       $systemStore
     * @param array       $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init class.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('afeatureItemForm');
        $this->setTitle(__('Item Information'));
        $this->setUseContainer(true);
    }

    /**
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
            [
                'data' => [
                    'id' => 'edit_form',
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Banner Information')]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        /*
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

        $fieldset->addField(
            'desktop_image_url',
            'image',
            [
                'label' => __('Desktop Image'),
                'name' => 'desktop_image_url',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'tablet_image_url',
            'image',
            [
                'label' => __('Tablet Image'),
                'name' => 'tablet_image_url',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'mobile_image_url',
            'image',
            [
                'label' => __('Mobile Image'),
                'name' => 'mobile_image_url',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'alt',
            'text',
            [
                'name' => 'alt',
                'label' => __('Alt Text'),
                'class' => '',
                'required' => false,
            ]
        );

        $fieldset->addField(
            'has_two_sections',
            'select',
            [
                'label' => __('Has Two Sections'),
                'name' => 'has_two_sections',
                'required' => true,
                'options' => ['1' => __('Yes'), '0' => __('No')],
            ]
        );

        $fieldset->addField(
            'url',
            'text',
            [
                'name' => 'url',
                'label' => __('Banner URL'),
                'class' => '',
                'required' => false,
            ]
        );

        $fieldset->addField(
            'url2',
            'text',
            [
                'name' => 'url2',
                'label' => __('Banner URL (Right Section)'),
                'class' => '',
                'required' => false,
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'class' => 'validate-not-negative-number',
                'required' => false,
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => ['1' => __('Active'), '0' => __('Inactive')],
            ]
        );

        if ($model && $model->getId()) {
            $form->setValues($model);
            $fieldset->addField('item_id', 'hidden', ['name' => 'item_id', 'value' => $model->getItemId()]);
        }

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                'has_two_sections',
                'has_two_sections'
            )->addFieldMap(
                'url2',
                'url2'
            )->addFieldDependence(
                'url2',
                'has_two_sections',
                '1'
            )
        );

        $form->setAction($this->getUrl('dilmah_afeature/item/save'));
        $form->setUseContainer($this->getUseContainer());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

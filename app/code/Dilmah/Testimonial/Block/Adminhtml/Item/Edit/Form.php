<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Testimonial
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Testimonial\Block\Adminhtml\Item\Edit;

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

        $this->setId('testimonialtemForm');
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
                ],
            ]
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Item Information')]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'location',
            'text',
            [
                'label' => __('Location'),
                'name' => 'location',
                'class' => '',
                'required' => false,
            ]
        );

        $fieldset->addField(
            'date',
            'date',
            [
                'label' => __('Date'),
                'name' => 'date',
                'class' => 'required-entry',
                'required' => true,
                'date_format' => $this->_localeDate->getDateFormat(
                    \IntlDateFormatter::MEDIUM
                ),
            ]
        );

        $fieldset->addField(
            'message',
            'textarea',
            [
                'label' => __('Message'),
                'name' => 'message',
                'class' => 'required-entry',
                'required' => true,
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
                'has_text',
                'has_text'
            )->addFieldMap(
                'html',
                'html'
            )->addFieldDependence(
                'html',
                'has_text',
                '1'
            )
        );

        $form->setAction($this->getUrl('dilmah_testimonial/item/save'));
        $form->setUseContainer($this->getUseContainer());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

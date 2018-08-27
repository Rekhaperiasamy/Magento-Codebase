<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Block\Adminhtml\Csblock;

/**
 * Class Edit
 * @package Aheadworks\Csblock\Block\Adminhtml\Csblock
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {

        $this->_objectId = 'id';
        $this->_blockGroup = 'Aheadworks_Csblock';
        $this->_controller = 'adminhtml_csblock';
        $this->setId('csblock_edit');
        parent::_construct();

        /* @var $model \Aheadworks\Csblock\Model\Csblock */
        $model = $this->_coreRegistry->registry('aw_csblock_model');
        if ($model && $model->getId()) {
            $this->buttonList->update('save', 'class_name', 'Magento\Backend\Block\Widget\Button\SplitButton');
            $this->buttonList->update('save', 'options', $this->_getSaveButtonOptions());
        }
        $this->buttonList->add(
            'save_and_continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ]
            ],
            0
        );
    }

    protected function _getSaveButtonOptions()
    {
        return [
            [
                'label' => __('Save'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#edit_form']],
                ],
                'default' => true
            ],
            [
                'label' => __('Save as New'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndNew', 'target' => '#edit_form']],
                ],
                'default' => false
            ]
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/csblock/');
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/csblock/delete', ['_current' => true]);
    }
}

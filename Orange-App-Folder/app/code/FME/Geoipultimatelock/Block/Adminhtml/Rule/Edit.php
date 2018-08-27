<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Block\Adminhtml\Rule;

/**
 * Admin CMS page
 *
 * @author     Magento Core Team <core@magentocommerce.com>
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
        array $data = array()
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'geoipultimatelock_id';
        $this->_blockGroup = 'FME_Geoipultimatelock';
        $this->_controller = 'adminhtml_rule';

        parent::_construct();

        if ($this->_isAllowedAction('FME_Geoipultimatelock::save')) {
            $this->buttonList->update('save', 'label', __('Save'));
            $this->buttonList->add(
                'saveandcontinue',
                array(
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                    ),
                )
                    ),
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('FME_Geoipultimatelock::delete')) {
            $this->buttonList->update('delete', 'label', __('Delete'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('geoipultimatelock_data')->getId()) {
            return __("Edit '%1'", $this->escapeHtml($this->_coreRegistry->registry('geoipultimatelock_data')->getTitle()));
        } else {
            return __('New');
        }
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

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('geoipultimatelock/*/save', array('_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}'));
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('error_msg') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'error_msg');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'error_msg');
                }
            };
        ";
        return parent::_prepareLayout();
    }
}

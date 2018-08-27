<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Block\Adminhtml;

/**
 * Adminhtml Geoipultimatelock pages content block
 */
class Rule extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Block constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_rule';
        $this->_blockGroup = 'FME_Geoipultimatelock';
        $this->_headerText = __('Manage Rule');

        parent::_construct();

        if ($this->_isAllowedAction('FME_Geoipultimatelock::save')) {
            $this->buttonList->update('add', 'label', __('Add New Rule'));
        } else {
            $this->buttonList->remove('add');
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
}

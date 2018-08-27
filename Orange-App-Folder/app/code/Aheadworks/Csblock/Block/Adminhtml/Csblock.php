<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Block\Adminhtml;

class Csblock extends \Magento\Backend\Block\Widget\Grid\Container
{

    public function _construct()
    {
        $this->_controller = 'admin_csblock';
        $this->_blockGroup = 'Aheadworks_Csblock';
        $this->_headerText = __('Manage Blocks');
        $this->_addButtonLabel = __('Create New Block');
        parent::_construct();
    }
}

<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Block\Adminhtml\Colissimo;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Rule
 */
class Rule extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'colissimo_rule';
        $this->_headerText = __('Colissimo Rules');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();
    }
}

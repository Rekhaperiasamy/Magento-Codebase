<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\TeaFaq\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Category
 *
 * @package Dilmah\TeaFaq\Block\Adminhtml
 */
class Item extends Container
{
    /**
     * Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_item';
        $this->_blockGroup = 'Dilmah_TeaFaq';
        $this->_headerText = __('Manage FAQ Items');
        $this->_addButtonLabel = __('Add New FAQ Item');
        parent::_construct();
    }
}

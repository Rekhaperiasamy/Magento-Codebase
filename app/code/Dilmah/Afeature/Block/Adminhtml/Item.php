<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Afeature
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Afeature\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Item.
 */
class Item extends Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_item';
        $this->_blockGroup = 'Dilmah_Afeature';
        $this->_headerText = __('Manage Items');
        $this->_addButtonLabel = __('Add New Banner');
        parent::_construct();
    }
}

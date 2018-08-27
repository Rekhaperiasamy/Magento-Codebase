<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Testimonial
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Testimonial\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Item.
 */
class Item extends Container
{
    /**
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_item';
        $this->_blockGroup = 'Dilmah_Testimonial';
        $this->_headerText = __('Manage Items');
        $this->_addButtonLabel = __('Add New Item');
        parent::_construct();
    }
}

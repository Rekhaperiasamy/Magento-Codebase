<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Testimonial
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Testimonial\Block\Adminhtml\Item;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class Edit.
 */
class Edit extends Container
{
    /**
     * Init class.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'item_id';
        $this->_controller = 'adminhtml_item';
        $this->_blockGroup = 'Dilmah_Testimonial';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Item'));
        $this->buttonList->update('delete', 'label', __('Delete Item'));

        $this->buttonList->add(
            'save_and_continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ],
            ],
            10
        );
    }
}

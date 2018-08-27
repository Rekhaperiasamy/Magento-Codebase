<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\TeaFaq\Block\Adminhtml\Item;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class Edit
 *
 * @package Dilmah\TeaFaq\Block\Adminhtml\Category
 */
class Edit extends Container
{
    /**
     * Doc
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param []                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Init class
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'item_id';
        $this->_controller = 'adminhtml_item';
        $this->_blockGroup = 'Dilmah_TeaFaq';

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
                ]
            ],
            10
        );
    }
}

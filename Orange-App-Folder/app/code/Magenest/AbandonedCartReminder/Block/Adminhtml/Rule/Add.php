<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/09/2015
 * Time: 23:27
 */

namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Rule;

class Add extends \Magento\Backend\Block\Widget\Form\Container
{


    /**
     * Add button save
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Magenest_AbandonedCartReminder';
        $this->_controller = 'adminhtml_rule';
        $this->_mode       = 'add';

        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->update('save', 'id', 'save_button');

        $this->buttonList->update('reset', 'id', 'reset_button');

    }//end _construct()


    /**
     * Get add new review header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('New Rule');

    }//end getHeaderText()
}//end class

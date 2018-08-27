<?php
namespace Orange\OutofstockReminder\Block\Adminhtml\OutofstockReminder\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_outofstockreminder_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('OutofstockReminder Information'));
    }
}
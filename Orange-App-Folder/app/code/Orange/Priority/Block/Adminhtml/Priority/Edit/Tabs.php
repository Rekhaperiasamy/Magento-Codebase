<?php
namespace Orange\Priority\Block\Adminhtml\Priority\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_priority_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Priority Information'));
    }
}
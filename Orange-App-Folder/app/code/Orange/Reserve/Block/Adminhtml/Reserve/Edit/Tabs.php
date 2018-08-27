<?php
namespace Orange\Reserve\Block\Adminhtml\Reserve\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_reserve_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Reserve Information'));
    }
}
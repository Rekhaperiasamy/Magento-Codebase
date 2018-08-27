<?php
namespace Orange\Reserve\Block\Adminhtml\Blacklist\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_blacklist_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Blacklist Information'));
    }
}
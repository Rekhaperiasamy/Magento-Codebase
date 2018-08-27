<?php
namespace Orange\Errormessage\Block\Adminhtml\Errormessage\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_errormessage_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Error Message'));
    }
}
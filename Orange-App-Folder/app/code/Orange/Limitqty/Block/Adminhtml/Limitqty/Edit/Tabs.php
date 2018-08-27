<?php
namespace Orange\Limitqty\Block\Adminhtml\Limitqty\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_limitqty_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Limitqty Information'));
    }
}
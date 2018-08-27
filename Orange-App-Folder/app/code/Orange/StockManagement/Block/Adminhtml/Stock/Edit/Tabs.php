<?php
namespace Orange\StockManagement\Block\Adminhtml\Stock\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_stock_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Stock Information'));
    }
}
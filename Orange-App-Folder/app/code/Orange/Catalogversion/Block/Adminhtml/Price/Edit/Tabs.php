<?php
namespace Orange\Catalogversion\Block\Adminhtml\Price\Edit;
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		parent::_construct();
        $this->setId('checkmodule_pricerevison_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Product Price Revision Information'));
    }
}
?>
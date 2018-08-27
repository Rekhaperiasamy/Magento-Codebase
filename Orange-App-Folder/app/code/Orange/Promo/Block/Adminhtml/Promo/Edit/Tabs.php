<?php
namespace Orange\Promo\Block\Adminhtml\Promo\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_promo_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Promo Information'));
    }
}
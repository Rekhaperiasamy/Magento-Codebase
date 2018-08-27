<?php
namespace Orange\PromoDescription\Block\Adminhtml\PromotionDescription\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_promotiondescription_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('PromotionDescription Information'));
    }
}
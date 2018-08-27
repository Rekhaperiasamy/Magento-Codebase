<?php
namespace Orange\Scoringfield\Block\Adminhtml\Scoringresponse\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('scoringresponse_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Content Information'));
    }
}
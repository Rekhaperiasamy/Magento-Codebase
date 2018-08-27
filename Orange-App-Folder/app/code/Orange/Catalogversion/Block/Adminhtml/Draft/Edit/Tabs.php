<?php
namespace Orange\Catalogversion\Block\Adminhtml\draft\Edit;
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		parent::_construct();
        $this->setId('checkmodule_drafts_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Product drafts Information'));
    }
}
?>
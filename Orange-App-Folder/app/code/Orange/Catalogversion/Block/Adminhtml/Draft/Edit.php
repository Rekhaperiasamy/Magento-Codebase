<?php
namespace Orange\Catalogversion\Block\Adminhtml\draft;

/**
 * CMS block edit form container
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
		//die('asdasda');
		$this->_objectId = 'id';
        $this->_blockGroup = 'Orange_Catalogversion';
        $this->_controller = 'adminhtml_draft';
       
        //$this->buttonList->update('save', 'label', __('Save'));
       // $this->buttonList->update('delete', 'label', __('Delete'));
	   parent::_construct();
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('checkmodule_checkmodel')->getId()) {
            return __("Edit Item '%1'", $this->escapeHtml($this->_coreRegistry->registry('checkmodule_checkmodel')->getTitle()));
        } else {
            return __('New Item');
        }
    }
}

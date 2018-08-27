<?php
namespace Orange\Catalogversion\Block\Adminhtml\Price;

/**
 * CMS block edit form container
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
		//die('asdasda');
		$this->_objectId = 'edit_form_price';
        $this->_blockGroup = 'Orange_Catalogversion';
        $this->_controller = 'adminhtml_price';
       
        //
       // $this->buttonList->update('delete', 'label', __('Delete'));
	   parent::_construct();
	   $this->buttonList->update('save', 'label', __('Rollback'));
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
?>
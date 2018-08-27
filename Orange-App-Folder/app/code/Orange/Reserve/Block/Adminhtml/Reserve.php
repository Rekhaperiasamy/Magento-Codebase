<?php
namespace Orange\Reserve\Block\Adminhtml;
class Reserve extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_reserve';/*block grid.php directory*/
        $this->_blockGroup = 'Orange_Reserve';
        $this->_headerText = __('Reserve');
        $this->_addButtonLabel = __('Add New'); 
        parent::_construct();
		
    }
}

<?php
namespace Orange\Limitqty\Block\Adminhtml;
class Limitqty extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_limitqty';/*block grid.php directory*/
        $this->_blockGroup = 'Orange_Limitqty';
        $this->_headerText = __('Manage LOB Qty');
        $this->_addButtonLabel = __('Add New Limitqty'); 
        parent::_construct();
		
    }
}

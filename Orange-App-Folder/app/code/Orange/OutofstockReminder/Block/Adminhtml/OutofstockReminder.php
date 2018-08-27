<?php
namespace Orange\OutofstockReminder\Block\Adminhtml;
class OutofstockReminder extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_outofstockReminder';/*block grid.php directory*/
        $this->_blockGroup = 'Orange_OutofstockReminder';
        $this->_headerText = __('OutofstockReminder');
        $this->_addButtonLabel = __(''); 
        parent::_construct();
		
    }
}

<?php
namespace Orange\Errormessage\Block\Adminhtml;
class Errormessage extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_errormessage';/*block grid.php directory*/
        $this->_blockGroup = 'Orange_Errormessage';
        $this->_headerText = __('Errormessage');
        $this->_addButtonLabel = __('Add New Error'); 
        parent::_construct();
		
    }
}

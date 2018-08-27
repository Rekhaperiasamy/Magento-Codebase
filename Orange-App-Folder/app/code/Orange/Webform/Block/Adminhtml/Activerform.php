<?php
namespace Orange\Webform\Block\Adminhtml;
class Mnpform extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_mnpform';/*block grid.php directory*/
        $this->_blockGroup = 'Orange_Webform';
        $this->_headerText = __('Mnpform');
       // $this->_addButtonLabel = __('Import Stock'); 
        parent::_construct();
		
    }
}

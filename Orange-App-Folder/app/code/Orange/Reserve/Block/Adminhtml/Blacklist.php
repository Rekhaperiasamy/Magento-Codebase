<?php
namespace Orange\Reserve\Block\Adminhtml;
class Blacklist extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_blacklist';/*block grid.php directory*/
        $this->_blockGroup = 'Orange_Reserve';
        $this->_headerText = __('Blacklist');
        $this->_addButtonLabel = __('Add New Blacker Data'); 
        parent::_construct();
		
    }
}

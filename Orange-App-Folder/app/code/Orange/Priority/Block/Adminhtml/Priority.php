<?php
namespace Orange\Priority\Block\Adminhtml;
class Priority extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_priority';/*block grid.php directory*/
        $this->_blockGroup = 'Orange_Priority';
        $this->_headerText = __('Priority Managent');
        $this->_addButtonLabel = __('Add Priority'); 
        parent::_construct();
		
    }
}

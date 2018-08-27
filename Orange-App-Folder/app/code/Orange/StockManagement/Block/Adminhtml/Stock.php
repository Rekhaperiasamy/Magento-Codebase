<?php
namespace Orange\StockManagement\Block\Adminhtml;
class Stock extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_stock';/*block grid.php directory*/
        $this->_blockGroup = 'Orange_StockManagement';
        $this->_headerText = __('Stock');
        $this->_addButtonLabel = __('Import Stock'); 
        parent::_construct();
		
    }
}

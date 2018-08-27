<?php
namespace Orange\Promo\Block\Adminhtml;
class Promo extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_promo';/*block grid.php directory*/
        $this->_blockGroup = 'Orange_Promo';
        $this->_headerText = __('Promo');
        $this->_addButtonLabel = __('Add New Promo'); 
        parent::_construct();
		
    }
}

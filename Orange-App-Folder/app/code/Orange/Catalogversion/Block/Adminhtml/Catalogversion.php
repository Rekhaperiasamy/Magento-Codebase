<?php
namespace Orange\Catalogversion\Block\Adminhtml;
class Catalogversion extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_catalogversion';/*block grid.php directory*/
        $this->_blockGroup = 'Orange_Catalogversion';
        $this->_headerText = __('Catalogversion');
        parent::_construct();
		$this->removeButton('add');
		
    }
}

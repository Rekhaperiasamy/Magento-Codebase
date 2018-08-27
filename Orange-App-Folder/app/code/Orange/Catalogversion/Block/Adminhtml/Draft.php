<?php
namespace Orange\Catalogversion\Block\Adminhtml;
class Draft extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
	    //die('asdasdasdas draft block ');
        $this->_controller = 'adminhtml_draft';/*block grid.php directory*/
        $this->_blockGroup = 'Orange_Catalogversion';
        $this->_headerText = __('Catalogversion');
        //$this->_addButtonLabel = __(''); 
		parent::_construct();
		$this->removeButton('add');
    }
}
?>
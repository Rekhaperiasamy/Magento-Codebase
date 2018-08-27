<?php
namespace Orange\Scoringfield\Block\Adminhtml;
class Scoringresponse extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		$this->_controller = 'adminhtml_scoringresponse';
        $this->_blockGroup = 'Orange_Scoringfield';
        $this->_headerText = __('Ws Content Mapping');
        $this->_addButtonLabel = __('Add Content'); 
        parent::_construct();
		
    }
}

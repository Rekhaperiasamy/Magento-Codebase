<?php
namespace Orange\PromoDescription\Block\Adminhtml;
class PromotionDescription extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_promotionDescription';/*block grid.php directory*/
        $this->_blockGroup = 'Orange_PromoDescription';
        $this->_headerText = __('PromotionDescription');
        $this->_addButtonLabel = __('Add New Promo'); 
        parent::_construct();
		
    }
}

<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/09/2015
 * Time: 21:07
 */

namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Rule;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{


    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);

    }//end __construct()


    /**
     * set the mode edit
     */
    protected function _construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'Magenest_AbandonedCartReminder';
        $this->_controller = 'adminhtml_rule';
        $this->_mode       = 'edit';

        parent::_construct();

    }//end _construct()


    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Edit Rule');

    }//end getHeaderText()
}//end class

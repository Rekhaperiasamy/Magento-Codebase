<?php

namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Birthday;

class Birthday extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;

        parent::__construct($context, $data);

    }//end __construct()


    /**
     * Initialize add new review
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_addButtonLabel = __('Search');
        parent::_construct();

        $this->_blockGroup = 'Magenest_AbandonedCartReminder';
        $this->_controller = 'adminhtml_birthday';

        $this->_headerText = __('Follow up birthday');

    }//end _construct()
}//end class

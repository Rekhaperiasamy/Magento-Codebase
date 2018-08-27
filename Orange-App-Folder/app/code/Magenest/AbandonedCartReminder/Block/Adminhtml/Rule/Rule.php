<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/09/2015
 * Time: 23:11
 */

namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Rule;

class Rule extends \Magento\Backend\Block\Widget\Grid\Container
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
        $this->_addButtonLabel = __('New Rule');
        $emulateDaily          = $this->getUrl('abandonedcartreminder/emulate/daily', ['_current' => false]);
        $emulateHourly         = $this->getUrl('abandonedcartreminder/emulate/hourly', ['_current' => false]);

        $this->addButton(
            'emulateDailyCron',
            [
             'id'      => 'emulate-daily-cron',
             'label'   => __('Emulate Collecting'),
             'class'   => 'save primary save-category',
             'onclick' => "setLocation('{$emulateDaily}') ",
            ]
        );

        $this->addButton(
            'emulateSendMail',
            [
             'id'      => 'emulate-send-cron',
             'label'   => __('Emulate Sending'),
             'class'   => 'save primary save-category',
             'onclick' => "setLocation('{$emulateHourly}') ",
            ]
        );

        parent::_construct();

        $this->_blockGroup = 'Magenest_AbandonedCartReminder';
        $this->_controller = 'adminhtml_rule';

        $this->_headerText = __('Follow up email rules');

    }//end _construct()
}//end class

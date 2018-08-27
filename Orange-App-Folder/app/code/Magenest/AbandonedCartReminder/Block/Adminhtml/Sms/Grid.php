<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 27/06/2016
 * Time: 14:13
 */
namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Sms;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $smsFactory;
    /**
     * @param \Magento\Backend\Block\Template\Context           $context
     * @param \Magento\Backend\Helper\Data                      $backendHelper
     * @param \Magenest\AbandonedCartReminder\Model\MailFactory $mailFactory
     * @param \Magenest\AbandonedCartReminder\Model\Status      $status
     * @param \Magento\Framework\Module\Manager                 $moduleManager
     * @param array                                             $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magenest\AbandonedCartReminder\Model\SmsFactory $smsFactory,
        \Magenest\AbandonedCartReminder\Model\System\Config\Source\Mail\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->smsFactory  = $smsFactory;
        $this->_status       = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);

    }//end __construct()

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->smsFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;

    }//end _prepareCollection()


    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header'           => __('ID'),
                'type'             => 'number',
                'index'            => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'status',
            [
                'header'  => __('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => $this->_status->getOptionArray(),
            ]
        );
        $this->addColumn(
            'rule_id',
            [
                'header' => __('Rule ID'),
                'index'  => 'rule_id',
                'type'   => 'abc',
            ]
        );
        $this->addColumn(
            'recipient_name',
            [
                'header' => __('Recipient Name'),
                'index'  => 'recipient_name',
                'type'   => 'abc',
            ]
        );
        $this->addColumn(
            'recipient_mobile',
            [
                'header' => __('Recipient Mobile'),
                'index'  => 'recipient_mobile',
                'type'   => 'abc',
            ]
        );

        $this->addColumn(
            'scheduled_send_date',
            [
                'header' => __('Send At'),
                'index'  => 'scheduled_send_date',
                'type'   => 'datetime',
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Created at'),
                'index'  => 'created_at',
                'type'   => 'datetime',
            ]
        );

        $this->addColumn(
            'action',
            [
                'header'  => __('Action'),
                'type'    => 'action',
                'index'   => 'id',

                'actions' => [

                    [
                        'caption' => __('Send now'),
                        'url'     => array('base' => '*/sms/send'),
                        'field'   => 'id',
                    ] ,
                    [
                        'caption' => __('Cancel'),
                        'url'     => array('base' => '*/sms/cancel'),
                        'field'   => 'id',
                    ],
                ],

            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');

        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();

    }//end _prepareColumns()
}

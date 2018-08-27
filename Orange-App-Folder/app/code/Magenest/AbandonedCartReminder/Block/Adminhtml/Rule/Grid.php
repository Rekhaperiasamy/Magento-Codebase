<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/09/2015
 * Time: 23:19
 */

namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Rule;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    protected $_status;


    /**
     * @param \Magento\Backend\Block\Template\Context           $context
     * @param \Magento\Backend\Helper\Data                      $backendHelper
     * @param \Magenest\AbandonedCartReminder\Model\RuleFactory $ruleFactory
     * @param \Magenest\AbandonedCartReminder\Model\Status      $status
     * @param \Magento\Framework\Module\Manager                 $moduleManager
     * @param array                                             $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magenest\AbandonedCartReminder\Model\RuleFactory $ruleFactory,
        \Magenest\AbandonedCartReminder\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_ruleFactory  = $ruleFactory;
        $this->_status       = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);

    }//end __construct()


    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('post_filter');

    }//end _construct()


    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_ruleFactory->create()->getCollection();
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
            'type',
            [
             'header' => __('Type'),
             'index'  => 'type',
             'class'  => 'abc',
            ]
        );
        $this->addColumn(
            'name',
            [
             'header' => __('Name'),
             'index'  => 'name',
             'class'  => 'abc',
            ]
        );

        $this->addColumn(
            'from_date',
            [
             'header' => __('From'),
             'index'  => 'from_date',
             'type'   => 'datetime',
            ]
        );
        $this->addColumn(
            'to_date',
            [
             'header' => __('To'),
             'index'  => 'to_date',
             'type'   => 'datetime',
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

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();

    }//end _prepareColumns()


    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('rule');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
             'label'   => __('Delete'),
             'url'     => $this->getUrl('abandonedcartreminder/*/massDelete'),
             'confirm' => __('Are you sure?'),
            ]
        );
        $statuses = $this->_status->getOptionArray();
        $this->getMassactionBlock()->addItem(
            'status',
            [
             'label'      => __('Change status'),
             'url'        => $this->getUrl('abandonedcartreminder/*/massStatus', ['_current' => true]),
             'additional' => [
                              'visibility' => [
                                               'name'   => 'status',
                                               'type'   => 'select',
                                               'class'  => 'required-entry',
                                               'label'  => __('Status'),
                                               'values' => $statuses,
                                              ],
                             ],
            ]
        );

        return $this;

    }//end _prepareMassaction()


    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('abandonedcartreminder/*/grid', ['_current' => true]);

    }//end getGridUrl()


    /**
     * @param \Magenest\AbandonedCartReminder\Model\Rule|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'abandonedcartreminder/*/edit',
            [
             'id'   => $row->getId(),
             'type' => $row->getType(),
            ]
        );

    }//end getRowUrl()
}//end class

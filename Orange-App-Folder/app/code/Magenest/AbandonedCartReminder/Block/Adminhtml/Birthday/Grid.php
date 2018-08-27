<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/09/2015
 * Time: 23:19
 */

namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Birthday;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    protected $_status;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;


    /**
     * @param \Magento\Backend\Block\Template\Context               $context
     * @param \Magento\Backend\Helper\Data                          $backendHelper
     * @param \Magenest\AbandonedCartReminder\Model\BirthdayFactory $birthdayFactory
     * @param \Magenest\AbandonedCartReminder\Model\Status          $status
     * @param \Magento\Framework\Module\Manager                     $moduleManager
     * @param array                                                 $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magenest\AbandonedCartReminder\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
        $this->_status         = $status;
        $this->moduleManager   = $moduleManager;
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
        // birthday is in the format of m-d for exam 05-20 or 11-14
        $birthDay = $this->_request->getParam('birthday-search');

        // $birthdayObj  = new \DateTime($bd);
        // $sn = $birthdayObj->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        if (!$birthDay) {
            $collection = $this->customerFactory->create()->getCollection();
        } else {
            $collection = $this->customerFactory->create()->getCollection();

            $collection->getSelect()->where(
                'DATE_FORMAT(dob, "%m-%d")=?',
                $birthDay
            );
        }

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
            'entity_id',
            [
             'header'           => __('ID'),
             'type'             => 'number',
             'index'            => 'entity_id',
             'header_css_class' => 'col-id',
             'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'email',
            [
             'header' => __('Email'),
             'index'  => 'email',
             'class'  => 'abc',
            ]
        );
        $this->addColumn(
            'lastname',
            [
             'header' => __('LastName'),
             'index'  => 'lastname',
             'class'  => 'abc',
            ]
        );
        $this->addColumn(
            'dob',
            [
             'header' => __('Dob'),
             'index'  => 'dob',
             'type'   => 'date',
            ]
        );
        $this->addColumn(
            'is_active',
            [
             'header'  => __('Is Active'),
             'index'   => 'is_active',
             'type'    => 'options',
             'options' => $this->_status->getOptionArrayActive(),
            ]
        );
        $this->addColumn(
            'website_id',
            [
             'header' => __('Website'),
             'index'  => 'website_id',
             'type'   => 'abc',
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

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();

    }//end _prepareColumns()


    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('abandonedcartreminder/*/grid', ['_current' => true]);

    }//end getGridUrl()


    /**
     * @param \Magenest\AbandonedCartReminder\Model\Birthday|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'customer/index/edit',
            ['id' => $row->getId()]
        );

    }//end getRowUrl()
}//end class

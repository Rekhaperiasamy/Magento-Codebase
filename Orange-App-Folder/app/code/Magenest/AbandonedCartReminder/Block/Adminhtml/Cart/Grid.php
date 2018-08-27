<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 12/11/2015
 * Time: 00:44
 */

namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Cart;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $_abandonedCartCollection;

    /**
     * @var \Magento\Reports\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $_quotesFactory;

    protected $_status;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magenest\AbandonedCartReminder\Model\Resource\AbandonedCart\CollectionFactory $abandonedCartCollection,
        \Magenest\AbandonedCartReminder\Model\System\Config\Source\AbandonedCart\Status $status,
        \Magento\Reports\Model\ResourceModel\Quote\CollectionFactory $quotesFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry            = $coreRegistry;
        $this->_abandonedCartCollection = $abandonedCartCollection;
        $this->_quotesFactory           = $quotesFactory;
        $this->_status                  = $status;

        parent::__construct($context, $backendHelper, $data);

    }//end __construct()


    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('cartListGrid');
        $this->setUseAjax(true);

    }//end _construct()


    /**
     * Prepare collection for grid
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $priceRule = $this->_coreRegistry->registry('current_promo_quote_rule');

        /*
            * @var \Magento\SalesRule\Model\ResourceModel\Coupon\Collection $collection
         */
        $collection = $this->_abandonedCartCollection->create();

        $quoteTable = $collection->getResource()->getTable('quote');
		
		$collection->addFieldToFilter('is_deleted',array('neq' => 'yes'));

        $collection->getSelect()->join(array('q' => $quoteTable), 'q.entity_id = main_table.quote_id');

        $this->setCollection($collection);

        return parent::_prepareCollection();

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
            'customer_email',
            [
             'header'           => __('Customer Email'),
             'type'             => 'text',
             'index'            => 'customer_email',
             'header_css_class' => 'col-id',
             'column_css_class' => 'col-id',
            ]
        );$this->addColumn(
            'customer_email',
            [
             'header'           => __('Customer Email'),
             'type'             => 'text',
             'index'            => 'customer_email',
             'header_css_class' => 'col-id',
             'column_css_class' => 'col-id',
            ]
        );$this->addColumn(
            'customer_firstname',
            [
             'header'           => __('Customer First Name'),
             'type'             => 'text',
             'index'            => 'customer_firstname',
             'header_css_class' => 'col-id',
             'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'customer_lastname',
            [
             'header'           => __('Customer last name'),
             'type'             => 'text',
             'index'            => 'customer_lastname',
             'header_css_class' => 'col-id',
             'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'updated_at',
            [
             'header'           => __('Update At'),
             'type'             => 'text',
             'index'            => 'updated_at',
             'header_css_class' => 'col-id',
             'column_css_class' => 'col-id',
            ]
        );

      /*  $this->addColumn(
            'status',
            [
             'header'  => __('Status'),
             'index'   => 'status',
             'type'    => 'options',
             'options' => $this->_status->getOptionArray(),
            ]
        ); */

        return parent::_prepareColumns();

    }//end _prepareColumns()
}//end class

<?php
/**
 * @category   OPS
 * @package    Netresearch_OPS
 * @author     Thomas Birke <thomas.birke@netresearch.de>
 * @author     Michael Lühr <michael.luehr@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * @author     Thomas Birke <thomas.birke@netresearch.de>
 * @author     Michael Lühr <michael.luehr@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Alias extends \Magento\Backend\Block\Widget\Grid\Extended implements TabInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Netresearch\OPS\Model\ResourceModel\Alias\CollectionFactory
     */
    protected $aliasCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Netresearch\OPS\Model\ResourceModel\Alias\CollectionFactory $aliasCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Netresearch\OPS\Model\ResourceModel\Alias\CollectionFactory $aliasCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->registry = $registry;
        $this->aliasCollectionFactory = $aliasCollectionFactory;
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('customer_edit_tab_ops_alias');
        $this->setUseAjax(true);
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Payment Information');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Payment Information');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return (bool) $this->getCustomerId();
    }

    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Defines after which tab, this tab should be rendered
     *
     * @return string
     */
    public function getAfter()
    {
        return 'orders';
    }

    /*
     * Retrieves Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getAbsoluteGridUrl();
    }

    protected function _prepareCollection()
    {
        $collection = $this->aliasCollectionFactory->create()
            ->addFieldToFilter('customer_id', $this->getCustomerId());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('alias', [
            'header'        => __('Alias'),
            'align'         => 'right',
            'index'         => 'alias',
        ]);

        $this->addColumn('payment_method', [
            'header'    => __('Payment method'),
            'index'     => 'payment_method',
            'renderer'  => '\Netresearch\OPS\Block\Adminhtml\Customer\Renderer\PaymentMethod'
        ]);

        $this->addColumn('brand', [
            'header'    => __('Credit Card Type'),
            'index'     => 'brand',
        ]);

        $this->addColumn('pseudo_account_or_cc_no', [
            'header'    => __('Card Number/Account Number'),
            'index'     => 'pseudo_account_or_cc_no',
        ]);

        $this->addColumn('expiration_date', [
            'header'    => __('Expiration Date'),
            'index'     => 'expiration_date',
        ]);

        $this->addColumn('card_holder', [
            'header'    => __('Card Holder'),
            'index'     => 'card_holder',
        ]);

        $this->addColumn('state', [
            'header'    => __('State'),
            'index'     => 'state',
            'renderer'  => '\Netresearch\OPS\Block\Adminhtml\Customer\Renderer\State',
        ]);

        $this->addColumn('action', [
            'header'    =>  __('Action'),
            'width'     => '100',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => [
                [
                    'caption'   => __('Delete'),
                    'url'       => ['base' => 'adminhtml/alias/delete'],
                    'field'     => 'id'
                ]
            ],
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        ]);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return '#';
    }
}

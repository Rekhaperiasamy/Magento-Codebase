<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Block\Adminhtml\Rule;

/**
 * Adminhtml cms pages grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \FME\Geoipultimatelock\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \FME\Geoipultimatelock\Model\Rule
     */
    protected $_geoipultimatelockRule;

    /**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $pageLayoutBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \FME\Geoipultimatelock\Model\Rule $geoipultimatelockRule
     * @param \FME\Geoipultimatelock\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \FME\Geoipultimatelock\Model\Rule $geoipultimatelockRule,
        \FME\Geoipultimatelock\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = array()
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        $this->_collectionFactory = $collectionFactory;
        $this->_geoipultimatelockRule = $geoipultimatelockRule;
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('geoipultimatelockRuleGrid');
        $this->setDefaultSort('geoipultimatelock_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection \FME\Geoipultimatelock\Model\ResourceModel\Rule\Collection */
        $collection->setFirstStoreFlag(true);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('title', array('header' => __('Title'), 'index' => 'title'));

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                array(
                'header' => __('Store View'),
                'index' => 'store_id',
                'type' => 'store',
                'store_all' => true,
                'store_view' => true,
                'sortable' => false,
                'filter_condition_callback' => array($this, '_filterStoreCondition')
                    )
            );
        }

        $this->addColumn(
            'is_active',
            array(
            'header' => __('Status'),
            'index' => 'is_active',
            'type' => 'options',
            'options' => $this->_geoipultimatelockRule->getAvailableStatuses()
                )
        );

        $this->addColumn(
            'creation_time',
            array(
            'header' => __('Created'),
            'index' => 'creation_time',
            'type' => 'datetime',
            'header_css_class' => 'col-date',
            'column_css_class' => 'col-date'
                )
        );

        $this->addColumn(
            'update_time',
            array(
            'header' => __('Modified'),
            'index' => 'update_time',
            'type' => 'datetime',
            'header_css_class' => 'col-date',
            'column_css_class' => 'col-date'
                )
        );

        $this->addColumn(
            'rule_actions',
            array(
            'header' => __('Action'),
            'sortable' => false,
            'filter' => false,
            'renderer' => 'FME\Geoipultimatelock\Block\Adminhtml\Rule\Grid\Renderer\Action',
            'header_css_class' => 'col-action',
            'column_css_class' => 'col-action'
                )
        );

        return parent::_prepareColumns();
    }

    /**
     * After load collection
     *
     * @return void
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * Filter store condition
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Framework\DataObject $column
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _filterStoreCondition($collection, \Magento\Framework\DataObject $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('geoipultimatelock_id' => $row->getId()));
    }
}

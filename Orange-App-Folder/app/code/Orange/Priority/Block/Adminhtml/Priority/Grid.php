<?php

namespace Orange\Priority\Block\Adminhtml\Priority;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;
    protected $_collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Store\Model\System\Store $systemStore, \Magento\Backend\Helper\Data $backendHelper, \Magento\Store\Model\WebsiteFactory $websiteFactory, \Orange\Priority\Model\ResourceModel\Priority\Collection $collectionFactory, \Magento\Framework\Module\Manager $moduleManager, array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_collectionFactory = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct() {
        parent::_construct();

        $this->setId('productGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
    }

    /**
     * @return Store
     */
    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection() {
        try {
            $collection = $this->_collectionFactory->load();
            $this->setCollection($collection);

            parent::_prepareCollection();

            return $this;
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column) {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField(
                        'websites', 'catalog_product_website', 'website_id', 'product_id=entity_id', null, 'left'
                );
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    public function getCustomerGroup() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $groupOptions = $objectManager->get('\Magento\Customer\Model\ResourceModel\Group\Collection')->toOptionArray();
        $grp = array();
        foreach ($groupOptions as $key => $group) {
            $grp[$group['value']] = $group['label'];
        }
        return $grp;
    }

    public function getStoresData() {
        $storeData = $this->_systemStore->getStoreValuesForForm();
        $store = array();
        foreach ($storeData[1]['value'] as $storeDataValue) {
            $store[$storeDataValue['value']] = $storeDataValue['label'];
        }
		 $store[0] = "All Store View";
        return $store;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns() {


        $this->addColumn(
                'family_name', [
            'header' => __('Product Family Name'),
            'index' => 'family_name',
            'class' => 'family_name'
                ]
        );
        $this->addColumn(
                'priority', [
            'header' => __('Priority'),
            'index' => 'priority'
                ]
        );
        $this->addColumn(
                'store_id', [
            'header' => __('Store Id'),
            'index' => 'store_id',
            'type' => 'options',
            'options' => $this->getStoresData(),
                ]
        );

        $this->addColumn(
                'customer_group', [
            'header' => __('Customer Group'),
            'index' => 'customer_group',
            'type' => 'options',
            'options' => $this->getCustomerGroup(),
                ]
        );

        $this->addColumn(
                'created_at', [
            'header' => __('Created At'),
            'index' => 'created_at',
            'type' => 'datetime',
                ]
        );

        $this->addColumn(
                'updated_at', [
            'header' => __('Updated At'),
            'index' => 'updated_at',
            'type' => 'datetime',
                ]
        );
        /* {{CedAddGridColumn}} */

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
                'delete', array(
            'label' => __('Delete'),
            'url' => $this->getUrl('priority/*/massDelete'),
            'confirm' => __('Are you sure?')
                )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl() {
        return $this->getUrl('priority/*/index', ['_current' => true]);
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl(
                        'priority/*/edit', ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }

}

<?php
namespace Orange\Catalogversion\Block\Adminhtml\Catalogversion;


class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
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
	protected $_collectionFactory1;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

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
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
		\Orange\Catalogversion\Model\ResourceModel\Catalogversion\Collection $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
		
		$this->_collectionFactory1 = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
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
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
	  //die('hello prepare collection');
		try{
			$collection =$this->_collectionFactory1->load();
			//echo $collection->getSelect();
            $this->setCollection($collection);
            parent::_prepareCollection();
		    return $this;
		}
		catch(Exception $e)
		{
			echo $e->getMessage();die;
		}
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField(
                    'websites',
                    'catalog_product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left'
                );
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
            'productid',
            [
                'header' => __('ProductId'),
                'index' => 'productid',
                'class' => 'productid'
            ]
        );
		$this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',
                'class' => 'sku'
            ]
        );
		$this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'name'
            ]
        );
		$this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'index' => 'price',
                'class' => 'status'
            ]
        );
		$this->addColumn(
            'quantity',
            [
                'header' => __('Quantity'),
                'index' => 'quantity',
                'class' => 'status'
            ]
        );
		$this->addColumn(
            'status',
            [
                'header' => __('status'),
                'index' => 'status',
                'class' => 'status'
            ]
        );
		$this->addColumn(
            'revision_number',
            [
                'header' => __('Revison Number'),
                'index' => 'revision_number',
                'class' => 'revision_number'
            ]
        );
		
		$this->addColumn(
            'store_id',
            [
                'header' => __('Store ID'),
                'index' => 'store_id',
                'class' => 'store_id',
				'type' => 'options',
            'options' => $this->getStoresData(),
            ]
        );
	$this->addColumn(
            'created',
            [
                'header' => __('Created Date'),
                'index' => 'created',
                'class' => 'created'
            ]
        ); 
		 /* 
		$this->addColumn(
            'quantity',
            [
                'header' => __('quantity'),
                'index' => 'quantity',
                'class' => 'quantity'
            ]
        ); */
		/*{{CedAddGridColumn}}*/

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }
	
	 public function getStoresData() {      
        $store = array();       
		 $store[0] = "Default";
		 $store[1] = "Dutch";
		 $store[2] = "French";
        return $store;
    }

     /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => __('Delete'),
                'url' => $this->getUrl('catalogversion/*/massDelete'),
                'confirm' => __('Are you sure?')
            )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('catalogversion/*/index', ['_current' => true]);
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'catalogversion/*/edit',
            ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }
}

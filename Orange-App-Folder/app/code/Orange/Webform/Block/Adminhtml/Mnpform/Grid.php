<?php
namespace Orange\Webform\Block\Adminhtml\Mnpform;


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
	protected $_collectionFactory;

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
		\Orange\Webform\Model\ResourceModel\Mnpform\Collection $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
		
		$this->_collectionFactory = $collectionFactory;
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
		$this->setId('webform_mnpform_grid');
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
		try{
			
			
			$collection =$this->_collectionFactory->load();

		  

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
               /* $this->getCollection()->joinField(
                    'websites',
                    'catalog_product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left'
                );*/
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
		'create_date',
            [
                'header' => __('Create Date'),
                'index' => 'create_date',
                'type' => 'date',
            ]
        );
		$this->addColumn(
            'firstname',
            [
                'header' => __('firstname'),
                'index' => 'firstname',
                'class' => 'firstname'
            ]
        );
		$this->addColumn(
            'lastname',
            [
                'header' => __('lastname'),
                'index' => 'lastname',
                'class' => 'lastname'
            ]
        );
		$this->addColumn(
            'email',
            [
                'header' => __('lastname'),
                'index' => 'lastname',
                'class' => 'lastname'
            ]
        );
		$this->addColumn(
            'vatnumber',
            [
                'header' => __('vatnumber'),
                'index' => 'vatnumber',
                'class' => 'vatnumber'
            ]
        );
		$this->addColumn(
            'what_is_your_current_operator_make_your_choice',
            [
                'header' => __('what_is_your_current_operator_make_your_choice'),
                'index' => 'what_is_your_current_operator_make_your_choice',
                'class' => 'what_is_your_current_operator_make_your_choice'
            ]
        );
		$this->addColumn(
            'current_mobile_phone_number',
            [
                'header' => __('current_mobile_phone_number'),
                'index' => 'current_mobile_phone_number',
                'class' => 'current_mobile_phone_number'
            ]
        );
		$this->addColumn(
            'card_type',
            [
                'header' => __('Card Type'),
                'index' => 'card_type',
                'class' => 'card_type'
            ]
        );
		$this->addColumn(
            'email',
            [
                'header' => __('orange_mobile_phone_number'),
                'index' => 'orange_mobile_phone_number',
                'class' => 'orange_mobile_phone_number'
            ]
        );
		
		$this->addColumn(
            'email',
            [
                'header' => __('sim_card_number_orange'),
                'index' => 'sim_card_number_orange',
                'class' => 'sim_card_number_orange'
            ]
        );
			$this->addColumn(
            'email',
            [
                'header' => __('network_customer_number'),
                'index' => 'network_customer_number',
                'class' => 'network_customer_number'
            ]
        );
			$this->addColumn(
            'email',
            [
                'header' => __('bill_in_name'),
                'index' => 'bill_in_name',
                'class' => 'bill_in_name'
            ]
        );
			$this->addColumn(
            'email',
            [
                'header' => __('holders_name'),
                'index' => 'holders_name',
                'class' => 'holders_name'
            ]
        );
			$this->addColumn(
            'email',
            [
                'header' => __('holder_name'),
                'index' => 'holder_name',
                'class' => 'holder_name'
            ]
        );
		$this->addColumn(
            'do_you_want_to_receive_interesting_offers_and_the_latest_Orange',
            [
                'header' => __('do_you_want_to_receive_interesting_offers_and_the_latest_Orange'),
                'index' => 'do_you_want_to_receive_interesting_offers_and_the_latest_Orange',
                'class' => 'do_you_want_to_receive_interesting_offers_and_the_latest_Orange'
            ]
        );
		
		/*$this->addColumn(
            'valid_from',
            [
                'header' => __('valid_from'),
                'index' => 'valid_from',
                'type' => 'date',
            ]
        );*/
		/* $this->addColumn(
            'valid_to',
            [
                'header' => __('valid_to'),
                'index' => 'valid_to',
                'type' => 'date',
            ]
        ); */
		/*{{CedAddGridColumn}}*/

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

     /**
     * @return $this
     */
   /*  protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => __('Delete'),
                'url' => $this->getUrl('Webform/ * /massDelete'),
                'confirm' => __('Are you sure?')
            )
        );
        return $this;
    } */

    /**
     * @return string
     */
   /*  public function getGridUrl()
    {
        return $this->getUrl('Webform/ * /index', ['_current' => true]);
    } */

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $row
     * @return string
     */
   /*  public function getRowUrl($row)
    {
        return $this->getUrl(
            'Webform/ * /edit',
            ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    } */
}

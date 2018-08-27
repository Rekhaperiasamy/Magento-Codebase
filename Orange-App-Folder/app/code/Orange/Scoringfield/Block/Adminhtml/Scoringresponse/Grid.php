<?php

namespace Orange\Scoringfield\Block\Adminhtml\Scoringresponse;

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
    \Magento\Backend\Block\Template\Context $context, \Magento\Store\Model\System\Store $systemStore, \Magento\Backend\Helper\Data $backendHelper, \Magento\Store\Model\WebsiteFactory $websiteFactory, \Orange\Scoringfield\Model\ResourceModel\Scoringresponse\Collection $collectionFactory, \Magento\Framework\Module\Manager $moduleManager, array $data = []
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

        $this->setId('responseGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
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
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns() {


        $this->addColumn(
                'ws_response_content', [
            'header' => __('WS Response'),
            'index' => 'ws_response_content'
                ]
        );
		$this->addColumn(
                'content_fr', [
            'header' => __('Content For FR'),
            'index' => 'content_fr'
                ]
        );
        $this->addColumn(
                'content_nl', [
            'header' => __('Content For NL'),
            'index' => 'content_nl'
                ]
        );
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
            'url' => $this->getUrl('scoringfield/*/massdelete'),
            'confirm' => __('Are you sure?')
                )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl() {
        return $this->getUrl('scoringfield/*/index', ['_current' => true]);
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl(
                        'scoringfield/*/edit', ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }

}

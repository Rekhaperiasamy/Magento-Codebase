<?php
namespace Orange\Promo\Block\Adminhtml\Promo\Edit\Tab;
class PromoConfiguration extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
	protected $eavConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Eav\Model\Config $eavConfig,
		\Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
		$this->_wysiwygConfig = $wysiwygConfig;
		$this->eavConfig = $eavConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
		/* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('promodescription_promotiondescription');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$groupOptions = $objectManager->get('\Magento\Customer\Model\Customer\Attribute\Source\Group')->getAllOptions();
		$groupOptions = $objectManager->get('\Magento\Customer\Model\ResourceModel\Group\Collection')->toOptionArray();
		$allgroup = array();
		$comgroup = array();
		$allgroup['value']  = '-1';
		$allgroup['label']  = 'All';
		$comgroup[0] = $allgroup;
	    $lobgroupOptionsM = $this->eavConfig->getAttribute('catalog_product', 'meta_product_title');
		$lobgroupOptions = $lobgroupOptionsM->getSource()->getAllOptions();
		$groupOptions = array_merge ($comgroup,$groupOptions);
		//$groupOptions = array_unshift($groupOptions, $allgroup);
		
		$isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Promo Configuration')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }
         
		 
		$fieldset->addField(
            'family',
            'text',
            array(
                'name' => 'family',
                'label' => __('Family'),
                'title' => __('family'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'sku',
            'text',
            array(
                'name' => 'sku',
                'label' => __('SKU'),
                'title' => __('sku'),
                /*'required' => true,*/
            )
        );
      $fieldset->addField(
                'store_id', 'select', array(
            'name' => 'store_id',
            'label' => __('Store Id'),
            'title' => __('Store_id'),
            'values' => $this->getStoresData(),
            'required' => true,
                )
        );
        $fieldset->addField(
                'customer_group', 'select', array(
            'name' => 'customer_group',
            'label' => __('Customer Group'),
            'title' => __('Customer Group'),
            'values' => $groupOptions,
            'required' => true,
                )
        );
		 $fieldset->addField(
                'lob_product', 'select', array(
            'name' => 'lob_product',
            'label' => __('Product Type'),
            'title' => __('Product Type'),
            'values' => $lobgroupOptions,
                )
        );
     $fieldset->addField(
                'html_content', 'editor', array(
            'name' => 'html_content',
            'label' => __('Promo Description'),
            'title' => __('Content'),
            'style' => 'height:20em',
            'required' => true,
            'config' => $this->_wysiwygConfig->getConfig()
                )
        );
		/*{{CedAddFormField}}*/
        
        if (!$model->getId()) {
            $model->setData('status', $isElementDisabled ? '2' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();   
    }
	
	public function getStoresData() {
				$storeData = $this->_systemStore->getStoreValuesForForm();
				$store = array();
				$store[''] = "";
				foreach ($storeData[1]['value'] as $storeDataValue) {
					$store[$storeDataValue['value']] = $storeDataValue['label'];
				}
				return $store;
		}

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Promo Configuration');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Promo Configuration');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}

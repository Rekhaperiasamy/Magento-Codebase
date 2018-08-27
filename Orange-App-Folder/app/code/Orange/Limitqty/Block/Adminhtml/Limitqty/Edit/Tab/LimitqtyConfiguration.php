<?php
namespace Orange\Limitqty\Block\Adminhtml\Limitqty\Edit\Tab;
class LimitqtyConfiguration extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

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
		\Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
		$this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    public function getCustomerGroup() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $groupOptions = $objectManager->get('\Magento\Catalog\Model\Product\AttributeSet\Options')->toOptionArray();
		$grp = array();
		$grp[16] = "IEW NINTENDO";
		$grp[17] = "POSTPAID NINTENDO";
        foreach ($groupOptions as $key => $group) {
		    unset ($grp[4]);
			$grp[$group['value']] = strtoupper($group['label']);
        }
        return $grp;
    }
	protected function _prepareForm()
    {
		/* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('limitqty_limitqty');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Limitqty Configuration')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }

		
      $fieldset->addField(
                'store_id', 'select', array(
            'name' => 'store_id',
            'label' => __('Language'),
            'title' => __('Store_id'),
            'values' => $this->_systemStore->getStoreValuesForForm(false, true),
            'required' => true,
                )
        );
        $fieldset->addField(
                'customer_group', 'select', array(
            'name' => 'customer_group',
            'label' => __('Product Type'),
            'title' => __('Product Type'),
            'values' => $this->getCustomerGroup(),
            'required' => true,
                )
        );
		$fieldset->addField(
            'limitquantity',
            'text',
            array(
                'name' => 'limitquantity',
                'label' => __('Limit Quantity'),
                'title' => __('limitquantity'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'errormessage',
            'text',
            array(
                'name' => 'errormessage',
                'label' => __('Error Message'),
                'title' => __('errormessage'),
                /*'required' => true,*/
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

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Limitqty Configuration');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Limitqty Configuration');
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

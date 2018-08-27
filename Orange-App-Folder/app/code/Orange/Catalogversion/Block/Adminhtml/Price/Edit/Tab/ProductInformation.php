<?php
namespace Orange\Catalogversion\Block\Adminhtml\Price\Edit\Tab;
class ProductInformation extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
	 
	    $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {  
	    $model = $this->getModel();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        //$form->setHtmlIdPrefix('pricerevision_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'product_name',
            'text',
            ['name' => 'product_name', 'label' => __('Product Name'), 'title' => __('Product Name'), 'required' => true,'readonly' => true]
        );
        $fieldset->addField(
            'sku',
            'text',
            ['name' => 'name', 'label' => __('Sku'), 'title' => __('Product Name'), 'required' => true,'readonly' => true]
        );
		$fieldset->addField(
            'price',
            'text',
            ['name' => 'price', 'label' => __('Price'), 'title' => __('Price'), 'required' => true,'readonly' => true]
        );
		
        if (!$model->getId()) {
            $model->setData('is_enabled', '1');
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
        return __('Product Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
       // return __('Product Information');
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
	
	public function getModel()
    {
        return $this->_coreRegistry->registry('catalogversion_pricerevision');
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
?>
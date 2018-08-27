<?php
namespace Orange\OutofstockReminder\Block\Adminhtml\OutofstockReminder\Edit\Tab;
class OutofStockProductInformation extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
		/* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('outofstockreminder_outofstockreminder');
		$isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('OutofStock Product Information')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }

		$fieldset->addField(
            'name',
            'text',
            array(
                'name' => 'name',
                'label' => __('name'),
                'title' => __('name'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'lastname',
            'text',
            array(
                'name' => 'lastname',
                'label' => __('last name'),
                'title' => __('last name'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'email',
            'text',
            array(
                'name' => 'email',
                'label' => __('email'),
                'title' => __('email'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'title',
            'text',
            array(
                'name' => 'title',
                'label' => __('title'),
                'title' => __('title'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'status',
            'text',
            array(
                'name' => 'status',
                'label' => __('status'),
                'title' => __('status'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'lang',
            'text',
            array(
                'name' => 'lang',
                'label' => __('language'),
                'title' => __('language'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'product_name',
            'text',
            array(
                'name' => 'product_name',
                'label' => __('product name'),
                'title' => __('product name'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'product_url',
            'text',
            array(
                'name' => 'product_url',
                'label' => __('product url'),
                'title' => __('product url'),
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
        return __('OutofStock Product Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('OutofStock Product Information');
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
